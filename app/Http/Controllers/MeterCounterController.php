<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCounterRequest;
use App\Http\Requests\UpdateCounterRequest;
use App\Http\Resources\CounterResource;
use App\Models\Counter;
use App\Models\Meter;

class MeterCounterController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->middleware("can:viewAny,".Counter::class.',meter')->only('index');
        $this->middleware("can:create,".Counter::class.',meter')->only('store');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Meter $meter)
    {
        $resource = $meter->counters()
            ->withCount('values as values_count')
            ->get();
        return CounterResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCounterRequest $request, Meter $meter)
    {
        $resource = $meter->counters()->create($request->validated());

        $resource->loadCount('values as values_count');

        return new CounterResource($resource);
    }
}
