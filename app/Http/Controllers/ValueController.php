<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreValueRequest;
use App\Http\Requests\UpdateValueRequest;
use App\Http\Resources\ValueResource;
use App\Models\Counter;
use App\Models\Meter;
use App\Models\Reading;
use App\Models\Value;

class ValueController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Value::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Value $value)
    {
        return new ValueResource($value);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValueRequest $request, Value $value)
    {
        $value->update($request->validated());

        return new ValueResource($value);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Value $value)
    {
        $value->delete();

        return response()->noContent();
    }
}
