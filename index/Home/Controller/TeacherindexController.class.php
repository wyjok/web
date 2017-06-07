<?php
namespace Home\Controller;
use Think\Controller;
class TeacherindexController extends Controller {
    public function index(){
        //$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
        //$stuid=1133710222;
        if(session('?teacherid')) {
            $teacherid = session('teacherid');

            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);
            $this->assign('teacherid',$teacherid);
            $this->assign('login', $login);
            //$this->assign('title','欢迎'.$login.stuname.'登录');

            dump($login);
            //输出老师角色对应操作（1为导师，1 _ 为工业秘书，1 _ _ 为结题检查组，1_ _ _ 为教学秘书，1 _ _ _ _为审阅人，
            //1 _ _ _ _ _为答辩组）
            if( (int)($login[teacherrole])%10) {
                $this->assign('operation1', '<a href="' . U('/home/Stuendrecord/') . '" title="导师主页">导师主页</a>');
                if( (int)($login[teacherrole])/10%10){
                    $this->assign('operation2', '<a href="' . U('/home/endgrouping/') . '" title="工业实践秘书主页">工业实践秘书主页</a>');
                }
                if( (int)($login[teacherrole])/100%10){
                    $this->assign('operation3', '<a href="' . U('/home/endjudge/') . '" title="结题检查组主页">结题检查组主页</a>');
                }
                if( (int)($login[teacherrole])/1000%10){
                    $this->assign('operation4', '<a href="' . U('/home/reviewgrouping/') . '" title="教学秘书主页">教学秘书主页</a>');
                }
                if( (int)($login[teacherrole])/10000%10){
                    $this->assign('operation5', '<a href="' . U('/home/Stuendrecord/') . '" title="论文审阅人主页">论文审阅人主页</a>');
                }
                if( (int)($login[teacherrole])/100000%10){
                    $this->assign('operation6', '<a href="' . U('/home/Stuendrecord/') . '" title="论文答辩检查组主页">论文答辩检查组主页</a>');
                }

            }

            $this->display();
        }else  {
            $this->error('您好，请先登录！！！',U('/home/teacherlogin/'));
        }
    }

    function quit(){
        session(null);//清空所有session信息
        redirect(U('/home/teacherlogin/'),0, '重新登录');
    }
}