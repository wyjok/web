<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-3-13
 * Time: 22:38
 */

namespace Home\Controller;
use Think\Controller;

class FinalessayController extends Controller
{
    public function index()
    {
        if(session('?stuid')) {
            $stuid = session('stuid');
            $stuinfo = M('stuinfo');
            $userinfo = $stuinfo->find($stuid);
            $stuessay =M('finalessay');
            $userrecord = $stuessay->find($stuid);
            if($userrecord!=null) {
                $stuendprojname = $userrecord['stuendprojname'];
                $stuendreportname = $userrecord['stuendreportname'];
                $this->assign('stuendprojname', $stuendprojname);
                $this->assign('stuendreportname', $stuendreportname);

                // $stuendfilelocate = '<a href="'.'../../uploads/'.'endreport/'.$stuid.'/'.$userrecord['stuendfilelocate'].'" title="文档下载">下载</a>';
                $stuendfilelocate = '../../uploads/'.'finalessay/'.$stuid.'/'.$userrecord['stuendfilelocate'];
                $encode = mb_detect_encoding($stuendreportname, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
                //dump($encode);
                $filename=$userrecord['stuendfilelocate'];
                if(mb_strlen($filename,$encode)>30){
                    $filename=mb_substr($filename,0,30,$encode)."...";
                }


                $this->assign('filename', $filename);
                //配置页面显示内容
                $this->assign('filetitle', $userrecord['stuendfilelocate']);

            }else{
                $stuendfilelocate = '未提交过文档';
            }
            $this->assign('stuendfilelocate', $stuendfilelocate);

            //$this->assign('operation','<a href="'.U('/home/').'" title="返回">返回</a>');
            $this->display();
        }else  {
            $this->error('您好，请先登录！！！',U('/home/login/'));
        }

    }
    function stuessay()
    {
        if(session('?stuid')) {
            $stuendprojname=$_POST['stuendprojname'];
            $stuendreportname=$_POST['stuendreportname'];
            $stuid = session('stuid');
            $stuinfo = M('stuinfo');
            $userinfo = $stuinfo->find($stuid);
            if($stuendprojname==''||$stuendprojname==''){
                $this->error('请填写所有信息');
            }else{
                $upload = new \Think\Upload();
                $upload->maxSize   =     3145728 ;
                $upload->exts      =     array('docx', 'doc', 'pdf');
                $upload->rootPath  =     './uploads/';
                $upload->savePath  =     'finalessay/';
                $upload->replace   =   true;
                //dump(date("YmdHis", time()));
                $upload->saveName  =  '论文修改版'.$stuid.$userinfo[stuname].'-'.date("YmdHis", time());

                $stuid = session('stuid');

                $upload->subName  =   $stuid;
                $info   =   $upload->upload();

                if(!$info) {
                    $this->error($upload->getError());
                }else{
                    $stuid = session('stuid');

                    $Form = M('finalessay');
                    // 要修改的数据对象属性赋值
                    $data['stuid'] = $stuid;
                    $data['stuendprojname'] = $stuendprojname;
                    $data['stuendreportname'] = $stuendreportname;
//                    dump($stuendprojname);
//                    dump($stuendreportname);
//                    dump($stuid);
//                    dump($info);
//                    dump($info['document']['savename']);
                    //'./uploads/endreport/'.$stuid.'/'.
                    $data['stuendfilelocate'] = $info['document']['savename'];
                    // $result=$Form->where("stuid=$stuid")->save($data);
                   if($Form->find($stuid)){
                       $result=$Form->save($data);
                   }else{
                       $result=$Form->add($data);
                   }
//                    dump($result);
//                    dump($data);
                    $this->success('upload done！','',1);

                    // redirect some where
                }
            }

        }else  {
            $this->error('您好，请先登录！！！',U('/home/login/'));
        }
    }


}
