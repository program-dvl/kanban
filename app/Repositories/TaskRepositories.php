<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    /**
     * @var App\Models\Task
     */
    private $task;

    /**
     * Create a new task repository instance.
     *
     * @param  App\Models\Task $task
     * @return void
     */
    public function __construct(
        Task $task
    ) {
        $this->task = $task;
    }

    /**
     * Store task details.
     *
     * @param array $request
     * @return App\Models\Task
     */
    public function store(array $request): Task
    {
        // Store task details
        return $this->task->create($request);
    }

    /**
     * Update task.
     *
     * @param array $request
     * @param int $taskId
     * @return App\Models\Task
     */
    public function update(array $request, int $taskId): Task
    {
        $task = $this->task->findOrfail($taskId);
        $task->update($request);
        return $task;
    }

    /**
     * Remove Task.
     *
     * @param int $taskId
     * @return bool
     */
    public function delete(int $taskId)
    {
        $task = $this->task->findOrfail($taskId);
        return $task->delete();
    }


    /**
     * Find task.
     *
     * @param string $taskId
     * @return App\Models\Task
     */
    public function find(int $taskId): Task
    {
        return $this->task->findOrfail($taskId);
    }
}
