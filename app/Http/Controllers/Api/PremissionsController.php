<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Premission;
use Illuminate\Http\Request;

class PremissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $premissions = Premission::all();
        return response()->json($premissions, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => 'required',
        ]);

        $premission = new Premission();
        $premission->name = $request->name;
        $premission->save();

        return response()->json($premission, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Premission $premission)
    {
        return response()->json($premission, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Premission $premission)
    {
        $request->validate([
            "name" => "required",
        ]);

        $premission->name = $request->name;
        $premission->update();
        return response()->json($premission, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Premission $premission)
    {
        $premission->delete();
        return response()->json($premission, 200);
    }
}
