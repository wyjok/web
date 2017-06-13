<?php
namespace Home\Controller;
use Think\Controller;
class TeachingsecretaryController extends Controller {
    public function index(){
        //$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
        //$stuid=1133710222;
        if(session('?teacherid')) {
            $teacherid = session('teacherid');
            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);


            if( (int)($login[teacherrole])/1000%10){
                $this->display();
            }else{
                $this->error('您好，无此项任务',U('/home/teacherindex/'));
            }

        }else  {
            $this->error('您好，请先登录！！！',U('/home/teacherlogin/'));
        }
    }

    public function teacherfunction(){
        if(session('?teacherid')) {
            $teacherid = session('teacherid');
            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);
            if( (int)($login[teacherrole])/1000%10) {

                    $package['name'] = '论文审阅分组';
                    $package['url'] = U('/home/reviewgrouping/');
                    $infoset[] = $package;


                    $package['name'] = '论文答辩分组';
                    $package['url'] = U('/home/defensegrouping/');
                    $infoset[] = $package;

            }


            $testarr['success']=true;
            $testarr['message']='';
            $testarr['data']=$infoset;
            //echo json_encode($groupsetinfo);
            $this->ajaxReturn($testarr,'JSON');
        }



    }

    function quit(){
        session(null);//清空所有session信息
        redirect(U('/home/teacherlogin/'),0, '重新登录');
    }
}