<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-6-9
 * Time: 21:14
 */

namespace Home\Controller;


use Think\Controller;

class ReviewjudgeController extends Controller
{
    public function index()
    {
        if (session('?teacherid')) {
            $teacherid = session('teacherid');
            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);

            $time=date("Y-m-d");
            $timeset=M('timeset');
            $set=$timeset->find('1');
            if($time<$set['starttime4']||$time>$set['finishtime4'])
            {
                $this->error('您好，不在可用时间范围内',U('/home/teacherindex/'));
            }
            $teacher =M('stureviewassign')->where("teacherid=$teacherid")->select();

            if( $teacher==null){
                $this->error('您好，无此项任务',U('/home/teacherindex/'));
            }

            $stuinfo=M('stuinfo');
            $reviewresult=M('reviewresult');
            $status['teacherid']=$teacherid;
            $stureviewassign=M('stureviewassign');
            $judgelist=$stureviewassign->join('LEFT join stuinfo ON stuinfo.stuid = stureviewassign.stuid')
                ->field('stuinfo.stuid as stuid,stuinfo.stuname as stuname,stureviewassign.teacherid as teacherid')
                ->where("stureviewassign.teacherid=$teacherid")->select();
            //$judgelist=$stuinfo->where("teacherid=$teacherid")->select();
            //echo $stuinfo->_sql();
            foreach ($judgelist as &$list)
            {
                $judgement=$reviewresult->find($list['stuid']);
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
        $reviewresult=M('reviewresult');
        $stuinfo=M('stuinfo')->find($stuid);
        $teacher =M('stureviewassign')->where("teacherid=$teacherid")->select();
        $stureviewassign=M('stureviewassign')->find($stuid);
        if( $teacher==null){
            $this->error('您好，无此项任务',U('/home/reviewjudge/'));
        }
        if($stureviewassign['teacherid']!=$teacherid)
            $this->error('您好，组内无此学生！！！',U('/home/reviewjudge/'));
        $judgement=$reviewresult->find($stuid);
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
        $teacher =M('stureviewassign')->where("teacherid=$teacherid")->select();
        $login = $teacherinfo->find($teacherid);
        $stuinfo=M('stuinfo');
        $reviewresult=M('reviewresult');
        $stu=M('stureviewassign')->find($stuid);
        $stureviewassign=M('stureviewassign')->find($stuid);

        if( $teacher==null){
            $this->error('您好，无此项任务',U('/home/teacherindex/'));
        }
        if($stureviewassign['teacherid']!=$teacherid)
            $this->error('您好，组内无此学生！！！',U('/home/reviewjudge/'));

        $data['comment']=$_POST['comment'];
        $data['permission']=$_POST['permission'];
        $data['teacherid']=$teacherid;
        $data['stuid']=$stuid;
        $data['time'] = date("Y-m-d h:i:sa");

        //dump($data);
        if($reviewresult->find($stuid))
            $result=$reviewresult->save($data);
        else
            $result=$reviewresult->add($data);
        ////echo $studefenseresult->_sql();
        if($result){
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('新增成功', U('/home/reviewjudge/'));
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败',U('/home/reviewjudge/'));
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