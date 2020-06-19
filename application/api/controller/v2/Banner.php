<?php


namespace app\api\controller\v2;

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

        return 'this is v2 version';
    }
}