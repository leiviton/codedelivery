<?php

namespace CodeDelivery\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use CodeDelivery\Repositories\ActionRepository;
use CodeDelivery\Models\Action;
use CodeDelivery\Validators\ActionValidator;

/**
 * Class ActionRepositoryEloquent
 * @package namespace CodeDelivery\Repositories;
 */
class ActionRepositoryEloquent extends BaseRepository implements ActionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Action::class;
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
        return \CodeDelivery\Presenters\ActionPresenter::class;
    }
}
