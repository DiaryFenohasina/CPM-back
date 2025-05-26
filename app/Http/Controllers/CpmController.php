<?php

namespace App\Http\Controllers;

use App\HasCpm;
use App\Http\Requests\AddTaskRequest;
use App\Http\Services\CpmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CpmController extends Controller
{
    use HasCpm;
    private $cpmService;

    public function __construct(CpmService $cpmService) {
        $this->cpmService = $cpmService;
    }
    public function addTask (AddTaskRequest $request) {
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
}
