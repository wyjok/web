<?php
namespace Home\Controller;
use Think\Controller;
class EndgroupingController extends Controller {
    public function index(){
        //$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
        //$stuid=1133710222;



        if(session('?teacherid')) {
            $time=date("Y-m-d");
            $timeset=M('timeset');
            $set=$timeset->find('1');
            if($time>$set['starttime2']||$time<$set['finishtime1'])
            {
                $this->error('您好，不在可用时间范围内',U('/home/teacherindex/'));
            }
            $teacherid = session('teacherid');

            $teacherinfo = M('teacherinfo');
            $login = $teacherinfo->find($teacherid);
            //$this->assign('teacherid',$teacherid);
            //$this->assign('login', $login);
            //$this->assign('title','欢迎'.$login.stuname.'登录');
            if(!($login[teacherrole]/10%10))
                $this->quit();
            ////dump($login);

            $teacherendassign = M('teacherendassign');

            $groupnum=$teacherendassign->distinct(true)->field('endgroupnum')->select();
            ////echo $teacherendassign->_sql();

            if($groupnum==null)
                redirect(U('/home/Endgrouping/teachergrouping'),2, '请先进行检查组老师分组');

           // $this->assign('groupnum', $groupnum);

            $stuinfo = M('stuinfo');
            $stulist = $stuinfo->select();
            //$this->assign('stulist',$stulist);

            ////dump($stulist);

            $this->display();
            //redirect(U('/home/Endgrouping/index'),0, '请先进行检查组老师分组');
        }else  {
            $this->error('您好，请先登录！！！',U('/home/teacherlogin/'));
        }
    }
    public function groupinput(){
        $time=date("Y-m-d");
        $timeset=M('timeset');
        $set=$timeset->find('1');
        if($time>$set['starttime2']||$time<$set['finishtime1'])
        {
            $this->error('您好，不在可用时间范围内',U('/home/teacherindex/'));
        }
        $stuinfo = M('stuendassign');
        $stugroup=(array)($_POST['postData']);
        ////dump($stugroup);
        foreach ($stugroup as $stu){
            $temp['stuid']=$stu['stuId'];
            $del[]=$stu['stuId'];
            $temp['endgroupnum']=$stu['groupId'];
            $inputlist[]= $temp;
        }
        ////dump($inputlist);

        $stuinfo->delete(implode(',',$del));
//        //echo $stuinfo->_sql();
        $stuinfo->addAll($inputlist);
    }
    public function groupinginfo(){

        $stuinfo = M('stuinfo');

//        //dump($data);
//        print_r($data);
        ////dump($data['page']);
        ////dump($data['size']);
        //
        $stulist = $stuinfo->page($_GET['page'],$_GET['size'])->join('LEFT join stuendassign ON stuinfo.stuid = stuendassign.stuid')
            ->field('stuinfo.stuid as stuId,stuinfo.stuname as stuName,stuendassign.endgroupnum as groupId')->where('stuinfo.stustate>10')
            ->order('stuinfo.stuid')->select();

        ////echo M('stuinfo')->_sql();
        $testarr['success']=true;
        $testarr['message']='';
        $testarr['data']=$stulist;
        ////dump($stulist);
        ////echo json_encode($stulist);

        $this->ajaxReturn($testarr,'JSON');
    }
    public function totalNum(){
        $stuinfo = M('stuinfo');
        $totalNum = $stuinfo->count();
        $testarr['success']=true;
        $testarr['message']='';
        $testarr['totalNum']=$totalNum;
        $this->ajaxReturn($testarr,'JSON');

    }
    public function groupsetinfo(){
        $teacherendassign = M('teacherendassign');
        $groupnum=$teacherendassign->distinct(true)->field('endgroupnum')->select();
        foreach($groupnum as $number){
            $teacherinfo = M('teacherinfo');
            $teacherids=$teacherendassign->where('endgroupnum='.$number['endgroupnum'])->getField('teacherid',true);
            $data['groupName'] = implode(',',$teacherinfo->where(array('teacherid'=>array('in',$teacherids)))->getField('teachername',true));
            $data['groupId']=$number['endgroupnum'];
            $groupsetinfo[]=$data;
        }
        $testarr['success']=true;
        $testarr['message']='';
        $testarr['data']=$groupsetinfo;

        ////echo json_encode($groupsetinfo);
        $this->ajaxReturn($testarr,'JSON');
    }
    public function teachergrouping(){
        $time=date("Y-m-d");
        $timeset=M('timeset');
        $set=$timeset->find('1');
        if($time>$set['starttime2']||$time<$set['finishtime1'])
        {
            $this->error('您好，不在可用时间范围内',U('/home/teacherindex/'));
        }
        $teacherendassign = M('teacherendassign');
        $groupnum=0;
        $groupnum=$teacherendassign->distinct(true)->field('endgroupnum')->select();
        ////dump($groupnum);
        if($groupnum!=0){
            $teacherinfo = M('teacherinfo');
            foreach($groupnum as $number){
                ////dump($number);
                $teacherids=$teacherendassign->where('endgroupnum='.$number['endgroupnum'])->getField('teacherid',true);
               // //echo M('teacherendassign')->_sql();

                ////dump($teacherids);

                $groupteachername[$number['endgroupnum']] = implode('|',$teacherinfo->where(array('teacherid'=>array('in',$teacherids)))->getField('teachername',true));
                ////echo M('teacherinfo')->_sql();
                ////dump($groupteachername);

            }
            ////dump($groupteachername);
            $this->assign('groupteachername',$groupteachername);
            $this->assign('number',6);
        }
        $this->display();
    }
    public function teachergroupinput(){
        $time=date("Y-m-d");
        $timeset=M('timeset');
        $set=$timeset->find('1');
        if($time>$set['starttime2']||$time<$set['finishtime1'])
        {
            $this->error('您好，不在可用时间范围内',U('/home/teacherindex/'));
        }
        for($i=1;;$i++){
            //dump($_POST['group'.$i]);
            if(!empty($_POST['group'.$i])){
                $groupteacher[$i]=explode('|',$_POST['group'.$i]);

            }
            else{
                break;
            }

        }
        //dump($groupteacher);
        $teacherinfo=M('teacherinfo');
        $t=0;
        $teacherendassign=M('teacherendassign');
        foreach($groupteacher as $k=>$group){
            foreach($group as $i=>$name){
                $dataList[$t]['endgroupnum'] =$k;
                $st['teachername']=$name;

                $tid=$teacherinfo->where($st)->find();
                //dump($tid);
                //echo M('teacherinfo')->_sql();
                if($tid['teacherid']==0){
                    $da['teachername']= $st['teachername'];
                    $da['teacherrole']='0000100';
                    $teacherinfo->create($da);
                    $tid['teacherid']=$teacherinfo->add($da);
                   //echo 's';
                    //dump($tid);
                }
                if($tid['teacherrole']/100%10!=1){
                    $da1['teachername']= $st['teachername'];
                    $da1['teacherrole']=$tid['teacherrole']+100;
                    $da1['teacherid']=$tid['teacherid'];
                    $teacherinfo->create($da1);
                    $teacherinfo->save($da1);
                    //echo 'u';
                    //dump($tid);
                }

                $dataList[$t++]['teacherid'] =$tid['teacherid'];


            }
        }
       // //dump($dataList);

        $endgroup=M('endgroup');
        $endgroup->where('1')->delete();
        for($i=0;$i<$k;$i++){
            $data['endgroupnum']=$i+1;
            $endgroup->add($data);
        }

        $teacherendassign->where('1')->delete();
        $result=$teacherendassign->addAll($dataList);


//        //echo M('teacherinfo')->_sql();
        if($result>0){
            $this->success('教师分组完成，进入学生分组',U('/home/endgrouping/'),1);
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