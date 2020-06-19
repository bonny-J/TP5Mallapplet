<?php


namespace app\api\controller\v1;
use app\api\service\Token as TokenService;
use app\api\validate\OrderPlace;
use app\api\service\Order as OrderService;


class Order extends BaseController
{
    //用户选择商品后，向API提交包含选择的商品属性信息
    //API接收信息后，检查订单商品库存量
    //有库存：把订单信息存入数据库中，下单成功，告诉客户端可以支付
    //调用支付接口，进行支付
    //再次检查库存量
    //服务器这边可以调用微信的支付接口进行支付
    //微信返回支付结果
    //成功：库存检查，库存量扣除；失败：返回支付失败结果

    //注意，超级管理员不能访问
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder']
    ];

    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');//获取post传来的数组类型
        $uid = TokenService::getCurrentUid();//从缓存读取uid
        $order = new OrderService();
        $status = $order->place($uid,$products);
        return $status;

    }


}