<?php

namespace CodeDelivery\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use CodeDelivery\Models\Order;
use CodeDelivery\Validators\OrderValidator;

/**
 * Class OrderRepositoryEloquent
 * @package namespace CodeDelivery\Repositories;
 */
class OrderRepositoryEloquent extends BaseRepository implements OrderRepository
{
    protected $skipPresenter = true;

    public function getByIdAndDeliveryman($id,$idDeliveryman){
        $result = $this->model->where('id',$id)
            ->where('user_deliveryman_id',$idDeliveryman)
            ->where('status','!=',2)
            ->first();
        if ($result){
            return $this->parserResult($result);
        }
        throw (new ModelNotFoundException())->setModel($this->model());
    }

    public function getByIdAndClient($id,$idClient){
        $result = $this->model->where('id',$id)
            ->where('client_id',$idClient)
            ->first();
        if ($result){
            return $this->parserResult($result);
        }
        throw (new ModelNotFoundException())->setModel($this->model());
    }

    public function countM($id,$status){

            return $this->model->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->where('user_deliveryman_id',$id)
                ->where('status',$status)->get()->count();
    }

    public function countD($id,$status){

            return $this->model->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('user_deliveryman_id',$id)->where('status',$status)->get()->count();

    }


    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Order::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function presenter()
    {
        return \CodeDelivery\Presenters\OrderPresenter::class;
    }

}
