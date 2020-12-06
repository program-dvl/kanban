<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Validator;
use App\Repositories\StateRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StateController extends Controller
{
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Repositories\StateRepository
     */
    private $stateRepository;

    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Repositories\StateRepository $stateRepository
     * @return void
     */
    public function __construct(
        ResponseHelper $responseHelper,
        StateRepository $stateRepository
    ) {
        $this->responseHelper = $responseHelper;
        $this->stateRepository = $stateRepository;
    }

     /**
     * Create new state.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Server side validations
        $validation = [
            'name' => 'required|max:255',
            'order_id' => 'sometimes|required|unique:state',
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
        // Store State
        $state = $this->stateRepository->store($request->all());

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = 'State Created Successfully.';
        $apiData = $state->toArray();

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Delete state
     *
     * @param \Illuminate\Http\Request $request
     * @param int $stateId
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $stateId): JsonResponse
    {
        try{

            // Delete State
            $state = $this->stateRepository->delete($stateId);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = 'State deleted successfully';

            return $this->responseHelper->success($apiStatus, $apiMessage);

        } catch (ModelNotFoundException $e) {
            return $this->responseHelper->error(
                Response::HTTP_NOT_FOUND,
                Response::$statusTexts[Response::HTTP_NOT_FOUND],
                'State not found'
            );
        }
    }
    
    /**
     * Update new state.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $stateId
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $stateId): JsonResponse
    {
        try{
            // Server side validations
            $validation = [
                'name' => 'required|max:255',
                'order_id' => 'sometimes|required',
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

            // Check valid order id exist or not
            $return = $this->stateRepository->checkOrderIdExist($request->order_id, $stateId);
            if (!$return) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    'Please select valid order id'
                );
            }

            // Update State
            $state = $this->stateRepository->update($request->all(), $stateId);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = 'State Updated Successfully.';
            $apiData = $state->toArray();

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);

        } catch (ModelNotFoundException $e) {
            return $this->responseHelper->error(
                Response::HTTP_NOT_FOUND,
                Response::$statusTexts[Response::HTTP_NOT_FOUND],
                'State not found'
            );
        }
    }

    /**
     * Get Board
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function getBoard(Request $request){
        
        // Get state wise tasks
        $apiData = $this->stateRepository->getBoard();

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = 'Board data listed successfully';

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

}
