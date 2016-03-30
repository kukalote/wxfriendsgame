<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED); 

class Questions extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->load->driver('cache');
        $this->load->model('question_model');
        $this->load->model('user_answer_model');
    }

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    /**
     * 将题目保存到redis中
     */
    public function index()
    {
        $now = time();
        $params = $this->uri->uri_to_assoc();
        $room_id = intval($params['room_id']);
        $_SESSION['room_id'] = $room_id;

        $answers   = array();
        $questions = $this->question_model->getQuestions();
        //var_dump($questions);
        foreach($questions as $k=>$question)
        {
            $answers[$k]['answer']      = $question['answer'];
            $answers[$k]['question_id'] = $question['id'];
            if(empty($questions[$k]['options'])) continue;
            //去除json中多余字段
            unset($questions[$k]['answer'], $questions[$k]['id']);
            foreach($questions[$k]['options'] as $kk=>$option)
            {
                if($answers[$k]['answer']==$option['id'])
                {
                    $answers[$k]['order'] = $kk+1;
                }
                $questions[$k]['options'][$kk]['order'] = $kk+1;
                unset($questions[$k]['options'][$kk]['id'], $questions[$k]['options'][$kk]['question_id']);
            }
            //var_dump($questions[$k]['options']);
            shuffle($questions[$k]['options']);
        }
        //var_dump($questions, $answers);

        $questions = serialize($questions);
        $answers   = serialize($answers);
        $this->cache->redis->save('room_id_'.$room_id.'_questions', $questions, 10*60);
        $this->cache->redis->save('room_id_'.$room_id.'_answers',   $answers, 10*60);
        $this->load->view('room');
    }

    /**
     * 答卷过程
     */
    public function questionProcess()
    {
        $data = array();
        $questions = $this->cache->redis->get('room_id_'.$_SESSION['room_id'].'_questions');
        $questions = unserialize($questions);
        $questions = json_encode($questions);
        $data['questions'] = $questions;
        //从数据库中抓取5道题
        $this->load->view('question_process', $data);
    }

    /**
     * 提交答案
     */
    public function answerQuestion()
    {
        //记录答案，返回正确答案
        $params  = $this->uri->uri_to_assoc();
        $user_answer = intval($params['answer']);
        $question    = intval($params['question']);
///session_unset(); session_destroy(); exit;
        $answers = $this->cache->redis->get('room_id_'.$_SESSION['room_id'].'_answers');
        $answers = unserialize($answers);
        $answer  = $answers[$question]['order'];

        $code    = 2;           //失败
        $reason  = '成功';
        $data    = array();
//var_dump(time()); var_dump($_SESSION); exit;
        while(1)
        {
            //无此题目
            if(empty($answers[$question]))
            {
                $reason = '无此题目';
                break;
            }

            //题目已经回答过
            if(!empty($_SESSION['user_answer'][$question] ))
            {
                $reason = '题目已经回答过';
                //break;
            }


            //答案比较
            if($user_answer==$answer)
            {
                $user_answer = array('room_id'=>$_SESSION['room_id'],'user_id'=>$_SESSION['user_id'], 'question_id'=>$answer['question_id'], 'answer'=>$answer['answer'], 'is_true'=>1, 'answer_time'=>time());
                $_SESSION['user_answer'][$question] = $user_answer;
                $data = array('answer'=>$answer, 'is_true'=>1);         //如果失败, 正确答案是。。。
            }
            else
            {
                $user_answer = array('room_id'=>$_SESSION['room_id'],'user_id'=>$_SESSION['user_id'], 'question_id'=>$answer['question_id'], 'answer'=>$answer['answer'], 'is_true'=>2, 'answer_time'=>time());
                $_SESSION['user_answer'][$question] = $user_answer;
                $code = 2;
                $data = array('answer'=>$answer, 'is_true'=>2);         //如果失败, 正确答案是。。。
            }

            //如果是最后一道题,则插入数据库
            if(count($answers)==count($_SESSION['user_answer']))
            {
                $this->db->insert_batch('user_answer', $_SESSION['user_answer']);
            }
            break;
        }


        $result = array('data'=>$data, 'code'=>$code, 'msg'=>$reason);
        echo json_encode($result);
        exit;
    }

    /**
     * 答题排行
     * 处理问题级答案，进行排行, 并把排行保存到room_id中,清空缓存的数据(session,redis)
     */
    public function answerOrder()
    {
        $right_answer_num = 0;
        $all_money        = 100;

        //清空session 和 redis
        $this->cache->redis->delete('room_id_'.$_SESSION['room_id'].'_questions');
        $this->cache->redis->delete('room_id_'.$_SESSION['room_id'].'_answers');
        session_unset();
        session_destroy();

        //查询数据
        $where  = ' AND room_id='.$_SESSION['room_id'];
        $result = $this->user_answer_model->sortOrder($where);

        $all_right_answer = array_column($result, 'num');
        $right_answer_num = array_sum($all_right_answer);   //正确答案总和

        foreach($result as $k=>$v)
        {
            $result[$k]['percent'] = round(($v['num']*100)/$right_answer_num);
            $result[$k]['money']   = round(($all_money*$result[$k]['percent']))/100;
        }



        var_dump($result);
        exit;

    }
}
