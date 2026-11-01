<?php

namespace App\Http\Controllers\Manage\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SERC\SaveMarkingPointTemplateRequest;
use App\Models\SERC\MarkingPointTemplate;
use Inertia\Inertia;

/**
 * Admin's marking point templates: how each marking point in a SERC is marked. The UI calls the
 * `default` mode a Range and `choice` Choices.
 */
class AdminMarkingPointTemplatesController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/MarkingPointTemplates', [
            'templates' => MarkingPointTemplate::withCount('markingPoints')->orderBy('name')->get()
                ->map(fn (MarkingPointTemplate $template) => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'kind' => ($template->settings['mode'] ?? null) === 'choice' ? 'choices' : 'range',
                    'default' => (bool) $template->default,
                    'uses' => $template->marking_points_count,
                    'url' => route('manage.admin.marking-point-templates.edit', $template),
                ]),
            'createUrl' => route('manage.admin.marking-point-templates.create'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/MarkingPointTemplate', ['template' => null]);
    }

    public function store(SaveMarkingPointTemplateRequest $request)
    {
        // Validation leaves out the other mode's fields, so only the chosen one's are kept
        MarkingPointTemplate::create($request->validated());

        return to_route('manage.admin.marking-point-templates.index')->with('success', 'Template added');
    }

    public function edit(MarkingPointTemplate $marking_point)
    {
        return Inertia::render('Admin/MarkingPointTemplate', [
            'template' => [
                'id' => $marking_point->id,
                'name' => $marking_point->name,
                'settings' => $marking_point->settings,
                'uses' => $marking_point->markingPoints()->count(),
            ],
        ]);
    }

    public function update(SaveMarkingPointTemplateRequest $request, MarkingPointTemplate $marking_point)
    {
        $marking_point->update($request->validated());

        return to_route('manage.admin.marking-point-templates.index')->with('success', 'Template saved');
    }

    /**
     * Not while marking points use it: deleting it would leave them with no way to be marked.
     */
    public function destroy(MarkingPointTemplate $marking_point)
    {
        $uses = $marking_point->markingPoints()->count();

        if ($uses > 0) {
            return back()->withErrors(['template' => "{$uses} marking " . ($uses === 1 ? 'point uses' : 'points use') . ' this template, so it can\'t be deleted.']);
        }

        $marking_point->delete();

        return to_route('manage.admin.marking-point-templates.index')->with('success', 'Template deleted');
    }
}
