<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    /*进行图片地址拼接*/
    public function prefixImgUrl($value, $data)
    {
        $finalUrl = $value;
        if ($data['from'] == 1) {
            $finalUrl = config('setting.img_prefix') . $value;
        }
        return $finalUrl;
    }
}
