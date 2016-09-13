<?php

namespace CodeDelivery\Presenters;

use CodeDelivery\Transformers\ActionTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ActionPresenter
 *
 * @package namespace CodeDelivery\Presenters;
 */
class ActionPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ActionTransformer();
    }
}
