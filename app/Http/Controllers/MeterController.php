<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMeterRequest;
use App\Http\Requests\UpdateMeterRequest;
use App\Http\Resources\MeterResource;
use App\Models\Meter;
use Illuminate\Http\Request;

class MeterController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Meter::class, 'meter');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return MeterResource::collection($request->user()->meters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMeterRequest $request)
    {
        return new MeterResource($request->user()->meters()->create($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Meter $meter)
    {
        return new MeterResource($meter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMeterRequest $request, Meter $meter)
    {
        $meter->update($request->validated());

        return new MeterResource($meter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meter $meter)
    {
        abort_if($meter->counters()->count(), 406);

        $meter->delete();

        return response()->noContent();
    }
}
