<?php
namespace Home\Controller;
use Think\Controller;
class TeacherindexController extends Controller {
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

            //dump($login);
            //输出老师角色对应操作（1为导师，1 _ 为工业秘书，1 _ _ 为结题检查组，1_ _ _ 为教学秘书，1 _ _ _ _为审阅人，
            //1 _ _ _ _ _为答辩组）
//            if( (int)($login[teacherrole])%10) {
//                $this->assign('operation1', '<a href="' . U('/home/Stuendrecord/') . '" title="导师主页">导师主页</a>');
//                if( (int)($login[teacherrole])/10%10){
//                    $this->assign('operation2', '<a href="' . U('/home/endgrouping/') . '" title="工业实践秘书主页">工业实践秘书主页</a>');
//                }
//                if( (int)($login[teacherrole])/100%10){
//                    $this->assign('operation3', '<a href="' . U('/home/endjudge/') . '" title="结题检查组主页">结题检查组主页</a>');
//                }
//                if( (int)($login[teacherrole])/1000%10){
//                    $this->assign('operation4', '<a href="' . U('/home/reviewgrouping/') . '" title="教学秘书主页">教学秘书主页</a><a href="' . U('/home/defensegrouping/') . '" title="教学秘书主页">答辩</a>');
//                }
//                if( (int)($login[teacherrole])/10000%10){
//                    $this->assign('operation5', '<a href="' . U('/home/Stuendrecord/') . '" title="论文审阅人主页">论文审阅人主页</a>');
//                }
//                if( (int)($login[teacherrole])/100000%10){
//                    $this->assign('operation6', '<a href="' . U('/home/Stuendrecord/') . '" title="论文答辩检查组主页">论文答辩检查组主页</a>');
//                }
//
//            }


            $this->display();
        }else  {
            $this->error('您好，请先登录！！！',U('/home/teacherlogin/'));
        }
    }

    public function teacherfunction(){
        if(session('?teacherid')) {
            $teacherid = session('teacherid');
            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);

            if( (int)($login[teacherrole])%10) {
                $package['name']='导师主页';
                $package['url']=U('/home/mentorindex/');
                $infoset[]=$package;
            }
            if( (int)($login[teacherrole])/10%10){
                $package['name']='工业实践秘书主页';
                $package['url']=U('/home/endgrouping/');
                $infoset[]=$package;            }
            if( (int)($login[teacherrole])/100%10){
                $package['name']='结题检查组主页';
                $package['url']=U('/home/endjudge/');
                $infoset[]=$package;            }
            if( (int)($login[teacherrole])/1000%10){
                $package['name']='教学秘书主页';
                $package['url']=U('/home/teachingsecretary/') ;
                $infoset[]=$package;            }
            if( (int)($login[teacherrole])/10000%10){
                $package['name']='论文审阅人主页';
                $package['url']=U('/home/reviewjudge/') ;
                $infoset[]=$package;            }
            if( (int)($login[teacherrole])/100000%10){
                $package['name']='论文答辩检查组主页';
                $package['url']=U('/home/defensejudge/');
                $infoset[]=$package;            }


            $testarr['success']=true;
            $testarr['message']='';
            $testarr['data']=$infoset;
            //echo json_encode($groupsetinfo);
            $this->ajaxReturn($testarr,'JSON');
        }



    }

    function quit(){
        session(null);//清空所有session信息
        redirect(U('/home/teacherlogin/'),0, '重新登录');
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