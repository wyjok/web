<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-3-16
 * Time: 23:34
 */

namespace Home\Controller;


class Teacherlogin extends Controller
{
    public function index()
    {
        //配置页面显示内容
        $this->assign('title','毕业设计系统教师登录');
        $this->display();
    }
    function login()
    {
        $login=M('Stulogin');//参数的User必须首字母大写，否则自动验证功能失效！
        $stuid=$_POST['stuid'];
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
                $this->error('用户名或密码错误');
            }
        }else{
            $this->error('验证码错误');
        }

    }


    public function verify()
    {
        $Verify = new \Think\Verify();
        $Verify->entry();
    }
    function checkVerify($code, $id = '')
    {
        $verify = new \Think\Verify();

        return $verify->check($code, $id);
    }

}