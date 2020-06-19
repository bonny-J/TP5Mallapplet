<?php

namespace app\api\model;

class BannerItem extends BaseModel
{
    /*隐藏字段*/
    protected $hidden = ['id', 'img_id', 'banner_id', 'update_time', 'delete_time'];

    /*一对一关联模型*/
    public function img()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}
