<?php


namespace app\api\model;


class Category extends BaseModel
{
    public function img(){
        return $this->belongsTo('Image','topic_img_id','id');
    }
}