<?php
namespace App\Http\Services;

class CriticalPathService
{
    public function calculate(array $tasks): array
    {
        $taskMap = [];
        foreach ($tasks as $task) {
            $taskMap[$task['name']] = array_merge($task, [
                'earlyStart' => 0,
                'earlyFinish' => 0,
                'lateStart' => INF,
                'lateFinish' => INF,
                'predecessors' => [],
            ]);
        }

        foreach ($taskMap as $task) {
            foreach ($task['successors'] as $succ) {
                $taskMap[$succ]['predecessors'][] = $task['name'];
            }
        }

        $sorted = $this->topoSort($taskMap);

        foreach ($sorted as $name) {
            $task = &$taskMap[$name];
            $maxEF = 0;
            foreach ($task['predecessors'] as $pred) {
                $maxEF = max($maxEF, $taskMap[$pred]['earlyFinish']);
            }
            $task['earlyStart'] = $maxEF;
            $task['earlyFinish'] = $maxEF + ($task['duration'] ?? 0);
        }

        $projectDuration = $taskMap['fin']['earlyFinish'];

        foreach (array_reverse($sorted) as $name) {
            $task = &$taskMap[$name];
            if (empty($task['successors'])) {
                $task['lateFinish'] = $projectDuration;
            } else {
                $minLS = INF;
                foreach ($task['successors'] as $succ) {
                    $minLS = min($minLS, $taskMap[$succ]['lateStart']);
                }
                $task['lateFinish'] = $minLS;
            }
            $task['lateStart'] = $task['lateFinish'] - ($task['duration'] ?? 0);
        }

        $criticalPath = [];
        foreach ($sorted as $name) {
            $task = &$taskMap[$name];
            $task['float'] = $task['lateStart'] - $task['earlyStart'];
            if ($task['float'] == 0 && $name !== 'fin') {
                $criticalPath[] = $name;
            }
        }

        return [
            'duration' => $projectDuration,
            'criticalPath' => $criticalPath,
            'tasks' => $taskMap
        ];
    }

    private function topoSort(array $tasks): array
    {
        $inDegree = [];
        foreach ($tasks as $name => $task) $inDegree[$name] = 0;
        foreach ($tasks as $task) {
            $successors = $task['successors'] ?? [];
            foreach ($successors as $succ) {
                $inDegree[$succ]++;
            }
        }

        $queue = [];
        foreach ($inDegree as $name => $deg) {
            if ($deg === 0) $queue[] = $name;
        }

        $result = [];
        while (!empty($queue)) {
            $current = array_shift($queue);
            $result[] = $current;
            foreach ($tasks[$current]['successors'] ?? [] as $succ) {
                $inDegree[$succ]--;
                if ($inDegree[$succ] === 0) {
                    $queue[] = $succ;
                }
            }
        }

        return $result;
    }
}
