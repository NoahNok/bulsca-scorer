<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\OrganisationHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Scoring\SaveFormulaRequest;
use App\Http\Requests\Organisation\Scoring\SaveScoringTemplateRequest;
use App\Models\Event\ScoringSchema;
use App\Models\Organisation\Organisation;
use App\Models\ResultSchemaTemplate;
use App\Services\ScoringSchemas;
use Inertia\Inertia;

/**
 * An organisation's Scoring (Svelte, its Organisers only): its Scoring templates, which an event's scoring
 * starts from, and its Formulas, which a Results sheet copies in. Each is made, edited and deleted on its
 * own page.
 */
class OrganisationScoringController extends Controller
{
    public function index(Organisation $organisation)
    {
        return Inertia::render('Manage/Organisation/Scoring', [
            'organisation' => OrganisationHeader::for($organisation),
            'templates' => $organisation->scoringSchemas->map(fn (ScoringSchema $template) => [
                'id' => $template->id,
                'name' => $template->name,
                'url' => route('manage.organisations.scoring.templates.edit', [$organisation, $template]),
            ]),
            'formulas' => $organisation->resultSchemas->map(fn (ResultSchemaTemplate $formula) => [
                'id' => $formula->id,
                'name' => $formula->name,
                'url' => route('manage.organisations.scoring.formulas.edit', [$organisation, $formula]),
            ]),
        ]);
    }

    public function createTemplate(Organisation $organisation)
    {
        return $this->templatePage($organisation, null);
    }

    public function storeTemplate(SaveScoringTemplateRequest $request, Organisation $organisation)
    {
        $template = new ScoringSchema();
        $template->organisation = $organisation->id;
        $template->editFromRequest($request);

        return redirect()->route('manage.organisations.scoring.templates.edit', [$organisation, $template])
            ->with('success', "{$template->name} created");
    }

    public function editTemplate(Organisation $organisation, ScoringSchema $template)
    {
        $this->ensureOwns($organisation, $template);

        return $this->templatePage($organisation, $template);
    }

    public function updateTemplate(SaveScoringTemplateRequest $request, Organisation $organisation, ScoringSchema $template)
    {
        $this->ensureOwns($organisation, $template);

        $template->editFromRequest($request);

        return back()->with('success', "{$template->name} saved");
    }

    /**
     * Events that started from it have their own copy, so they keep scoring as they do
     */
    public function destroyTemplate(Organisation $organisation, ScoringSchema $template)
    {
        $this->ensureOwns($organisation, $template);

        $template->delete();

        return redirect()->route('manage.organisations.scoring', $organisation)->with('success', "{$template->name} deleted");
    }

    public function createFormula(Organisation $organisation)
    {
        return $this->formulaPage($organisation, null);
    }

    public function storeFormula(SaveFormulaRequest $request, Organisation $organisation)
    {
        $formula = new ResultSchemaTemplate();
        $formula->organisation = $organisation->id;
        $formula->editFromRequest($request);

        return redirect()->route('manage.organisations.scoring.formulas.edit', [$organisation, $formula])
            ->with('success', "{$formula->name} created");
    }

    public function editFormula(Organisation $organisation, ResultSchemaTemplate $formula)
    {
        $this->ensureOwns($organisation, $formula);

        return $this->formulaPage($organisation, $formula);
    }

    public function updateFormula(SaveFormulaRequest $request, Organisation $organisation, ResultSchemaTemplate $formula)
    {
        $this->ensureOwns($organisation, $formula);

        $formula->editFromRequest($request);

        return back()->with('success', "{$formula->name} saved");
    }

    /**
     * Results sheets copied it in, so they keep their Formula
     */
    public function destroyFormula(Organisation $organisation, ResultSchemaTemplate $formula)
    {
        $this->ensureOwns($organisation, $formula);

        $formula->delete();

        return redirect()->route('manage.organisations.scoring', $organisation)->with('success', "{$formula->name} deleted");
    }

    /** New Scoring template, or editing one */
    private function templatePage(Organisation $organisation, ?ScoringSchema $template)
    {
        return Inertia::render('Manage/Organisation/ScoringTemplate', [
            'organisation' => OrganisationHeader::for($organisation),
            'template' => $template ? [
                'id' => $template->id,
                'name' => $template->name,
                'schema' => ScoringSchemas::withDefaults($template->schema),
            ] : null,
        ]);
    }

    /** New Formula, or editing one */
    private function formulaPage(Organisation $organisation, ?ResultSchemaTemplate $formula)
    {
        return Inertia::render('Manage/Organisation/Formula', [
            'organisation' => OrganisationHeader::for($organisation),
            'formula' => $formula ? [
                'id' => $formula->id,
                'name' => $formula->name,
                'equation' => $formula->schema['equation'] ?? '',
                'global_variables' => $formula->schema['global_variables'] ?? [],
            ] : null,
        ]);
    }

    private function ensureOwns(Organisation $organisation, ScoringSchema|ResultSchemaTemplate $item): void
    {
        abort_unless((int) $item->organisation === $organisation->id, 404);
    }
}
