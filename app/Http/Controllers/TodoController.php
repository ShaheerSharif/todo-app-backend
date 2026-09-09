<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Services\TodoService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    public function __construct(private TodoService $todoService) {}

    public function index(Request $request)
    {
        $todos = $this->todoService->getAllForUser($request->user());

        return response()->json($todos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:40'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high'])],
            'due_at' => ['nullable', 'date'],
        ]);

        $todo = $this->todoService->create($request->user(), $validated);

        return response()->json($todo, 201);
    }

    public function show(Request $request, Todo $todo)
    {
        $this->todoService->authorizeTodo($todo, $request->user());

        return response()->json($todo);
    }

    public function update(Request $request, Todo $todo)
    {
        $this->todoService->authorizeTodo($todo, $request->user());

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:40'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high'])],
            'is_completed' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);

        $todo = $this->todoService->update($todo, $validated);

        return response()->json($todo);
    }

    public function destroy(Request $request, Todo $todo)
    {
        $this->todoService->authorizeTodo($todo, $request->user());

        $this->todoService->delete($todo);

        return response()->json(null, 204);
    }
}
