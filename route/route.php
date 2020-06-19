<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');

/*模块名，控制器名，操作名*/
Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');
/*主题接口*/
Route::get('api/:version/theme','api/:version.theme/getSimpleList');
Route::get('api/:version/theme/:id','api/:version.theme/getComplexOne');
/*产品接口*/
Route::group('api/:version/product',function (){
    Route::get('/by_category','api/:version.product/getAllCategories');
    Route::get('/:id','api/:version.product/getOne',[],['id' =>'\d+']);
    Route::get('/recent','api/:version.product/getRecent');
});

/*分类接口*/
Route::get('api/:version/category/all','api/:version.category/getAllCategories');
/*token接口*/
Route::get('api/:version/token/test1','api/:version.token/test1');
Route::post('api/:version/token/user','api/:version.token/getToken');
Route::post('api/:version/token/verify','api/:version.token/verifyToken');
Route::post('api/:version/token/gettest','api/:version.token/gettest');

/*订单支付接口*/
Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');

/*地址参数*/
Route::post('api/:version/address','api/:version.address/createOrUpdateAddress');
Route::get('api/:version/address/addretest','api/:version.address/addretest');
Route::get('api/:version/address/word','api/:version.address/word');
/*测试*/
Route::post('api/:version/test/wx','api/:version.test/Wx_GetOpenidByCode');
return [

];
