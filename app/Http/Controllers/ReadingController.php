<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReadingRequest;
use App\Http\Requests\UpdateReadingRequest;
use App\Http\Resources\ReadingResource;
use App\Models\Reading;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Reading::class, 'reading');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $resource = $request->user()->readings()
            ->withCount('values as values_count')
            ->get();
        return ReadingResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReadingRequest $request)
    {
        $resource = $request->user()->readings()->create($request->validated());
        $resource->loadCount('values as values_count');
        return new ReadingResource($resource);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reading $reading)
    {
        $reading->loadCount('values as values_count');

        return new ReadingResource($reading);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReadingRequest $request, Reading $reading)
    {
        $reading->loadCount('values as values_count');

        $reading->update($request->validated());

        return new ReadingResource($reading);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reading $reading)
    {
        abort_if($reading->values()->count(), 406);

        $reading->delete();

        return response()->noContent();
    }
}
