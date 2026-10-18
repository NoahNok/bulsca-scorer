<?php

namespace App\Http\Controllers\SERC;

use App\Http\Controllers\Controller;
use App\Http\Requests\SERC\SaveMarkingPointTemplateRequest;
use App\Models\SERC\MarkingPointTemplate;

class MarkingPointTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $temapltes = MarkingPointTemplate::paginate(10);

        return view('admin.serc.marking-point-templates', [
            'templates' => $temapltes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.serc.marking-point-template-creator');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveMarkingPointTemplateRequest $request)
    {
        $data = $request->validated();

        MarkingPointTemplate::create([
            'name' => $data['name'],
            'settings' => $data['settings'],
        ]);

        session()->flash('success', 'Marking Point Template created successfully.');

        return response()->json([], 201);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarkingPointTemplate $marking_point)
    {
        return view('admin.serc.marking-point-template-editor', [
            'template' => $marking_point,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveMarkingPointTemplateRequest $request, MarkingPointTemplate $marking_point)
    {
        $data = $request->validated();

        $marking_point->update([
            'name' => $data['name'],
            'settings' => $data['settings'],
        ]);

        session()->flash('success', 'Marking Point Template updated successfully.');

        return response()->json([], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarkingPointTemplate $marking_point)
    {
        $marking_point->delete();

        session()->flash('success', 'Marking Point Template deleted successfully.');

        return response()->json([], 200);
    }
}
