<?php

namespace App\Http\Controllers;

use App\Models\Response;
use Illuminate\Http\Request;
use App\Http\Services\ResponseService;

class ResponseController extends Controller
{
    protected ResponseService $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'step_field_id' => ['required', 'uuid'],
            'response'      => ['nullable', 'array'],
            'description'   => ['nullable', 'string'],
            'attachments.*' => ['file', 'max:20480'],
        ]);

        $this->responseService->store($validated, $request);

        return back()->with('success', 'Form submitted successfully!');
    }

    public function show(Response $response)
    {
        //
    }

    public function edit(Response $response)
    {
        //
    }

    public function update(Request $request, Response $response)
    {
        //
    }

    public function destroy(Response $response)
    {
        //
    }
}
