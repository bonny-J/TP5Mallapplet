<?php


namespace app\api\controller\v1;


use think\Controller;
use app\api\service\Token as TokenService;

class BaseController extends Controller
{
    protected function checkScope(){
        TokenService::needScope();
    }

    protected function checkExclusiveScope(){
        TokenService::needincluseScope();
    }
}