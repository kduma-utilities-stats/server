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
        return CounterResource::collection($meter->counters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCounterRequest $request, Meter $meter)
    {
        return new CounterResource($meter->counters()->create($request->validated()));
    }
}
