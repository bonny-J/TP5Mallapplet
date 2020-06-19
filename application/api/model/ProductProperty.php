<?php

namespace app\api\model;

use think\Model;

class ProductProperty extends BaseModel
{
    protected $hidden = ['product_id','delete_time','id'];

}
