<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCounterRequest;
use App\Http\Requests\StoreValueRequest;
use App\Http\Requests\UpdateCounterRequest;
use App\Http\Resources\CounterResource;
use App\Http\Resources\ValueResource;
use App\Models\Counter;
use App\Models\Meter;
use App\Models\Reading;
use App\Models\Value;

class ReadingValueController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->middleware("can:viewAny,".Value::class.',reading')->only('index');
        $this->middleware("can:create,".Value::class.',reading')->only('store');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Reading $reading)
    {
        return ValueResource::collection($reading->values);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValueRequest $request, Reading $reading)
    {
        return new ValueResource($reading->values()->create($request->validated()));
    }
}
