<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        //$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    	//$stuid=1133710222;
        if(session('?stuid')) {
            $stuid = session('stuid');

            $stuinfo = M('stuinfo');
            $login = $stuinfo->find($stuid);
            $this->assign('stuid',$stuid);
            $this->assign('login', $login);
            //$this->assign('title','欢迎'.$login.stuname.'登录');

            //dump($login);
            //输出学生状态对应操作
//            if( (int)($login[stustate])>10) {
//                $this->assign('operation','可进行的操作') ;
//                $this->assign('operation1', '<a href="' . U('/home/Stuendrecord/') . '" title="结题检查">结题信息录入与查看</a>');
//                if( (int)($login[stustate])>100){
//                    $this->assign('operation1', '<a href="' . U('/home/Stuendrecord/') . '" title="结题检查">结题信息查看</a>');
//                    $this->assign('operation2', '<a href="' . U('/home/Stuendrecord/') . '" title="论文答辩">答辩信息录入与查看</a>');
//                }
//                if( (int)($login[stustate])>1000){
//                    $this->assign('operation1', '<a href="' . U('/home/Stuendrecord/') . '" title="结题检查">结题信息查看</a>');
//                    $this->assign('operation2', '<a href="' . U('/home/Stuendrecord/') . '" title="论文答辩">答辩信息查看</a>');
//                }
//
//            }else{
//                $this->assign('operation','未通过开题') ;
//            }

            $this->display();
        }else  {
            $this->error('您好，请先登录！！！',U('/home/login/'));
        }
    }

    public function stufunction()
    {
        if (session('?stuid')) {
            $stuid = session('stuid');

            $stuinfo = M('stuinfo');
            $login = $stuinfo->find($stuid);
            $f1=M('stuendjudgement')->find($stuid);
            $f2=M('mentorresult')->find($stuid);
            $f3=M('reviewresult')->find($stuid);
            if ((int)($login[stustate]) > 10 ) {
                if((int)($f1[permission])!=1){
                    $package['name'] = '结题信息提交';
                    $package['url'] = U('/home/Stuendrecord/');
                    $infoset[] = $package;
                }

            }else{
                $package['name'] = '未通过开题';
                $package['url'] = U('/home/index');
                $infoset[] = $package;
            }
            if ((int)($f1[permission])>0&&(int)($f2[permission])<1) {
                $package['name'] = '论文信息提交';
                $package['url'] = U('/home/Stuessay/');
                $infoset[] = $package;
            }
            if ((int)($f3[permission])>0) {
                $package['name'] = '论文修改版提交';
                $package['url'] = U('/home/finalessay/');
                $infoset[] = $package;
            }


            $testarr['success'] = true;
            $testarr['message'] = '';
            $testarr['data'] = $infoset;
            //echo json_encode($groupsetinfo);
            $this->ajaxReturn($testarr, 'JSON');
        }
    }

    function quit(){
        session(null);//清空所有session信息
        redirect(U('/home/login/'),0, '重新登录');
    }
}