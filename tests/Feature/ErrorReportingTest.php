<?php

namespace Tests\Feature;

use App\Exceptions\ScoringException;
use App\Helpers\RouteHelpers;
use App\Models\Competition;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\ResultSchema;
use App\Scoring\CompetitionResults;
use App\Models\User;
use App\Support\ErrorContext;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Sentry\ClientBuilder;
use Sentry\Event;
use Sentry\Integration\RequestFetcherInterface;
use Sentry\Integration\RequestIntegration;
use Sentry\Logs\Logs;
use Sentry\Options;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Sentry\State\Scope;
use Tests\TestCase;

/**
 * What reaches Sentry: errors the app used to hide, a limit on repeating ones, and no personal data.
 */
class ErrorReportingTest extends TestCase
{
    use RefreshDatabase;

    private function competition(): Competition
    {
        $comp = new Competition();
        $comp->name = 'Test Competition';
        $comp->when = now();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = true;
        $comp->use_seeds = true;
        $comp->public_results = true;
        $comp->save();

        return $comp;
    }

    /** @return array<string, string> */
    private function tags(?Request $request): array
    {
        $scope = new Scope();
        ErrorContext::applyTo($scope, $request);

        return $scope->applyToEvent(Event::createEvent())->getTags();
    }

    public function test_removing_or_appealing_a_dq_reports_a_failure_instead_of_hiding_it(): void
    {
        foreach (['dj.dq.remove', 'dj.dq.appeal'] as $route) {
            Exceptions::fake();

            // No violation to report the activity on, so handling it throws
            $submission = new JudgeDQSubmission();
            $submission->forceFill(['id' => 1]);
            Route::bind('submission', fn () => $submission);

            $this->withSession(['digitalJudgeClientHeadJudge' => true])
                ->post(route($route, 1))
                ->assertJson(['success' => false]);

            Exceptions::assertReported(\Error::class);
        }
    }

    public function test_a_public_sheet_that_fails_to_score_is_reported(): void
    {
        Exceptions::fake();

        $comp = $this->competition();

        $sheet = new ResultSchema();
        $sheet->forceFill(['name' => 'Overall', 'competition' => $comp->id, 'viewable' => true]);
        $sheet->schema = ['equation' => 'item.position'];
        $sheet->save();

        $this->mock(CompetitionResults::class)->shouldReceive('sheet')->andThrow(new ScoringException('Error evaluating formula'));

        $this->getJson(route('landing.competition.results.get.sheet', [$comp, $sheet]))
            ->assertStatus(403)
            ->assertJson(['error' => 'Results unavailable at this time, please try again later. (S_ERR)']);

        Exceptions::assertReported(ScoringException::class);
    }

    public function test_a_repeating_scoring_failure_is_reported_once_an_hour(): void
    {
        $handler = app(ExceptionHandler::class);

        $this->assertTrue($handler->shouldReport(new ScoringException('Broken formula')));
        $this->assertFalse($handler->shouldReport(new ScoringException('Broken formula')));
        $this->assertTrue($handler->shouldReport(new ScoringException('Another broken formula')));
    }

    public function test_other_errors_are_not_limited(): void
    {
        $handler = app(ExceptionHandler::class);

        $this->assertTrue($handler->shouldReport(new RuntimeException('Boom')));
        $this->assertTrue($handler->shouldReport(new RuntimeException('Boom')));
    }

    public function test_errors_are_tagged_with_where_they_happened(): void
    {
        $comp = $this->competition();

        $request = Request::create('http://' . RouteHelpers::domainRemap('judge.') . '/somewhere');
        $route = (new \Illuminate\Routing\Route('GET', 'somewhere', []))->bind($request);
        $route->setParameter('competition', $comp);
        $request->setRouteResolver(fn () => $route);

        $this->assertSame([
            'subdomain' => 'judge',
            'competition' => (string) $comp->id,
            'user_role' => 'guest',
        ], $this->tags($request));

        $this->assertSame(['subdomain' => 'console', 'user_role' => 'guest'], $this->tags(null));
    }

    public function test_a_signed_in_user_is_sent_as_an_id_only(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $scope = new Scope();
        ErrorContext::applyTo($scope, null);
        $event = $scope->applyToEvent(Event::createEvent());

        $this->assertSame('user', $event->getTags()['user_role']);
        $this->assertSame((string) $user->id, (string) $event->getUser()->getId());
        $this->assertNull($event->getUser()->getEmail());
        $this->assertNull($event->getUser()->getUsername());
    }

    public function test_events_leave_out_request_data(): void
    {
        $request = (new ServerRequest('POST', 'https://example.test/reset-password?email=someone@example.test', [
            'Cookie' => 'scoring_session=secret',
            'X-Secret' => 'secret',
        ], null, '1.1', ['REMOTE_ADDR' => '203.0.113.9']))
            ->withCookieParams(['scoring_session' => 'secret'])
            ->withQueryParams(['email' => 'someone@example.test'])
            ->withParsedBody(['name' => 'A Competitor']);

        $fetcher = new class($request) implements RequestFetcherInterface {
            public function __construct(private ServerRequestInterface $request)
            {
            }

            public function fetchRequest(): ServerRequestInterface
            {
                return $this->request;
            }
        };

        // setupOnce only ever runs for the first client, so it can't be given this request through a client
        $event = Event::createEvent();
        (new \ReflectionMethod(RequestIntegration::class, 'processEvent'))->invoke(
            new RequestIntegration($fetcher),
            $event,
            new Options(collect(config('sentry'))->only(['send_default_pii', 'data_collection'])->all()),
        );

        $sent = $event->getRequest();

        $this->assertSame('POST', $sent['method']);
        $this->assertStringEndsWith('/reset-password', $sent['url']);
        $this->assertStringNotContainsString('someone@example.test', json_encode($sent));
        $this->assertArrayNotHasKey('query_string', $sent);
        $this->assertArrayNotHasKey('data', $sent);
        $this->assertArrayNotHasKey('cookies', $sent);
        $this->assertEmpty($sent['headers'] ?? []);
        $this->assertNull($event->getUser()?->getIpAddress());
    }

    public function test_logs_reach_sentry_with_the_same_context_as_errors(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        SentrySdk::getCurrentHub()->bindClient(ClientBuilder::create([
            'dsn' => 'http://key@example.test/1',
            'enable_logs' => true,
            'before_send_log' => config('sentry.before_send_log'),
        ])->getClient());

        try {
            Log::channel('sentry_logs')->warning('Something handled', ['sheet.id' => 5]);

            $logs = Logs::getInstance()->aggregator()->all();
            $this->assertCount(1, $logs);
            $this->assertSame('Something handled', $logs[0]->getBody());

            $attributes = $logs[0]->attributes()->toSimpleArray();
            $this->assertSame(5, $attributes['sheet.id']);
            $this->assertSame('console', $attributes['subdomain']);
            $this->assertSame('user', $attributes['user_role']);
            $this->assertSame((string) $user->id, $attributes['user.id']);
        } finally {
            // A hub with no client empties the buffer without sending anything
            Logs::getInstance()->aggregator()->flush(new Hub());
            SentrySdk::init();
        }
    }

    public function test_sentry_logs_has_its_own_level(): void
    {
        $this->assertSame(config('sentry.logs_channel_level'), config('logging.channels.sentry_logs.level'));
    }
}
