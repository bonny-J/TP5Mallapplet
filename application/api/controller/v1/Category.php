<?php


namespace app\api\controller\v1;
use app\api\model\Category as CategoryModel;

class Category
{
    /*@url category/all */
    public function getAllCategories(){
        $categories = CategoryModel::all([],'img');
        if($categories->isEmpty()){
            throw new CategoryException();
        }
        return $categories;
    }
}