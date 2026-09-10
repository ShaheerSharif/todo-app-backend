<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Services\TodoService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    public function __construct(private TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'is_completed' => ['sometimes', 'boolean'],
        ]);

        $todos = $this->todoService->getAllForUser($request->user(), $validated);

        return $this->successResponse(['todos' => $todos]);
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

        return $this->successResponse(['todo' => $todo], 201);
    }

    public function show(Request $request, int $id)
    {
        $todo = Todo::find($id);

        if (empty($todo) || $todo == null) {
            return $this->errorResponse(null, 404, 'Todo not found');
        }

        $this->todoService->authorizeTodo($todo, $request->user());

        return $this->successResponse(['todo' => $todo]);
    }

    public function update(Request $request, int $id)
    {
        $todo = Todo::find($id);

        if (empty($todo) || $todo == null) {
            return $this->errorResponse(null, 404, 'Todo not found');
        }

        $this->todoService->authorizeTodo($todo, $request->user());

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:40'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high'])],
            'is_completed' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);

        $todo = $this->todoService->update($todo, $validated);

        return $this->successResponse(['todo' => $todo]);
    }

    public function destroy(Request $request, int $id)
    {
        $todo = Todo::find($id);

        if (empty($todo) || $todo == null) {
            return $this->errorResponse(null, 404, 'Todo not found');
        }

        $this->todoService->authorizeTodo($todo, $request->user());

        $this->todoService->delete($todo);

        return $this->successResponse(null, 204);
    }
}
