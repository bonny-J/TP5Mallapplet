<?php


namespace app\api\service;


use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order
{
    //客户端传过来的商品参数
    protected $oProducts;
    //从数据库查询出来的商品参数
    protected $products;
    protected $uid;

    public function place($uid,$oProducts){
        //商品参数跟数据库商品参数匹配
        //将products从数据库里查询出来
        $this->oProducts = $oProducts;
        $this->products = $this->getProductByOrder($oProducts);//该products可能包含多个商品信息
        $this->uid = $uid;
        $status = $this->getOrderStatus();//获取订单库存状态
        if(!$status['pass']){
            $status['order_id'] = -1;
            return $status;
        }

        //开始创建订单【快照】
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);//接收到自定义订单号，创建时间，订单id
        $order['pass'] = true;
        return $order;
    }

    //生成订单数据
    private function createOrder($snap){
        Db::startTrans();//事务开启
        try{$orderNo = $this->makeOrderNo();//自定义订单号
        $order = new OrderModel();
        $order->user_id = $this->uid;
        $order->order_no = $orderNo;
        $order->total_price = $snap['orderPrice'];
        $order->total_count = $snap['totalCount'];
        $order->snap_img = $snap['snapImg'];
        $order->snap_name = $snap['snapName'];
        $order->snap_address = $snap['snapAddress'];
        $order->snap_items = json_encode($snap['pStatus']);
        $order->save();

        $orderID = $order->id;
        $create_time = $order->create_time;
        foreach ($this->oProducts as &$p) {
            $p['order_id'] = $orderID;
        }
        $orderProduct = new OrderProduct();
        $orderProduct->saveAll($this->oProducts);
        Db::commit();
        return [
            'order_no' => $orderNo,
            'order_id' => $orderID,
            'create_time' => $create_time
        ];
        }catch (Exception $ex) {
            Db::rollback();
            throw $ex;
        }

    }

    //生成随机订单号
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    //生成订单快照,保存当前状态
    private function snapOrder($status){
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => '',
            'snapName' => '',
            'snapImg' => ''
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];//用于保存商品详细信息
        $snap['snapAddress'] = json_encode($this->getUserAddress());//这里将地址存储为字段
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapName'] = $this->products[0]['mian_img_url'];

        if(count($this->products)>1){
            $snap['snapName'] .= '等';
        }
    }

    //查询地址信息
    private function getUserAddress(){
        $userAddress = UserAddress::where('uid_id','=',$this->uid)->find();
        if(!$userAddress){
            throw new UserException([
                'msg' => '用户收获地址不存在，下单失败',
                'errorCode' => 60001
            ]);
        }
    }

    //订单商品状态
    public function getOrderStatus(){
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatusArray' =>[] //用于保存商品详细信息
        ];

        foreach($this->oProducts as $oProduct){
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'],$oProduct['count'],$this->products
            );
            if(!$pStatus['haveStock']){
                $status['pass'] =false;//如果订单中有一个商品失败，则整体失败，
                }
                $status['totalCount'] += $pStatus['count'];
                $status['orderPrice'] += $pStatus['totalPrice'];
                array_push($status['pStatusArray'],$pStatus);

        }
        return $status;
    }
    //用于判断提交的订单信息和已经从数据库查询出来的商品信息进行匹配判断属性
    private function getProductStatus($oPID,$ocount,$products){
        $pIndex = -1;

        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' =>0,
            'name' =>'',
            'totalPrice' => 0
        ];//保存的是一个订单下的商品详情信息

        for($i=0;$i<count($products);$i++){
            if($oPID == $products[$i]['id']){
                $pIndex = $i;
            }
        }
        if($pIndex == -1){
            //客户端传递的product_id可能根本不存在
            throw new OrderException([
                'msg' => 'id为'.$oPID.'商品不存在，创建订单失败'
            ]);
        }else{
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['count'] = $product['count'];
            $pStatus['name'] = $product['name'];
            $pStatus['totalPrice'] = $product['price']*$ocount;
            if($product['stock'] - $ocount >=0){
                $pStatus['haveStock'] = true;
            }

        }
        return $pStatus;
    }

    //根据传参的订单信息查询数据库里的商品信息
    private function getProductByOrder($oProducts){
        //读取product_id封装成数组
        //查询数据库
        $oPIDs = [];
        foreach ($oProducts as $item){
            array_push($oPIDs,$item['product_id']);
        }
        $products = Product::all($oPIDs)
            ->visible(['id','stock','name','main_img_url'])
            ->toArray();
        return $products;
    }

    //订单库存量检查状态
    public function checkOrderStock($orderID)
    {
                if (!$orderID)
                {
                    throw new Exception('没有找到订单号');
                }

        $oProducts = OrderProduct::where('order_id', '=', $orderID)
            ->select();
        $this->products = $this->getProductByOrder($oProducts);
        $this->oProducts = $oProducts;
        $status = $this->getOrderStatus();
        return $status;
    }
}