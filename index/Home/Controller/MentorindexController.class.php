<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-6-9
 * Time: 20:13
 */

namespace Home\Controller;
use Think\Controller;

class MentorindexController extends Controller{
    public function index()
    {
        if (session('?teacherid')) {
            $time=date("Y-m-d");
            $timeset=M('timeset');
            $set=$timeset->find('1');
            if($time<$set['starttime3']||$time>$set['finishtime3'])
            {
                $this->error('您好，不在可用时间范围内',U('/home/teacherindex/'));
            }
            $teacherid = session('teacherid');
            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);
            if( !(int)($login[teacherrole])%10){
                $this->error('您好，无此项任务',U('/home/teacherindex/'));
            }

            $stuinfo=M('stuinfo');
            $mentorresult=M('mentorresult');
            $status['teacherid']=$teacherid;

            $judgelist=$stuinfo->where("teacherid=$teacherid")->select();
            //echo $stuinfo->_sql();
            foreach ($judgelist as &$list)
            {
                $judgement=$mentorresult->find($list['stuid']);
                $name=$stuinfo->find($list['stuid']);
                ////dump($judgement);
                $list['stuname']=$name['stuname'];
                if($judgement!=null)
                {
                    $list['judgement']=$judgement;
                }

            }


            //dump($judgelist);
            $this->assign('list', $judgelist);
            $this->display();


        } else  {
            $this->error('您好，请先登录！！！',U('/home/teacherlogin/'));
        }


    }

    public function judging($stuid){

        $teacherid = session('teacherid');
        $teacherinfo = M('teacherinfo');
        $login = $teacherinfo->find($teacherid);
        $mentorresult=M('mentorresult');
        $stuinfo=M('stuinfo')->find($stuid);
        $time=date("Y-m-d");
        $timeset=M('timeset');
        $set=$timeset->find('1');
        if($time<$set['starttime3']||$time>$set['finishtime3'])
        {
            $this->error('您好，不在可用时间范围内',U('/home/teacherindex/'));
        }

        if( !(int)($login[teacherrole])%10){
            $this->error('您好，无此项任务',U('/home/teacherindex/'));
        }
        if($stuinfo['teacherid']!=$teacherid)
            $this->error('您好，组内无此学生！！！',U('/home/mentorindex/'));
        $judgement=$mentorresult->find($stuid);
        $this->assign('stuid',$stuid);
        $this->assign('stuname',$stuinfo['stuname']);

        $stuessay=M('stuessay')->find($stuid);
        $stuendfilelocate = $stuid.'/../../../../../../uploads/'.'essay/'.$stuid.'/'.$stuessay['stuendfilelocate'];
        $this->assign('proj', "项目名称：".$stuessay[stuendprojname]);
        $this->assign('reason',"改动简述：". $stuessay[stuendreportname]);
        $this->assign('time', "提交时间：".$stuessay['time']);

        $this->assign('download', $stuendfilelocate);



        if($judgement!=null){
            if($judgement['permission']>0){
                $this->assign('checked1','checked="checked"');
                $this->assign('focus1',' focus on');
            }

            else{
                $this->assign('checked0','checked="checked"');
                $this->assign('focus0',' focus on');
            }

            $this->assign('teacher',"上次评价时间"+$judgement['time']);
            $this->assign('comment',$judgement['comment']);

        }
        $this->display();
    }

    public function judgementinput($stuid){

        $teacherid = session('teacherid');
        $teacherinfo = M('teacherinfo');
        $login = $teacherinfo->find($teacherid);
        $stuinfo=M('stuinfo');
        $mentorresult=M('mentorresult');
        $stu=$stuinfo->find($stuid);
        $time=date("Y-m-d");
        $timeset=M('timeset');
        $set=$timeset->find('1');
        if($time<$set['starttime3']||$time>$set['finishtime3'])
        {
            $this->error('您好，不在可用时间范围内',U('/home/teacherindex/'));
        }

        if( !(int)($login[teacherrole])%10){
            $this->error('您好，无此项任务',U('/home/teacherindex/'));
        }
        if($stu['teacherid']!=$teacherid)
            $this->error('您好，组内无此学生！！！',U('/home/mentorindex/'));

        $data['comment']=$_POST['comment'];
        $data['permission']=$_POST['permission'];

        $data['stuid']=$stuid;
        $data['time'] = date("Y-m-d h:i:sa");

        //dump($data);
        if($mentorresult->find($stuid))
            $result=$mentorresult->save($data);
        else
            $result=$mentorresult->add($data);
        //echo $studefenseresult->_sql();
        if($result){
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('评价成功', U('/home/mentorindex/'));
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('评价失败',U('/home/mentorindex/'));
        }
    }
}