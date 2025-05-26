<?php

namespace App\Http\Controllers;

use App\HasCpm;
use App\Http\Requests\AddTaskRequest;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Services\CriticalPathService;


class CpmController extends Controller
{
    use HasCpm;
    public function addTasks(AddTaskRequest $addTaskRequest) {
        foreach ($addTaskRequest->tasks as $task) {
            $taskData = [
                'name' => $task['name'],
                'duration' => $task['duration'],
                'successors' => json_encode($task['successors'] ?? ['fin'])
            ];

            Redis::hset('tasks', $task['name'], json_encode($taskData));
        }

        return response()->json(['message' => 'Tasks added successfully'],Response::HTTP_CREATED);

    }

    public function getTasks()
    {
        $tasks = Redis::hgetall('tasks');
        $decoded = [];

        foreach ($tasks as $key => $taskJson) {
            $decoded[] = json_decode($taskJson, true);
        }

        return response()->json($decoded,Response::HTTP_OK);
    }

    public function clearTasks()
    {
        Redis::del('tasks');
        return response()->json(['message' => 'All tasks cleared from Redis'], Response::HTTP_OK);
    }

        public function getCriticalPath(CriticalPathService $cpmService)
    {
        $rawTasks = Redis::hgetall('tasks');
        $tasks = [];

        foreach ($rawTasks as $task) {
            $tasks[] = json_decode($task, true);
        }

        return response()->json($cpmService->calculate($tasks));
    }
}
