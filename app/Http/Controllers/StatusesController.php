<?php

namespace App\Http\Controllers;

use App\Events\StatusCreated;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusesController extends Controller
{
    public function index()
    {
        return StatusResource::collection(
            Status::query()->latest()->paginate()
        );
    }

    public function show(Status $status)
    {
        return view('statuses.show', [
            'status' => StatusResource::make($status)
        ]);
    }

    public function store(Request $request)
    {
        $validStatus = $request->validate(['body' => 'required|min:5']);

        $status = $request->user()->statuses()->create($validStatus);

        $statusResource = StatusResource::make($status);

        StatusCreated::dispatch($statusResource);

        return $statusResource;
    }
}
