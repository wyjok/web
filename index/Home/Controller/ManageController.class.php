<?php
/**
 * Created by PhpStorm.
 * User: wyj19
 * Date: 2017-6-15
 * Time: 9:31
 */

namespace Home\Controller;


use Think\Controller;

class manageController extends Controller
{
    public function listteacher(){

    }
    public function passwordset(){

    }
    public function timeset(){
        $timeset=M('timeset');
        $set=$timeset->find('1');
        for ($i = 1; $i<6; $i++) {
            //dump($_POST['group'.$i]);
            if ($set['starttime' . $i]!=null&&$set['finishtime' . $i]!=null) {
                $this->assign('starttime' . $i,$set['starttime' . $i]);
                $this->assign('finishtime' . $i,$set['finishtime' . $i]);
            }

        }
        $this->display();
    }

    public function timeinput()
    {
        $timeset=M('timeset');
        for ($i = 1; $i<6; $i++) {
            //dump($_POST['group'.$i]);
            dump($_POST['starttime' . $i]);
            if (!empty($_POST['starttime' . $i])&&!empty($_POST['finishtime' . $i])){
                if (($_POST['starttime' . $i])<($_POST['finishtime' . $i])){
                    if (empty($_POST['starttime' . ($i+1)])||($_POST['finishtime' . $i])<($_POST['starttime' . ($i+1)]))
                    {
                        $data['starttime' . $i]=$_POST['starttime' . $i];
                        $data['finishtime' . $i]=$_POST['finishtime' . $i];
                        echo '1  ';
                        dump($_POST['starttime' . ($i+1)]);
                        dump($_POST['finishtime' . $i]);
                    }else {
                    $this->error('前一项目结束时间要早于下一项目开始时间',U('/home/manage/timeset'),1);
                    }
                }else{
                    $this->error('起止时间必须为先后顺序',U('/home/manage/timeset'),1);
                }
            }else{
                $this->error('起止时间必须成对录入',U('/home/manage/timeset'),1);
            }


        }
        dump($data);
        $data[key]=1;
        //$timeset->where('1')->del();
        $result=$timeset->save($data);
        if($result>0){
            $this->success('时间设置完成',U('/home/manage/timeset'),1);
        }
        $this->success('未发生没变动',U('/home/manage/timeset'),1);
    }
}