<?php

namespace App;
use Illuminate\Support\Facades\Redis;


trait HasCpm
{
    public function getTaskFromRedis() {
        $tasks = Redis::hgetall('tasks');
        if (!$tasks) return null;
        return  array_map(fn($task) => json_decode($task, true), $tasks);
    }
}
