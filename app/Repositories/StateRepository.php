<?php

namespace App\Repositories;

use App\Models\State;

class StateRepository
{
    /**
     * @var App\Models\State
     */
    private $state;

    /**
     * Create a new state repository instance.
     *
     * @param  App\Models\State $state
     * @return void
     */
    public function __construct(
        State $state
    ) {
        $this->state = $state;
    }

    /**
     * Store state details.
     *
     * @param array $request
     * @return App\Models\State
     */
    public function store(array $request): State
    {
        if (empty($request['order_id'])) {
            $lastOrderId = $this->state->select('order_id')->max('order_id');
            $request['order_id'] = $lastOrderId + 1;
        }
        // Store state details
        return $this->state->create($request);
    }

    /**
     * Update state.
     *
     * @param array $request
     * @param int $stateId
     * @return App\Models\State
     */
    public function update(array $request, int $stateId): State
    {
        $state = $this->state->findOrfail($stateId);
        $state->update($request);
        return $state;
    }

    /**
     * Remove State.
     *
     * @param int $stateId
     * @return bool
     */
    public function delete(int $stateId)
    {
        $state = $this->state->findOrfail($stateId);
        return $state->delete();
    }

    /**
     * Get state wise tasks
     *
     * @return array
     */
    public function getBoard(): array
    {
        $state = $this->state->with('tasks')->with(['tasks.user' => function ($query) {
            $query->select('name');
        }])->orderBy('order_id')->get();
        return $state->toArray();
    }

    /**
     * Check valid order id
     *
     * @param int $orderId
     * @param int $stateId
     * @return bool
     */
    public function checkOrderIdExist(int $orderId, int $stateId)
    {
        $state = $this->state->findOrfail($stateId);
        if($state->order_id == $orderId){
            return true;
        }
        if($this->state->where('order_id', $orderId)->exists()){
            return false;
        }
        return true;
    }
}
