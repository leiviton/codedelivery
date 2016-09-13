<?php


namespace CodeDelivery\Services;


use CodeDelivery\Repositories\CupomRepository;
use CodeDelivery\Repositories\OrderRepository;
use CodeDelivery\Repositories\ProductRepository;
use CodeDelivery\Repositories\UserRepository;
use Dmitrovskiy\IonicPush\PushProcessor;

class OrderService{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var CupomRepository
     */
    private $cupomRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var PushProcessor
     */
    private $pushProcessor;
    /**
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        CupomRepository $cupomRepository,
        ProductRepository $productRepository,
        PushProcessor $pushProcessor
    )
    {

        $this->orderRepository = $orderRepository;
        $this->cupomRepository = $cupomRepository;
        $this->productRepository = $productRepository;
        $this->pushProcessor = $pushProcessor;
        $this->userRepository = $userRepository;
    }

    public function create(array $data){

        \DB::beginTransaction();

        try {
            $data['status'] = 0;

            if (isset($data['cupom_id'])){
                unset($data['cupom_id']);
            }
            if (isset($data['cupom_code'])){
                $cupom = $this->cupomRepository->findByField('code',$data['cupom_code'])->first();
                $data['cupom_id'] = $cupom->id;
                $cupom->used = 1;
                $cupom->save();
                unset($data['cupom_code']);
            }
            $items = $data['items'];
            $order = $this->orderRepository->create($data);

            $total = 0;
            foreach ($items as $item){
                $item['price'] = $this->productRepository->find($item['product_id'])->price;
                $order->items()->create($item);
                $total += $item['price'] * $item['qtd'];
            }

            $order->total = $total;

            if (isset($cupom)){
                $order->total = $total - $cupom->value;
            }
            $order->save();

            \DB::commit();

            return $order;
        } catch (\Exception $e){
             \DB::rollback();
            throw $e;
        }
    }

    public function updateStatus($id,$idDeliveryman,$status,$lat,$long,$service=null,$devolver=null,$ax=null){
        $order = $this->orderRepository->getByIDAndDeliveryman($id,$idDeliveryman);
        $order->status = $status;
        $order->flag_sincronizado = 0;
        switch ((int)$status) {
            case 0:
                if($devolver==1){
                    $this->pushProcessor->notify([$order->deliveryman->device_token],[
                        'message'=>"Você devolveu a orderm {$order->number_os_sise} para o PCP"
                    ]);
                    $order->user_deliveryman_id = (int) $devolver;
                    $order->save();
                    break;
                }elseif ($order->visita==null){
                    $hora = date("H:i:s");
                    $data = date("d/m/Y");
                    $this->pushProcessor->notify([$order->deliveryman->device_token],[
                        'message'=>"Você visitou o cliente {$order->name} ás {$hora} do dia {$data}"
                    ]);
                    $order->visita = date("d/m/Y H:i:s");
                    $order->geo_client_no_location = $lat.','.$long;
                }else{
                    $hora = date("H:i:s");
                    $data = date("d/m/Y");
                    $this->pushProcessor->notify([$order->deliveryman->device_token],[
                        'message'=>"Você visitou o cliente {$order->name} ás {$hora} do dia {$data}"
                    ]);
                    $order->visita .= ','.date("d/m/Y H:i:s");
                    $order->geo_client_no_location = $lat.','.$long;
                }
                $order->save();
                break;
            case 1:
                if((int)($order->status == 1 && !$order->hash)){
                    $order->hash = md5((new \DateTime())->getTimestamp());
                }
                $order->geo = $lat.','.$long;
                $order->save();
                break;
            case 2:
                $order->geo_final = $lat.','.$long;
                $order->service = $service;
                $auxiliares = $ax;
                if ($auxiliares!=null) {
                    foreach ($auxiliares as $axs) {
                        $order->auxiliarys()->create($axs);
                    }
                }
                $order->save();
                break;
        }
        return $order;

    }


}

