<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseHelper;
use App\Repositories\StateRepository;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\StateController;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\State;

class StateControllerTest extends TestCase
{
    /**
     * @testdox Test store validation fail
     *
     * @return void
     */
    public function testStoreValidationError()
    {
        $responseHelper = $this->mockClass(ResponseHelper::class);
        $stateRepository = $this->mockClass(StateRepository::class);

        $errors = new Collection([
            'sample-error message'
        ]);

        $validator = $this->mockClass(\Illuminate\Validation\Validator::class);
        $validator->shouldReceive('fails')
            ->andReturn(true)
            ->shouldReceive('errors')
            ->andReturn($errors);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $request = new Request();
        
        $responseHelper->shouldReceive('error')
            ->once()
            ->with(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                $errors->first()
            )
            ->andReturn(new JsonResponse());

        $controller = $this->getController(
            $responseHelper,
            $stateRepository
        );

        $response = $controller->store($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @testdox Test store success
     *
     * @return void
     */
    public function testStoreSuccess()
    {
        $responseHelper = $this->mockClass(ResponseHelper::class);
        $stateRepository = $this->mockClass(StateRepository::class);

        $data = [
            'name' => 'testName'
        ];

        $validator = $this->mockClass(\Illuminate\Validation\Validator::class);
        $validator->shouldReceive('fails')
            ->andReturn(false);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $request = new Request($data);
        $stateModel = new State();
        $stateRepository->shouldReceive('store')
            ->once()
            ->with($request->all())
            ->andReturn($stateModel);

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = 'State Created Successfully.';
        $apiData = $stateModel->toArray();

        $responseHelper->shouldReceive('success')
            ->once()
            ->with($apiStatus, $apiMessage, $apiData)
            ->andReturn(new JsonResponse());

        $controller = $this->getController(
            $responseHelper,
            $stateRepository
        );

        $response = $controller->store($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Repositories\StateRepository $stateRepository
     * @return void
     */
    private function getController(
        ResponseHelper $responseHelper,
        StateRepository $stateRepository
    ) {
        return new StateController(
            $responseHelper,
            $stateRepository
        );
    }

    /**
     * Mock an object
     *
     * @param string name
     *
     * @return Mockery
     */
    public function mockClass($class)
    {
        return Mockery::mock($class);
    }
}
