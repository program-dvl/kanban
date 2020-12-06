<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Validator;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends Controller
{
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Repositories\TaskRepository
     */
    private $taskRepository;

    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Repositories\TaskRepository $taskRepository
     * @return void
     */
    public function __construct(
        ResponseHelper $responseHelper,
        TaskRepository $taskRepository
    ) {
        $this->responseHelper = $responseHelper;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Fetch all tasks
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::all();

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = ($tasks->isEmpty()) ? 'No tasks found' : 'Tasks listed successfully.';
        $apiData = $tasks->toArray();

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData, false);
    }

     /**
     * Create new task.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Server side validations
        $validation = [
            'name' => 'required|max:255',
            'user_id' => 'sometimes|required|numeric|exists:users,id',
            'description' => 'sometimes|required',
            'deadline' => 'sometimes|required|after:today|date',
            'priority' => 'sometimes|required|in:High,Medium,Low',
            'state_id' => 'sometimes|required|exists:state,id,deleted_at,NULL'
        ];

        $validator = Validator::make($request->all(), $validation);

         // If request parameter have any error
         if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                $validator->errors()->first()
            );
        }
        // Store Task
        $task = $this->taskRepository->store($request->all());

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = 'Task Created Successfully.';
        $apiData = $task->toArray();

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Update new task.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $taskId
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $taskId): JsonResponse
    {
        try{
            // Server side validations
            $validation = [
                'name' => 'sometimes|required|max:255',
                'user_id' => 'sometimes|required|numeric|exists:users,id',
                'description' => 'sometimes|required',
                'deadline' => 'sometimes|required|date|after:yesterday|date_format:Y-m-d H:i:s',
                'priority' => 'sometimes|required|in:High,Medium,Low',
                'state_id' => 'sometimes|required|exists:state,id,deleted_at,NULL'
            ];

            $validator = Validator::make($request->all(), $validation);

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    $validator->errors()->first()
                );
            }
            // Update Task
            $task = $this->taskRepository->update($request->all(), $taskId);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = 'Task Updated Successfully.';
            $apiData = $task->toArray();

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);

        } catch (ModelNotFoundException $e) {
            return $this->responseHelper->error(
                Response::HTTP_NOT_FOUND,
                Response::$statusTexts[Response::HTTP_NOT_FOUND],
                'Task not found'
            );
        }
    }

    /**
     * Get Task Detail
     *
     * @param \Illuminate\Http\Request $request
     * @param int $taskId
     * @return Illuminate\Http\JsonResponse
     */
    public function show(Request $request, int $taskId): JsonResponse
    {
        try{

            // Get task detail
            $task = $this->taskRepository->find($taskId);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = 'Task found successfully';
            $apiData = $task->toArray();

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);

        } catch (ModelNotFoundException $e) {
            return $this->responseHelper->error(
                Response::HTTP_NOT_FOUND,
                Response::$statusTexts[Response::HTTP_NOT_FOUND],
                'Task not found'
            );
        }
    }

    /**
     * Delete Task
     *
     * @param \Illuminate\Http\Request $request
     * @param int $taskId
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $taskId): JsonResponse
    {
        try{

            // Update Task
            $task = $this->taskRepository->delete($taskId);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = 'Task deleted successfully';

            return $this->responseHelper->success($apiStatus, $apiMessage);

        } catch (ModelNotFoundException $e) {
            return $this->responseHelper->error(
                Response::HTTP_NOT_FOUND,
                Response::$statusTexts[Response::HTTP_NOT_FOUND],
                'Task not found'
            );
        }
    }

}
