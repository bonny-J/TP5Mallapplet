<?php


namespace app\api\controller\v1;

use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePostiveInt;
use think\Exception;

class Banner
{
    /*
     * 获取指定的id的banner信息
     * @id banner的id号
     * @url /banner/:id
     * @http GETs
    */
    public function getBanner($id)
    {
        $result = (new IDMustBePostiveInt())->goCheck();
        $banner = BannerModel::getBannerByID($id);
        if(!$banner){
            throw new Exception('内部错误');
        }
        return json_encode($banner);
    }
}