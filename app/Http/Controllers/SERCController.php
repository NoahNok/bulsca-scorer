<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\SERC;
use Illuminate\Http\Request;

/**
 * What's left of a SERC's Blade routes: the marking sheet image (hidden for now).
 * Its pages are Manage\SercEventController.
 */
class SERCController extends Controller
{
    public function addSercImage(Competition $comp, SERC $serc, Request $request)
    {
        $request->validate([
            'image' => 'nullable|image',
        ]);

        $oldImage = $serc->image;
        if ($request->hasFile('image')) {
            // Remove old file



            if ($oldImage !== null) {
                unlink(public_path() . '/storage/' . $oldImage);
            }




            $serc->image = $request->file('image')->store('serc-images', 'public');
            $serc->save();
        }

        return redirect()->back();
    }

    public function removeSercImage(Competition $comp, SERC $serc)
    {
        unlink(public_path() . '/storage/' . $serc->image);
        $serc->image = null;
        $serc->save();
        return redirect()->back();
    }
}
