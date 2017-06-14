<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-3-13
 * Time: 22:37
 */
namespace Home\Controller;
use Think\Controller;

class LoginController extends Controller
{
    public function index()
    {
        //配置页面显示内容
        $this->assign('title','毕业设计系统学生登录');
        $this->display();
    }
    function Login()
    {
        $stulogin=M('Stulogin');//参数的User必须首字母大写，否则自动验证功能失效！
        $stuid=$_POST['id'];
        //$stutoken=md5($_POST['password']);
        $stutoken=$_POST['password'];
        if ( $this->checkVerify($_POST['captcha']) ) {
            //查找输入的用户名是否存在
            if($stulogin->where("stuid ='$stuid' AND stutoken = '$stutoken'")->find()){

                session(stuid,$stuid);
                //$url=U('/home/index/stuid/'.$stuid);
                $url=U('/home/index');
                redirect($url,0, '跳转中...');
            }else{
                $this->error('用户名或密码错误',U('/home/login/'));
            }
        }else{
            $this->error('验证码错误',U('/home/login/'));
        }

    }


    public function verify()
    {
        $config =    array(
            'fontSize'    =>    30,    // 验证码字体大小

        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }
    function checkVerify($code, $id = '')
    {
        $verify = new \Think\Verify();

        return $verify->check($code, $id);
    }

}