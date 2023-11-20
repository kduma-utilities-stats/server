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
        $counter->loadCount('values as values_count');

        return new CounterResource($counter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCounterRequest $request, Counter $counter)
    {
        $counter->loadCount('values as values_count');

        $counter->update($request->validated());

        return new CounterResource($counter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Counter $counter)
    {
        abort_if($counter->values()->count(), 406);

        $counter->delete();

        return response()->noContent();
    }
}
