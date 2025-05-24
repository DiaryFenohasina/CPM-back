<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CpmController extends Controller
{
    public function addTask (Request $request) {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.name' => 'required|string',
            'tasks.*.duration' => 'required|integer',
            'tasks.*.successors' => 'nullable|array'
        ]);

        foreach ($request->tasks as $task) {
            $taskData = [
                'name' => $task['name'],
                'duration' => $task['duration'],
                'successors' => json_encode($task['successors'] ?? ['fin'])
            ];

            Redis::hset('tasks', $task['name'], json_encode($taskData));
        }

        return response()->json(['message' => 'Tasks added successfully'],Response::HTTP_CREATED);
    }
}
