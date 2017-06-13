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
            $teacherid = session('teacherid');
            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);
            if( !(int)($login[teacherrole])%10){
                $this->error('您好，无此项任务',U('/home/teacherindex/'));
            }

            $stuinfo=M('stuinfo');
            $stumentorresult=M('stumentorresult');
            $status['teacherid']=$teacherid;

            $judgelist=$stuinfo->where($status)->select();

            foreach ($judgelist as &$list)
            {
                $judgement=$stumentorresult->find($list['stuid']);
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
        $stumentorresult=M('stumentorresult');
        $stuinfo=M('stuinfo')->find($stuid);

        if( !(int)($login[teacherrole])%10){
            $this->error('您好，无此项任务',U('/home/teacherindex/'));
        }
        if($stuinfo['teacherid']!=$teacherid)
            $this->error('您好，组内无此学生！！！',U('/home/mentorindex/'));
        $judgement=$stumentorresult->find($stuid);
        $this->assign('stuid',$stuid);
        $this->assign('stuname',$stuinfo['stuname']);

        if($judgement!=null){
            if($judgement['permission']>0){
                $this->assign('checked1','checked="checked"');
                $this->assign('focus1',' focus on');
            }

            else{
                $this->assign('checked0','checked="checked"');
                $this->assign('focus0',' focus on');
            }


            $this->assign('comment',$judgement['comment']);

        }
        $this->display();
    }

    public function judgementinput($stuid){

        $teacherid = session('teacherid');
        $teacherinfo = M('teacherinfo');
        $login = $teacherinfo->find($teacherid);
        $stuinfo=M('stuinfo');
        $stumentorresult=M('stumentorresult');
        $stu=$stuinfo->find($stuid);


        if( !(int)($login[teacherrole])%10){
            $this->error('您好，无此项任务',U('/home/teacherindex/'));
        }
        if($stu['teacherid']!=$teacherid)
            $this->error('您好，组内无此学生！！！',U('/home/defensejudge/'));

        $data['comment']=$_POST['comment'];
        $data['permission']=$_POST['permission'];

        $data['stuid']=$stuid;


        //dump($data);
        if($stumentorresult->find($stuid))
            $result=$stumentorresult->save($data);
        else
            $result=$stumentorresult->add($data);
        //echo $studefenseresult->_sql();
        if($result){
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('新增成功', U('/home/mentorindex/'));
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败',U('/home/mentorindex/'));
        }
    }
}