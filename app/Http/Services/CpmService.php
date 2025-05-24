<?php
namespace App\Http\Services;

class CpmService {
    public function FormatTask($tasksArray):array {
        $tasksWithAntecedents = [];
        
        foreach ($tasksArray as $taskName => $task) {
            $antecedents = [];
    
            foreach ($tasksArray as $prevTaskName => $prevTask) {
                if (in_array($taskName, json_decode($prevTask['successors'], true) ?? [])) {
                    $antecedents[] = $prevTask;
                }
            }

            if (empty($antecedents)) {
                $antecedents[] = [
                    'name' => 'debut',
                    'duration' => 0,
                    'successors' => '[]'
                ];
            }

            $tasksWithAntecedents[$task['name']] = [
                'name' => $task['name'],
                'duration' => $task['duration'],
                'successors' => $task['successors'],
                'antecedents' => $antecedents,
            ];
        }

        return $tasksWithAntecedents;
    }
}