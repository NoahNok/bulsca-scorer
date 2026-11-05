<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\SERC;
use App\Models\SERC\MarkingPointTemplate;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminMarkingPointTemplatesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->forceFill(['admin' => true])->save();
    }

    private function template(string $name, array $settings = ['mode' => 'default', 'min' => 0, 'max' => 10, 'step' => 0.5, 'use_toggle_for_half' => true]): MarkingPointTemplate
    {
        return MarkingPointTemplate::create(['name' => $name, 'settings' => $settings]);
    }

    private function range(array $overrides = []): array
    {
        return ['name' => 'Out of ten', 'settings' => [
            'mode' => 'default', 'min' => 0, 'max' => 10, 'step' => 0.5, 'use_toggle_for_half' => true, ...$overrides,
        ]];
    }

    /** A marking point in a SERC, using the template */
    private function useTemplate(MarkingPointTemplate $template): void
    {
        $comp = new Competition();
        $comp->name = 'Warwick 2026';
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->save();

        $serc = new SERC();
        $serc->name = 'Dry SERC';
        $serc->competition = $comp->id;
        $serc->scorable_entity = 'team';
        $serc->save();

        $judge = new SERCJudge();
        $judge->name = 'Casualty 1';
        $judge->description = '';
        $judge->serc = $serc->id;
        $judge->save();

        $point = new SERCMarkingPoint();
        $point->name = 'Talks to casualty';
        $point->weight = 1;
        $point->judge = $judge->id;
        $point->serc = $serc->id;
        $point->marking_point_template_id = $template->id;
        $point->save();
    }

    public function test_non_admins_cannot_see_templates(): void
    {
        $this->actingAs(User::factory()->create())
            ->get($this->appUrl('/admin/serc/marking-points'))
            ->assertRedirect('/');
    }

    public function test_templates_are_listed_by_name_with_their_kind_default_and_uses(): void
    {
        $choices = $this->template('Poor to excellent', ['mode' => 'choice', 'choice' => [['label' => 'Poor', 'value' => 1]]]);
        $range = $this->template('Out of ten');
        $range->forceFill(['default' => true])->save();
        $this->useTemplate($range);

        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/serc/marking-points'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/MarkingPointTemplates')
                ->has('templates', 2)
                ->where('templates.0.name', 'Out of ten')
                ->where('templates.0.kind', 'range')
                ->where('templates.0.default', true)
                ->where('templates.0.uses', 1)
                ->where('templates.0.url', route('manage.admin.marking-point-templates.edit', $range))
                ->where('templates.1.name', 'Poor to excellent')
                ->where('templates.1.kind', 'choices')
                ->where('templates.1.uses', 0)
                ->where('templates.1.url', route('manage.admin.marking-point-templates.edit', $choices)));
    }

    public function test_the_new_and_edit_pages_send_the_template(): void
    {
        $this->actingAs($this->admin)
            ->get($this->appUrl('/admin/serc/marking-points/create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/MarkingPointTemplate')
                ->where('template', null));

        $template = $this->template('Out of ten');
        $this->useTemplate($template);

        $this->actingAs($this->admin)
            ->get($this->appUrl("/admin/serc/marking-points/{$template->id}/edit"))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/MarkingPointTemplate')
                ->where('template.id', $template->id)
                ->where('template.name', 'Out of ten')
                ->where('template.settings.mode', 'default')
                ->where('template.uses', 1));
    }

    public function test_admins_can_add_a_range_template(): void
    {
        $this->actingAs($this->admin)
            ->post($this->appUrl('/admin/serc/marking-points'), $this->range())
            ->assertRedirect(route('manage.admin.marking-point-templates.index'));

        $template = MarkingPointTemplate::sole();
        $this->assertSame('Out of ten', $template->name);
        $this->assertSame(10, $template->settings['max']);
    }

    public function test_admins_can_change_a_template_to_choices(): void
    {
        $template = $this->template('Out of ten');

        $this->actingAs($this->admin)
            ->put($this->appUrl("/admin/serc/marking-points/{$template->id}"), ['name' => 'Poor to good', 'settings' => [
                'mode' => 'choice',
                'choice' => [['label' => 'Poor', 'value' => 1], ['label' => 'Good', 'value' => 5]],
            ]])
            ->assertRedirect(route('manage.admin.marking-point-templates.index'));

        $template->refresh();
        $this->assertSame('Poor to good', $template->name);
        $this->assertSame('choice', $template->settings['mode']);
        $this->assertCount(2, $template->settings['choice']);
        // the range's fields aren't kept
        $this->assertArrayNotHasKey('max', $template->settings);
    }

    public function test_a_range_must_end_above_where_it_starts_and_choices_need_distinct_values(): void
    {
        $this->actingAs($this->admin)
            ->post($this->appUrl('/admin/serc/marking-points'), $this->range(['min' => 5, 'max' => 2]))
            ->assertSessionHasErrors('settings.max');

        $this->actingAs($this->admin)
            ->post($this->appUrl('/admin/serc/marking-points'), ['name' => 'Choices', 'settings' => [
                'mode' => 'choice',
                'choice' => [['label' => 'Poor', 'value' => 1], ['label' => 'Also poor', 'value' => 1]],
            ]])
            ->assertSessionHasErrors('settings.choice.1.value');

        $this->assertSame(0, MarkingPointTemplate::count());
    }

    public function test_an_unused_template_can_be_deleted(): void
    {
        $template = $this->template('Out of ten');

        $this->actingAs($this->admin)
            ->delete($this->appUrl("/admin/serc/marking-points/{$template->id}"))
            ->assertRedirect(route('manage.admin.marking-point-templates.index'));

        $this->assertModelMissing($template);
    }

    public function test_a_template_in_use_cant_be_deleted(): void
    {
        $template = $this->template('Out of ten');
        $this->useTemplate($template);

        $this->actingAs($this->admin)
            ->delete($this->appUrl("/admin/serc/marking-points/{$template->id}"))
            ->assertSessionHasErrors('template');

        $this->assertModelExists($template);
    }
}
