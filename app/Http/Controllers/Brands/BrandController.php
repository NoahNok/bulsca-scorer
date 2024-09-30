<?php

namespace App\Http\Controllers\Brands;

use App\Http\Controllers\Controller;
use App\Models\Brands\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{

    public function index()
    {
        return view('admin.brands.index');
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:brands',
            'website' => 'required|string|unique:brands',
            'email' => 'required|email|unique:brands',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'logo' => 'nullable|image',
        ]);


        $brand = Brand::create($request->all());

        if ($request->hasFile('logo')) {
            $brand->update([
                'logo' => $request->file('logo')->store('brands', 'public'),
            ]);
        }

        return redirect()->route('admin.brands.show', $brand)->with('success', 'Created brand successfully');
    }

    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|unique:brands,name,' . $brand->id,
            'website' => 'required|string|unique:brands,website,' . $brand->id,
            'email' => 'required|email|unique:brands,email,' . $brand->id,
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'logo' => 'nullable|image',
        ]);

        $oldImage = $brand->logo;

        $brand->update($request->all());

        if ($request->hasFile('logo')) {
            // Remove old file

            if ($oldImage !== null) {
                unlink(public_path() . '/storage/' . $oldImage);
            }




            $brand->update([
                'logo' => $request->file('logo')->store('brands', 'public'),
            ]);
        }

        return redirect()->route('admin.brands.show', $brand)->with('success', 'Updated brand successfully');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('admin.brands')->with('success', 'Deleted brand successfully');
    }
}
