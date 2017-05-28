<?php
namespace Home\Controller;
use Think\Controller;
class EndgroupingController extends Controller {
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
            if(!($login[teacherrole]/10%10))
                $this->quit();
            dump($login);

            $teacherendassign = M('teacherendassign');

            $groupnum=$teacherendassign->distinct(true)->field('endgroupnum')->select();
            echo $teacherendassign->_sql();

            if($groupnum==0)
                redirect(U('/home/Endgrouping/teachergrouping'),2, '请先进行检查组老师分组');

            $this->assign('groupnum', $groupnum);

            $stuinfo = M('stuinfo');
            $stulist = $stuinfo->select();
            $this->assign('stulist',$stulist);

            dump($stulist);

            $this->display();
        }else  {
            $this->error('您好，请先登录！！！',U('/home/teacherlogin/'));
        }
    }
    public function groupinginfo(){
        $stuinfo = M('stuinfo');
        $data1=$_POST['data'];
        $data = json_decode($data1);
//        dump($data);
//        print_r($data);
        $data=(array)$data;
        //dump($data['page']);
        //dump($data['size']);
        //
        $stulist = $stuinfo->page($data['page'],$data['size'])->join('LEFT join stuendassign ON stuinfo.stuid = stuendassign.stuid')
            ->field('stuinfo.stuid,stuinfo.stuname,stuendassign.endgroupnum')
            ->order('stuinfo.stuid')->select();
        //dump($stulist);
        $this->ajaxReturn($stulist,'JSON');
    }
    public function groupsetinfo(){
        $teacherendassign = M('teacherendassign');
        $groupnum=$teacherendassign->distinct(true)->field('endgroupnum')->select();
        foreach($groupnum as $number){
            $teacherinfo = M('teacherinfo');
            $teacherids=$teacherendassign->where('endgroupnum='.$number['endgroupnum'])->getField('teacherid',true);
            $groupsetinfo[$number['endgroupnum']]['teachername'] = implode(',',$teacherinfo->where(array('teacherid'=>array('in',$teacherids)))->getField('teachername',true));
            $groupsetinfo[$number['endgroupnum']]['groupnum']=$number['endgroupnum'];
        }
        $this->ajaxReturn($groupsetinfo,'JSON');
    }
    public function teachergrouping(){
        $teacherendassign = M('teacherendassign');
        $groupnum=0;
        $groupnum=$teacherendassign->distinct(true)->field('endgroupnum')->select();
        dump($groupnum);
        if($groupnum!=0){
            $teacherinfo = M('teacherinfo');
            foreach($groupnum as $number){
                dump($number);
                $teacherids=$teacherendassign->where('endgroupnum='.$number['endgroupnum'])->getField('teacherid',true);
                echo M('teacherendassign')->_sql();

                dump($teacherids);


                $groupteachername[$number['endgroupnum']] = $teacherinfo->where(array('teacherid'=>array('in',$teacherids)))->getField('teachername',true);
                echo M('teacherinfo')->_sql();
                dump($groupteachername);

            }
            dump($groupteachername);
            $this->assign('groupteachername',$groupteachername);
            $this->assign('number',6);
        }
        $this->display();
    }
    public function teachergroupinput(){

        for($i=1;;$i++){
            dump($_POST['g'.$i]);
            if(!empty($_POST['g'.$i])){
                $groupteacher[$i]=explode('|',$_POST['g'.$i]);

            }
            else{
                break;
            }

        }
        dump($groupteacher);
        $teacherinfo=M('teacherinfo');
        $t=0;
        $teacherendassign=M('teacherendassign');
        foreach($groupteacher as $k=>$group){
            foreach($group as $i=>$name){
                $dataList[$t]['endgroupnum'] =$k;
                $st['teachername']=$name;

                $tid=$teacherinfo->where($st)->find();
                dump($tid);
                echo M('teacherinfo')->_sql();
                if($tid['teacherid']==0){
                    $da['teachername']= $st['teachername'];
                    $da['teacherrole']='0000100';
                    $teacherinfo->create($da);
                    $tid['teacherid']=$teacherinfo->add($da);
                   echo 's';
                    dump($tid);
                }
                if($tid['teacherrole']/100%10!=1){
                    $da1['teachername']= $st['teachername'];
                    $da1['teacherrole']=$tid['teacherrole']+100;
                    $da1['teacherid']=$tid['teacherid'];
                    $teacherinfo->create($da1);
                    $teacherinfo->save($da1);
                    echo 'u';
                    dump($tid);
                }

                $dataList[$t++]['teacherid'] =$tid['teacherid'];


            }
        }
        dump($dataList);
        $teacherendassign->where('1')->delete();
        $teacherendassign->addAll($dataList);
        echo M('teacherinfo')->_sql();
    }

    function quit(){
        session(null);//清空所有session信息
        redirect(U('/home/teacherlogin/'),0, '重新登录');
    }
}