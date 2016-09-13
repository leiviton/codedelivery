<?php

namespace CodeDelivery\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrderRepository
 * @package namespace CodeDelivery\Repositories;
 */
interface OrderRepository extends RepositoryInterface
{
    public function getByIDAndDeliveryman($id,$idDeliveryman);

    public function getByIdAndClient($id,$idClient);

    public function insertAux($auxiliary,$id);
}
