<?php


namespace app\api\controller\v1;

use app\api\model\Product as ProductModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ProductException;
use think\model\Collection;


class Product
{
    public function getRecent($count = 15){
        (new Count())->goCheck();
        $product = ProductModel::getMostRecent($count);
        if(!$product){
            throw new ProductException();
        }
        $collection = Collection::make($product);
        $product = $collection->hidden(['summary']);
        return $product;
    }

    public function getAllCategories($id){
        (new IDMustBePostiveInt())->goCheck();
        $product = ProductModel::getProductByCategoryID($id);
        if($product->isEmpty()){
            throw new ProductException();
        }
        $product = $product->hidden(['summary']);
        return $product;
    }

    public function getOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }

    public function deleteOne($id){

    }
}