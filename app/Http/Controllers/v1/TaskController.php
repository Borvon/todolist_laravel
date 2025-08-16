<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\dtos\v1\Task\CreateTaskDto;
use App\Http\dtos\v1\Task\UpdateTaskDto;
use App\Services\TaskService;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request, TaskService $taskService)
    {
        $validated = $request->validate([
            'limit' => 'required|integer',
            'offset' => 'required|integer'
        ]);

        $tasksDto = $taskService->index($validated['limit'], $validated['offset']);

        return response()->json($tasksDto);
    }

    public function show($id, TaskService $taskService)
    {
        $taskDto = $taskService->show($id);
        return response()->json($taskDto);
    }

    public function store(Request $request, TaskService $taskService)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'due_date' => 'nullable|date|after_or_equal:now',
            'status' => [
                'nullable',
                Rule::in(['new', 'in_progress', 'completed'])
            ]
        ]);

        $taskDto = new CreateTaskDto(
            title: $request->input('title'),
            description: $request->input('description'),
            due_date: $request->input('due_date'),
            status: $request->input('status')
        );

        $responseDto = $taskService->store($taskDto);

        return response()->json($responseDto);
    }

    public function update($id, Request $request, TaskService $taskService)
    {
        $validated = $request->validate([
            'title' => 'nullable',
            'description' => 'nullable',
            'due_date' => 'nullable|date|after_or_equal:now',
            'status' => [
                'nullable',
                Rule::in(['new', 'in_progress', 'completed'])
            ]
        ]);

        $taskDto = new UpdateTaskDto(
            title: $request->input('title'),
            description: $request->input('description'),
            due_date: $request->input('due_date'),
            status: $request->input('status')
        );

        $responseDto = $taskService->update($id, $taskDto);
        return response()->json($responseDto);
    }

    public function delete()
    {

    }
}
