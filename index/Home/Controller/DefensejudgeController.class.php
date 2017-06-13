<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-6-9
 * Time: 21:13
 */

namespace Home\Controller;


use Think\Controller;

class DefensejudgeController extends Controller
{

    public function index()
    {
        if (session('?teacherid')) {
            $teacherid = session('teacherid');
            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);
            if( !(int)($login[teacherrole])/100000%10){
                $this->error('您好，无此项任务',U('/home/teacherindex/'));
            }
            $studefenseassign=M('studefenseassign');
            $teacherdefenseassign=M('teacherdefenseassign');
            $studefenseresult=M('studefenseresult');
            $stuinfo=M('stuinfo');
            $teachergroup=$teacherdefenseassign->find($teacherid);
            $status['defensegroupnum']=$teachergroup['defensegroupnum'];
            $judgelist=$studefenseassign->where($status)->select();

            foreach ($judgelist as &$list)
            {
                $judgement=$studefenseresult->find($list['stuid']);
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
        $studefenseassign=M('studefenseassign');
        $teacherdefenseassign=M('teacherdefenseassign');
        $studefenseresult=M('studefenseresult');
        $stuinfo=M('stuinfo')->find($stuid);
        $teachergroup=$teacherdefenseassign->find($teacherid);
        $stu=$studefenseassign->find($stuid);
        if( !(int)($login[teacherrole])/100000%10){
            $this->error('您好，无此项任务',U('/home/teacherindex/'));
        }
        if($stu['defensegroupnum']!=$teachergroup['defensegroupnum'])
            $this->error('您好，组内无此学生！！！',U('/home/defensejudge/'));
        $judgement=$studefenseresult->find($stuid);
        $this->assign('stuid',$stuid);
        $this->assign('stuname',$stuinfo['stuname']);
        if($judgement!=null){
            $this->assign('checked'.$judgement['permission'],'checked="checked"');
            $this->assign('focus'.$judgement['permission'],'focus on');

            $teacher=$teacherinfo->find($judgement['teacherid']);
            $comment=$judgement['comment'];
            $this->assign('comment',$comment);



            $this->assign('teacher','(上一次评价老师为'.$teacher['teachername'].')');
        }
        $this->display();
    }

    public function judgementinput($stuid){

        $teacherid = session('teacherid');
        $teacherinfo = M('teacherinfo');
        $login = $teacherinfo->find($teacherid);
        $studefenseassign=M('studefenseassign');
        $teacherdefenseassign=M('teacherdefenseassign');
        $studefenseresult=M('studefenseresult');

        $teachergroup=$teacherdefenseassign->find($teacherid);
        $stu=$studefenseassign->find($stuid);
        if( !(int)($login[teacherrole])/100%10){
            $this->error('您好，无此项任务',U('/home/teacherindex/'));
        }
        if($stu['defensegroupnum']!=$teachergroup['defensegroupnum'])
            $this->error('您好，组内无此学生！！！',U('/home/defensejudge/'));
        $data['comment']=$_POST['comment'];
        $data['permission']=$_POST['permission'];
        $data['teacherid']=$teacherid;
        $data['stuid']=$stuid;


        //dump($data);
        if($studefenseresult->find($stuid))
            $result=$studefenseresult->save($data);
        else
            $result=$studefenseresult->add($data);
        //echo $studefenseresult->_sql();
        if($result){
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('新增成功', U('/home/defensejudge/'));
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败',U('/home/defensejudge/'));
        }
    }
}