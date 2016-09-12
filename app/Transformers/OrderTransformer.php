<?php

namespace CodeDelivery\Transformers;

use Illuminate\Database\Eloquent\Collection;
use League\Fractal\TransformerAbstract;
use CodeDelivery\Models\Order;

/**
 * Class OrderTransformer
 * @package namespace CodeDelivery\Transformers;
 */
class OrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['items'];


    /**
     * Transform the \Order entity
     * @param \Order $model
     *
     * @return array
     */
    public function transform(Order $model)
    {
        return [
            'id'         => (int) $model->id,
            'defect'=>$model->defect,
            'status'=>$model->status,
            'priority'=>$model->priority,
            'number_os_sise'=>$model->number_os_sise,
            'name' =>$model->name,
            'phone1' => $model->phone1,
            'phone2' => $model->phone2,
            'type' => $model->type,
            'address' => $model->address,
            'address_number'=>$model->address_number,
            'district' => $model->district,
            'city'=>$model->city,
            'state'=>$model->state,
            'visita'=>$model->visita,
            'plano'=>$model->plano,
            'id_plano'=>$model->id_plano,
            'product'=>$model->product,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }

    protected function getArrayProductNames(Collection $items){
        $names = [];
        foreach ($items as $item){
            $names[] = $item->product->name;
        }
        return $names;
    }


    public function includeCupom(Order $model){
        if (!$model->cupom){
            return null;
        }
            return $this->item($model->cupom, new CupomTransformer());
    }
    public function includeItems(Order $model){
        return $this->collection($model->items,new OrderItemTransformer());
    }

}
