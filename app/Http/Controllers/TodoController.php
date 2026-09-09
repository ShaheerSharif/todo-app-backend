<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $todos = $request->user()
            ->todos()
            ->latest()
            ->paginate(15);

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

        $todo = $request->user()->todos()->create($validated);

        return response()->json($todo, 201);
    }

    public function show(Request $request, Todo $todo)
    {
        $this->authorizeTodo($request, $todo);

        return response()->json($todo);
    }

    public function update(Request $request, Todo $todo)
    {
        $this->authorizeTodo($request, $todo);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:40'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high'])],
            'is_completed' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);

        // Keep completed_at in sync with is_completed
        if (array_key_exists('is_completed', $validated)) {
            $validated['completed_at'] = $validated['is_completed']
                ? now()
                : null;
        }

        $todo->update($validated);

        return response()->json($todo);
    }

    public function destroy(Request $request, Todo $todo)
    {
        $this->authorizeTodo($request, $todo);

        $todo->delete();

        return response()->json(null, 204);
    }

    /**
     * Ensure the todo belongs to the authenticated user.
     */
    private function authorizeTodo(Request $request, Todo $todo): void
    {
        if ($todo->user_id !== $request->user()->id) {
            abort(403, 'You do not own this todo.');
        }
    }
}
