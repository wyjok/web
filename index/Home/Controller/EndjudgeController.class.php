<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-3-13
 * Time: 22:38
 */

namespace Home\Controller;
use Think\Controller;

class EndjudgeController extends Controller{
    public function index()
    {
        if (session('?teacherid')) {
            $teacherid = session('teacherid');
            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);
            if( !(int)($login[teacherrole])/100%10){
                $this->error('您好，无此项任务',U('/home/teacherindex/'));
            }
            $stuendassign=M('stuendassign');
            $teacherendassign=M('teacherendassign');
            $stuendjudgement=M('stuendjudgement');
            $stuinfo=M('stuinfo');
            $teachergroup=$teacherendassign->find($teacherid);
            $status['endgroupnum']=$teachergroup['endgroupnum'];
            $judgelist=$stuendassign->where($status)->select();

            foreach ($judgelist as &$list)
            {
                $judgement=$stuendjudgement->find($list['stuid']);
                $name=$stuinfo->find($list['stuid']);
                dump($judgement);
                $list['stuname']=$name['stuname'];
                if($judgement!=null)
                {
                    $list['judgement']=$judgement;
                }

            }


            dump($judgelist);
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
        $stuendassign=M('stuendassign');
        $teacherendassign=M('teacherendassign');
        $stuendjudgement=M('stuendjudgement');

        $teachergroup=$teacherendassign->find($teacherid);
        $stu=$stuendassign->find($stuid);
        if( !(int)($login[teacherrole])/100%10){
            $this->error('您好，无此项任务',U('/home/teacherindex/'));
        }
        if($stu['endgroupnum']!=$teachergroup['endgroupnum'])
            $this->error('您好，组内无此学生！！！',U('/home/endjudge/'));
        $judgement=$stuendjudgement->find($stuid);
        $this->assign('stuid',$stuid);
        if($judgement!=null){
            if($judgement['permission']>0)
                $this->assign('checked1','checked="checked"');
            else
                $this->assign('checked0','checked="checked"');
            $teacher=$teacherinfo->find($judgement['teacherid']);
            $this->assign('comment',$judgement['comment']);

            $this->assign('teacher','(上一次评价老师为'.$teacher['teachername'].')');
        }
        $this->display();
    }

    public function judgementinput($stuid){

        $teacherid = session('teacherid');
        $teacherinfo = M('teacherinfo');
        $login = $teacherinfo->find($teacherid);
        $stuendassign=M('stuendassign');
        $teacherendassign=M('teacherendassign');
        $stuendjudgement=M('stuendjudgement');

        $teachergroup=$teacherendassign->find($teacherid);
        $stu=$stuendassign->find($stuid);
        if( !(int)($login[teacherrole])/100%10){
            $this->error('您好，无此项任务',U('/home/teacherindex/'));
        }
        if($stu['endgroupnum']!=$teachergroup['endgroupnum'])
            $this->error('您好，组内无此学生！！！',U('/home/endjudge/'));
        $data['comment']=$_POST['comment'];
        $data['permission']=$_POST['permission'];
        $data['teacherid']=$teacherid;
        $data['stuid']=$stuid;

        $stuendjudgement->create($data);

        if($stuendjudgement->find($stuid))
            $result=$stuendjudgement->save();
        else
            $result=$stuendjudgement->add();

        if($result){
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('新增成功', U('/home/endjudge/'));
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败',U('/home/endjudge/'));
        }
    }
}

