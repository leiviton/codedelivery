<?php

namespace CodeDelivery\Transformers;

use CodeDelivery\Models\User;
use League\Fractal\TransformerAbstract;
use CodeDelivery\Models\Action;

/**
 * Class ActionTransformer
 * @package namespace CodeDelivery\Transformers;
 */
class ActionTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user'];
    /**
     * Transform the \Action entity
     * @param \Action $model
     *
     * @return array
     */
    public function transform(Action $model)
    {
        return [
            'id'         => (int) $model->id,
            'order_id'   => (int) $model->order_id,
            'action'     => $model->action,
            'geo_location' => $model->geo_location,

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }

    public function includeUser(User $model){
        return $this->item($model->deliveryman, new UserTransformer());
    }
}
