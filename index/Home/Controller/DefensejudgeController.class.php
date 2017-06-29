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
            $time=date("Y-m-d");
            $timeset=M('timeset');
            $set=$timeset->find('1');
            if($time<$set['starttime5']||$time>$set['finishtime5'])
            {
                $this->error('您好，不在可用时间范围内',U('/home/teacherindex/'));
            }
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