<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-3-13
 * Time: 22:38
 */

namespace Home\Controller;
use Think\Controller;

class StuendrecordController extends Controller
{
    public function index()
    {
        if(session('?stuid')) {
            $stuid = session('stuid');
            $stuinfo = M('stuinfo');
            $userinfo = $stuinfo->find($stuid);
            $stuendrecord =M('stuendrecord');
            $userrecord = $stuendrecord->find($stuid);
            if($userrecord!=null) {
                $stuendprojname = $userrecord['stuendprojname'];
                $stuendreportname = $userrecord['stuendreportname'];

                $stuendfilelocate = '<a href="'.'../../uploads/'.'endreport/'.$stuid.'/'.$userrecord['stuendfilelocate'].'" title="文档下载">下载</a>';
                $this->assign('stuendprojname', $stuendprojname);
                //配置页面显示内容
                $this->assign('stuendreportname', $stuendreportname);

            }else{
                $stuendfilelocate = '未提交过文档';
            }
            $this->assign('stuendfilelocate', $stuendfilelocate);

            $this->assign('operation','<a href="'.U('/home/').'" title="返回">返回</a>');
            $this->display();
        }else  {
            $this->error('您好，请先登录！！！',U('/home/login/'));
        }

    }
    function stuendrecord()
    {
        if(session('?stuid')) {
            $stuendprojname=$_POST['stuendprojname'];
            $stuendreportname=$_POST['stuendreportname'];

            if($stuendprojname==''||$stuendprojname==''){
                $this->error('请填写所有信息');
            }else{
                $upload = new \Think\Upload();
                $upload->maxSize   =     3145728 ;
                $upload->exts      =     array('docx', 'doc', 'pdf');
                $upload->rootPath  =     './uploads/';
                $upload->savePath  =     'endreport/';
                $upload->replace   =   true;
                dump(date("YmdHis", time()));
                $upload->saveName  = date("YmdHis", time()).'-'.$stuendreportname;

                $stuid = session('stuid');

                $upload->subName  =   $stuid;
                $info   =   $upload->upload();

                if(!$info) {
                    $this->error($upload->getError());
                }else{
                    $stuid = session('stuid');

                    $Form = M('stuendrecord');
                    // 要修改的数据对象属性赋值
                    $data['stuid'] = $stuid;
                    $data['stuendprojname'] = $stuendprojname;
                    $data['stuendreportname'] = $stuendreportname;
                    dump($stuendprojname);
                    dump($stuendreportname);
                    dump($stuid);
                    dump($info);
                    dump($info['document']['savename']);
                    //'./uploads/endreport/'.$stuid.'/'.
                    $data['stuendfilelocate'] = $info['document']['savename'];
                   // $result=$Form->where("stuid=$stuid")->save($data);
                    $result=$Form->save($data);
                    dump($result);
                    dump($data);
                    $this->success('upload done！','',10);

                    // redirect some where
                }
            }

        }else  {
            $this->error('您好，请先登录！！！',U('/home/login/'));
        }
    }


}
