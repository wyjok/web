<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-3-16
 * Time: 23:34
 */

namespace Home\Controller;
use Think\Controller;

class TeacherloginController extends Controller
{
    public function index()
    {
        //配置页面显示内容
        $this->assign('title','毕业设计系统教师登录');
        $this->display();
    }
    function Teacherlogin()
    {
        $stulogin=M('Teacherlogin');//参数的User必须首字母大写，否则自动验证功能失效！
        $teacherid=$_POST['id'];
        //$stutoken=md5($_POST['password']);
        $teachertoken=$_POST['password'];
        if ( $this->checkVerify($_POST['captcha']) ) {
            //查找输入的用户名是否存在
            if($stulogin->where("teacherid ='$teacherid' AND teachertoken = '$teachertoken'")->find()){

                session(teacherid,$teacherid);
                //$url=U('/home/index/stuid/'.$stuid);
                $url=U('/home/Teacherindex');
                redirect($url,0, '跳转中...');
            }else{
                $this->error('用户名或密码错误',U('/home/teacherlogin/'));
            }
        }else{
            $this->error('验证码错误',U('/home/teacherlogin/'));
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