<?php

namespace Lsshu\Site\Api\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ControllerSubject
{
    /***
     * @return mixed
     */
    public function getCollectionResource();

    /***
     * @return mixed
     */
    public function getModelResource();
}
