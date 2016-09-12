<?php

namespace CodeDelivery\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function count($id,$status){
        if($status==0){
            return $this->model->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->where('user_deliveryman_id',$id)
                ->where('status','!=',2)->get()->count();
        }elseif($status==2){
            return $this->model->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->where('user_deliveryman_id',$id)->where('status',$status)->get()->count();
        }
    }

    public function countT($id){

            return $this->model->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->where('user_deliveryman_id',$id)->get()->count();

    }

    public function countAnt($id,$status){
        if ($status==0){
            return $this->model->where('created_at', '>=', Carbon::now()->subMonth())
                ->where('user_deliveryman_id',$id)->where('status','!=',2)->get()->count();
        }elseif($status==2){
            return $this->model->where('created_at', '>=', Carbon::now()->subMonth())
                ->where('user_deliveryman_id',$id)->where('status',$status)->get()->count();
        }
    }

    public function countAntT($id){

            return $this->model->where('created_at', '>=', Carbon::now()->subMonth())
                ->where('user_deliveryman_id',$id)->get()->count();

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
