<?php

namespace CodeDelivery\Http\Controllers\Api\Deliveryman;

use CodeDelivery\Events\GetLocationDeliveryman;
use CodeDelivery\Http\Controllers\Controller;
use CodeDelivery\Models\AuxiliaryItems;
use CodeDelivery\Models\Geo;
use CodeDelivery\Models\OrderItem;
use CodeDelivery\Repositories\AuxiliaryItemsRepository;
use CodeDelivery\Repositories\AuxiliaryRepository;
use CodeDelivery\Repositories\OrderRepository;
use CodeDelivery\Repositories\UserRepository;
use CodeDelivery\Services\OrderService;
use DB;
use Illuminate\Http\Request;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;


class DeliverymanCheckoutController extends Controller
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var OrderService
     */
    private $orderService;
    /**
     * @var AuxiliaryItemsRepository
     */
    private $auxiliaryRepository;

    private $repository;

    /**
     * @var DeliverymanAuxiliaryController
     */



    public function  __construct(
        OrderRepository $repository,
        UserRepository $userRepository,
        OrderService $orderService,
        AuxiliaryItemsRepository $auxiliaryRepository

    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->orderService = $orderService;

        $this->auxiliaryRepository = $auxiliaryRepository;
    }

    public function index(){
        $id = Authorizer::getResourceOwnerId();
        $status = 2;
        $orders = $this->repository
            ->skipPresenter(false)
            ->with(['items'])
            ->scopeQuery(function ($query)use($id,$status){
            return $query->where('user_deliveryman_id','=',$id)->where('status','!=',$status);
        })->paginate();

        return $orders;
    }

    public function show($id){
        $idDeliveryman= Authorizer::getResourceOwnerId();

        return $this->repository
            ->skipPresenter(false)
            ->getByIdAndDeliveryman($id,$idDeliveryman);
    }

    public function updateStatus(Request $request,$id){
        $idDeliveryman = Authorizer::getResourceOwnerId();
        $auxiliarys = null;
        if ($request->get('auxiliary')!=null){
            $auxiliarys = $request->get('auxiliary');
        }

        return $this->orderService->updateStatus($id,$idDeliveryman,
            $request->get('status'),
            $request->get('lat'),
            $request->get('long'),
            $request->get('service'),
            $request->get('devolver'),
            $auxiliarys
        );


    }

    public function count(Request $request){
        $idDeliveryman= Authorizer::getResourceOwnerId();
        return $this->repository->skipPresenter(false)->count($idDeliveryman,$request->get('status'));
    }

    public function countT(){
        $idDeliveryman= Authorizer::getResourceOwnerId();
        return $this->repository->skipPresenter(false)->countT($idDeliveryman);
    }

    public function countAntT(){
        $idDeliveryman= Authorizer::getResourceOwnerId();
        return $this->repository->skipPresenter(false)->countAntT($idDeliveryman);
    }

    public function countAnt(Request $request){
        $idDeliveryman= Authorizer::getResourceOwnerId();
        return $this->repository->skipPresenter(false)->countAnt($idDeliveryman,$request->get('status'));
    }

    public function geo(Request $request, Geo $geo, $id){
        $idDeliveryman= Authorizer::getResourceOwnerId();
        $order = $this->repository->getByIDAndDeliveryman($id, $idDeliveryman);
        $geo->lat = $request->get('lat');
        $geo->long = $request->get('long');
        event(new GetLocationDeliveryman($geo,$order));
        return $geo;
    }

    private function itemToArray($products)
    {
        $itemCollection = [];
        foreach ($products as $product) {
            $item = OrderItem::firstOrCreate(['product_id' => $product->id]);
            array_push($itemCollection, $item->id);
        }
        return $itemCollection;
    }
}
