<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-3-13
 * Time: 22:38
 */

namespace Home\Controller;
use Think\Controller;

class StuessayController extends Controller
{
    public function index()
    {
        if(session('?stuid')) {
            $stuid = session('stuid');
            $stuinfo = M('stuinfo');
            $userinfo = $stuinfo->find($stuid);
            $stuessay =M('stuessay');
            $userrecord = $stuessay->find($stuid);
            $time=date("Y-m-d");
            $timeset=M('timeset');
            $set=$timeset->find('1');
            if($time<$set['starttime3']||$time>$set['finishtime3'])
            {
                $this->error('您好，不在可用时间范围内',U('/home/index/'));
            }
            $this->assign('stuendreportname', '初次提交');
            if($userrecord!=null) {

                $mentorresult =M('mentorresult');
                $result=$mentorresult->find($stuid);
                $this->assign('result', $result);

                $stuendprojname = $userrecord['stuendprojname'];
                $stuendreportname = $userrecord['stuendreportname'];
                $this->assign('stuendprojname', $stuendprojname);
                $this->assign('stuendreportname', $stuendreportname);

               // $stuendfilelocate = '<a href="'.'../../uploads/'.'endreport/'.$stuid.'/'.$userrecord['stuendfilelocate'].'" title="文档下载">下载</a>';
                $stuendfilelocate = '../../uploads/'.'essay/'.$stuid.'/'.$userrecord['stuendfilelocate'];
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
                $upload->savePath  =     'essay/';
                $upload->replace   =   true;
                //dump(date("YmdHis", time()));
                $upload->saveName  =  '论文初稿'.$stuid.$userinfo[stuname].'-'.date("YmdHis", time());

                $stuid = session('stuid');

                $upload->subName  =   $stuid;
                $info   =   $upload->upload();

                if(!$info) {
                    $this->error($upload->getError());
                }else{
                    $stuid = session('stuid');

                    $Form = M('stuessay');
                    // 要修改的数据对象属性赋值
                    $data['stuid'] = $stuid;
                    $data['stuendprojname'] = $stuendprojname;
                    $data['stuendreportname'] = $stuendreportname;
                    $data['time'] = date("Y-m-d h:i:sa");
//                    //dump($stuendprojname);
//                    //dump($stuendreportname);
//                    //dump($stuid);
//                    //dump($info);
//                    //dump($info['document']['savename']);
                    //'./uploads/endreport/'.$stuid.'/'.
                    $data['stuendfilelocate'] = $info['document']['savename'];
                   // $result=$Form->where("stuid=$stuid")->save($data);
                    if($Form->find($stuid)){
                        $result=$Form->save($data);
                    }else{
                        $result=$Form->add($data);
                    }
                    //dump($result);
                    //dump($data);
                    $this->success('upload done！','',1);

                    // redirect some where
                }
            }

        }else  {
            $this->error('您好，请先登录！！！',U('/home/login/'));
        }
    }
    public function add($data='',$options=array(),$replace=false) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data           =   $this->data;
                // 重置数据
                $this->data     = array();
            }else{
                $this->error    = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 数据处理
        $data       =   $this->_facade($data);
        // 分析表达式
        $options    =   $this->_parseOptions($options);
        if(false === $this->_before_insert($data,$options)) {
            return false;
        }
        // 写入数据到数据库
        $result = $this->db->insert($data,$options,$replace);
        if(false !== $result && is_numeric($result)) {
            $pk     =   $this->getPk();
            // 增加复合主键支持
            if (is_array($pk)) return $result;
            $insertId   =   $this->getLastInsID();
            if($insertId) {
                // 自增主键返回插入ID
                $data[$pk]  = $insertId;
                if(false === $this->_after_insert($data,$options)) {
                    return false;
                }
                return $insertId;
            }
            if(false === $this->_after_insert($data,$options)) {
                return false;
            }
        }
        return $result;
    }
    public function save($data='',$options=array()) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data           =   $this->data;
                // 重置数据
                $this->data     =   array();
            }else{
                $this->error    =   L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 数据处理
        $data       =   $this->_facade($data);
        if(empty($data)){
            // 没有数据则不执行
            $this->error    =   L('_DATA_TYPE_INVALID_');
            return false;
        }
        // 分析表达式
        $options    =   $this->_parseOptions($options);
        $pk         =   $this->getPk();
        if(!isset($options['where']) ) {
            // 如果存在主键数据 则自动作为更新条件
            if (is_string($pk) && isset($data[$pk])) {
                $where[$pk]     =   $data[$pk];
                unset($data[$pk]);
            } elseif (is_array($pk)) {
                // 增加复合主键支持
                foreach ($pk as $field) {
                    if(isset($data[$field])) {
                        $where[$field]      =   $data[$field];
                    } else {
                        // 如果缺少复合主键数据则不执行
                        $this->error        =   L('_OPERATION_WRONG_');
                        return false;
                    }
                    unset($data[$field]);
                }
            }
            if(!isset($where)){
                // 如果没有任何更新条件则不执行
                $this->error        =   L('_OPERATION_WRONG_');
                return false;
            }else{
                $options['where']   =   $where;
            }
        }

        if(is_array($options['where']) && isset($options['where'][$pk])){
            $pkValue    =   $options['where'][$pk];
        }
        if(false === $this->_before_update($data,$options)) {
            return false;
        }
        $result     =   $this->db->update($data,$options);
        if(false !== $result && is_numeric($result)) {
            if(isset($pkValue)) $data[$pk]   =  $pkValue;
            $this->_after_update($data,$options);
        }
        return $result;
    }
    public function select($options=array()) {
        $pk   =  $this->getPk();
        if(is_string($options) || is_numeric($options)) {
            // 根据主键查询
            if(strpos($options,',')) {
                $where[$pk]     =  array('IN',$options);
            }else{
                $where[$pk]     =  $options;
            }
            $options            =  array();
            $options['where']   =  $where;
        }elseif (is_array($options) && (count($options) > 0) && is_array($pk)) {
            // 根据复合主键查询
            $count = 0;
            foreach (array_keys($options) as $key) {
                if (is_int($key)) $count++;
            }
            if ($count == count($pk)) {
                $i = 0;
                foreach ($pk as $field) {
                    $where[$field] = $options[$i];
                    unset($options[$i++]);
                }
                $options['where']  =  $where;
            } else {
                return false;
            }
        } elseif(false === $options){ // 用于子查询 不查询只返回SQL
            $options['fetch_sql'] = true;
        }
        // 分析表达式
        $options    =  $this->_parseOptions($options);
        // 判断查询缓存
        if(isset($options['cache'])){
            $cache  =   $options['cache'];
            $key    =   is_string($cache['key'])?$cache['key']:md5(serialize($options));
            $data   =   S($key,'',$cache);
            if(false !== $data){
                return $data;
            }
        }
        $resultSet  = $this->db->select($options);
        if(false === $resultSet) {
            return false;
        }
        if(!empty($resultSet)) { // 有查询结果
            if(is_string($resultSet)){
                return $resultSet;
            }

            $resultSet  =   array_map(array($this,'_read_data'),$resultSet);
            $this->_after_select($resultSet,$options);
            if(isset($options['index'])){ // 对数据集进行索引
                $index  =   explode(',',$options['index']);
                foreach ($resultSet as $result){
                    $_key   =  $result[$index[0]];
                    if(isset($index[1]) && isset($result[$index[1]])){
                        $cols[$_key] =  $result[$index[1]];
                    }else{
                        $cols[$_key] =  $result;
                    }
                }
                $resultSet  =   $cols;
            }
        }

        if(isset($cache)){
            S($key,$resultSet,$cache);
        }
        return $resultSet;
    }

}
