<?php


namespace app\api\controller\v1;


use app\api\validate\IDCollection;
USE app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePostiveInt;
use think\Exception;

class Theme
{
    /*
     * @url /theme/:id
     * @return 一组theme模型
     */
    public function getSimpleList($ids=''){
        (new IDCollection())->batch()->goCheck();
        $ids = explode(',',$ids);
        $result = ThemeModel::with('topicImg,headImg')
            ->select($ids);
        if(!$result){
            throw new ThemeException();
        }
        return $result;
    }

    public function getComplexOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $theme = ThemeModel::getThemeWithProducts($id);
        if(!$theme){
            throw new Exception();
        }
        return $theme;
    }
}