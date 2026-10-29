<?php

namespace Tests\Feature;

use App\Models\Event\ScoringSchema;
use App\Models\Organisation\Organisation;
use App\Models\ResultSchemaTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * An organisation's Scoring (Svelte): its Scoring templates and Formulas, and making, editing and
 * deleting them.
 */
class OrganisationScoringTest extends TestCase
{
    use RefreshDatabase;

    private Organisation $org;

    private User $organiser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = $this->makeOrg('Cardiff ULSC');

        $this->organiser = User::factory()->create();
        $this->org->addAccount($this->organiser, 'admin');
    }

    private function makeOrg(string $name): Organisation
    {
        $org = new Organisation();
        $org->name = $name;
        $org->save();

        return $org;
    }

    private function makeTemplate(string $name, ?Organisation $org = null): ScoringSchema
    {
        $template = new ScoringSchema();
        $template->name = $name;
        $template->schema = ['equation' => 'item.result', 'global_variables' => [], 'rank_higher' => false];
        $template->organisation = ($org ?? $this->org)->id;
        $template->save();

        return $template;
    }

    private function makeFormula(string $name, ?Organisation $org = null): ResultSchemaTemplate
    {
        $formula = new ResultSchemaTemplate();
        $formula->name = $name;
        $formula->schema = ['equation' => 'item.points', 'global_variables' => [['name' => 'top', 'expression' => 'MAXIMUM(results, "points")', 'order' => 1]]];
        $formula->organisation = ($org ?? $this->org)->id;
        $formula->save();

        return $formula;
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl("/organisation/{$this->org->id}/scoring{$path}");
    }

    /** What the Scoring template editor posts */
    private function templateData(array $overrides = []): array
    {
        return [
            'name' => 'Rope throw',
            'equation' => 'item.result',
            'penalty_func' => '',
            'global_variables' => [],
            'local_variables' => [],
            'auto_penalties' => [],
            'auto_disqualifications' => [],
            'rank_higher' => true,
            'rank_equation' => '',
            'allow_disqualified_to_rank' => false,
            'group_by' => [],
            'force_penalty' => false,
            ...$overrides,
        ];
    }

    public function test_scoring_lists_its_templates_and_formulas(): void
    {
        $this->makeTemplate('Swim');
        $this->makeTemplate('Other', $this->makeOrg('Swansea'));
        $this->makeFormula('Points');

        $this->actingAs($this->organiser)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Scoring')
                ->where('organisation.tabs.4.label', 'Scoring')
                ->where('organisation.tabs.4.current', true)
                ->has('templates', 1)
                ->where('templates.0.name', 'Swim')
                ->has('formulas', 1)
                ->where('formulas.0.name', 'Points'));
    }

    public function test_new_scoring_template_opens_blank(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/ScoringTemplate')
                ->where('template', null)
                ->where('organisation.tabs.4.current', true));
    }

    public function test_making_a_scoring_template_lands_on_its_page(): void
    {
        $response = $this->actingAs($this->organiser)->post($this->url(), $this->templateData([
            'global_variables' => [['name' => 'best', 'expression' => 'MINIMUM(results, "result")', 'order' => 1]],
        ]));

        $template = ScoringSchema::where('organisation', $this->org->id)->sole();
        $response->assertRedirect(route('manage.organisations.scoring.templates.edit', [$this->org, $template]));
        $this->assertSame('Rope throw', $template->name);
        $this->assertSame('item.result', $template->schema['equation']);
        $this->assertSame('best', $template->schema['global_variables'][0]['name']);
    }

    public function test_a_scoring_template_wants_a_name_and_an_equation(): void
    {
        $this->actingAs($this->organiser)->from($this->url('/create'))
            ->post($this->url(), $this->templateData(['name' => '', 'equation' => '']))
            ->assertRedirect($this->url('/create'))
            ->assertSessionHasErrors(['name', 'equation']);

        $this->assertSame(0, ScoringSchema::count());
    }

    public function test_a_scoring_template_opens_with_its_schema(): void
    {
        $template = $this->makeTemplate('Swim');

        $this->actingAs($this->organiser)->get($this->url("/{$template->id}/edit"))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/ScoringTemplate')
                ->where('template.name', 'Swim')
                ->where('template.schema.equation', 'item.result')
                ->where('template.schema.rank_higher', false)
                // Settings it was saved without come with their defaults
                ->where('template.schema.local_variables', []));
    }

    public function test_saving_a_scoring_template_changes_it(): void
    {
        $template = $this->makeTemplate('Swim');

        $this->actingAs($this->organiser)->from($this->url("/{$template->id}/edit"))
            ->post($this->url("/{$template->id}"), $this->templateData(['name' => 'Swim 2', 'equation' => 'item.result * 2']))
            ->assertRedirect($this->url("/{$template->id}/edit"))
            ->assertSessionHasNoErrors();

        $template->refresh();
        $this->assertSame('Swim 2', $template->name);
        $this->assertSame('item.result * 2', $template->schema['equation']);
    }

    public function test_deleting_a_scoring_template_goes_back_to_scoring(): void
    {
        $template = $this->makeTemplate('Swim');

        $this->actingAs($this->organiser)->delete($this->url("/{$template->id}"))
            ->assertRedirect(route('manage.organisations.scoring', $this->org));

        $this->assertModelMissing($template);
    }

    public function test_another_organisations_scoring_template_is_not_found(): void
    {
        $template = $this->makeTemplate('Other', $this->makeOrg('Swansea'));

        $this->actingAs($this->organiser)->get($this->url("/{$template->id}/edit"))->assertNotFound();
        $this->actingAs($this->organiser)->post($this->url("/{$template->id}"), $this->templateData())->assertNotFound();
        $this->actingAs($this->organiser)->delete($this->url("/{$template->id}"))->assertNotFound();
        $this->assertModelExists($template);
    }

    public function test_new_formula_opens_blank(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/result-schemas/create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Formula')
                ->where('formula', null)
                ->where('organisation.tabs.4.current', true));
    }

    public function test_making_a_formula_lands_on_its_page(): void
    {
        $response = $this->actingAs($this->organiser)->post($this->url('/result-schemas'), [
            'name' => 'Points',
            'equation' => 'item.points',
            'global_variables' => [['name' => 'top', 'expression' => 'MAXIMUM(results, "points")', 'order' => 1]],
        ]);

        $formula = ResultSchemaTemplate::where('organisation', $this->org->id)->sole();
        $response->assertRedirect(route('manage.organisations.scoring.formulas.edit', [$this->org, $formula]));
        $this->assertSame('Points', $formula->name);
        $this->assertSame('item.points', $formula->schema['equation']);
        $this->assertSame('top', $formula->schema['global_variables'][0]['name']);
    }

    public function test_a_formula_wants_a_name_and_an_equation(): void
    {
        $this->actingAs($this->organiser)->from($this->url('/result-schemas/create'))
            ->post($this->url('/result-schemas'), ['name' => '', 'equation' => '', 'global_variables' => []])
            ->assertRedirect($this->url('/result-schemas/create'))
            ->assertSessionHasErrors(['name', 'equation']);

        $this->assertSame(0, ResultSchemaTemplate::count());
    }

    public function test_a_formula_opens_with_its_variables_and_equation(): void
    {
        $formula = $this->makeFormula('Points');

        $this->actingAs($this->organiser)->get($this->url("/result-schemas/{$formula->id}/edit"))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Formula')
                ->where('formula.name', 'Points')
                ->where('formula.equation', 'item.points')
                ->where('formula.global_variables.0.name', 'top'));
    }

    public function test_saving_a_formula_changes_it(): void
    {
        $formula = $this->makeFormula('Points');

        $this->actingAs($this->organiser)->from($this->url("/result-schemas/{$formula->id}/edit"))
            ->post($this->url("/result-schemas/{$formula->id}"), ['name' => 'Points 2', 'equation' => 'item.points / top', 'global_variables' => []])
            ->assertRedirect($this->url("/result-schemas/{$formula->id}/edit"))
            ->assertSessionHasNoErrors();

        $formula->refresh();
        $this->assertSame('Points 2', $formula->name);
        $this->assertSame('item.points / top', $formula->schema['equation']);
        $this->assertSame([], $formula->schema['global_variables']);
    }

    public function test_deleting_a_formula_goes_back_to_scoring(): void
    {
        $formula = $this->makeFormula('Points');

        $this->actingAs($this->organiser)->delete($this->url("/result-schemas/{$formula->id}"))
            ->assertRedirect(route('manage.organisations.scoring', $this->org));

        $this->assertModelMissing($formula);
    }

    public function test_another_organisations_formula_is_not_found(): void
    {
        $formula = $this->makeFormula('Other', $this->makeOrg('Swansea'));

        $this->actingAs($this->organiser)->get($this->url("/result-schemas/{$formula->id}/edit"))->assertNotFound();
        $this->actingAs($this->organiser)->delete($this->url("/result-schemas/{$formula->id}"))->assertNotFound();
        $this->assertModelExists($formula);
    }

    public function test_a_member_with_view_access_is_refused_scoring(): void
    {
        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');
        $template = $this->makeTemplate('Swim');

        $this->actingAs($member)->get($this->url())->assertForbidden();
        $this->actingAs($member)->post($this->url(), $this->templateData())->assertForbidden();
        $this->actingAs($member)->delete($this->url("/{$template->id}"))->assertForbidden();
        $this->assertModelExists($template);
    }

    public function test_old_links_by_name_still_open(): void
    {
        $this->actingAs($this->organiser)->get($this->appUrl('/organisation/Cardiff ULSC/scoring'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Manage/Organisation/Scoring'));
    }
}
