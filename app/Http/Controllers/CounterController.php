<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCounterRequest;
use App\Http\Requests\UpdateCounterRequest;
use App\Http\Resources\CounterResource;
use App\Models\Counter;
use App\Models\Meter;

class CounterController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Counter::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Counter $counter)
    {
        return new CounterResource($counter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCounterRequest $request, Counter $counter)
    {
        $counter->update($request->validated());

        return new CounterResource($counter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Counter $counter)
    {
        $counter->delete();

        return response()->noContent();
    }
}
