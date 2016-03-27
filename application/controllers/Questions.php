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
                unset($questions[$k]['options'][$kk]['id'], $questions[$k]['options'][$kk]['question_id']);
            }
        }

        $questions = serialize($questions);
        $answers   = serialize($answers);
        $this->cache->redis->save('room_id_'.$room_id.'_questions', $questions, 10*60);
        $this->cache->redis->save('room_id_'.$room_id.'_answers',   $answers, 10*60);
    }

    /**
     * 答卷过程
     */
    public function questionProcess()
    {
        $questions = $this->cache->redis->get('room_id_'.$_SESSION['room_id'].'_questions');
        $questions = unserialize($questions);
var_dump($questions);
        $questions = json_encode($questions);


        exit;
        //从数据库中抓取5道题
        $this->load->view('welcome_message');
    }

    /**
     * 提交答案
     */
    public function answerQuestion()
    {
        //记录答案，返回正确答案
        $params  = $this->uri->uri_to_assoc();
        $user_answer = intval($params['answer']);
        $order       = intval($params['question_id']);
///session_unset(); session_destroy(); exit;
        $answers = $this->cache->redis->get('room_id_'.$_SESSION['room_id'].'_answers');
        $answers = unserialize($answers);
        $answer  = $answers[$order];
//var_dump(time()); var_dump($_SESSION); exit;
        //无此题目
        if(empty($answers[$order]))
        {
            echo '无此题目';
            exit;
        }

        //题目已经回答过
        if(!empty($_SESSION['user_answer'][$order] ))
        {
            echo '题目已经回答过';
            exit;
        }


        //答案比较
        if($user_answer==$answer['answer'])
        {
            $user_answer = array('room_id'=>$_SESSION['room_id'],'user_id'=>$_SESSION['user_id'], 'question_id'=>$answer['question_id'], 'answer'=>$answer['answer'], 'is_true'=>1, 'answer_time'=>time());
            $_SESSION['user_answer'][$order] = $user_answer;
            $result = array('result'=>'succ', 'code'=>1, 'msg'=>'');
        }
        else
        {
            $user_answer = array('room_id'=>$_SESSION['room_id'],'user_id'=>$_SESSION['user_id'], 'question_id'=>$answer['question_id'], 'answer'=>$answer['answer'], 'is_true'=>2, 'answer_time'=>time());
            $_SESSION['user_answer'][$order] = $user_answer;
            $result = array('result'=>'fail', 'code'=>2, 'msg'=>'');
        }

        //如果是最后一道题,则插入数据库
        if(count($answers)==count($_SESSION['user_answer']))
        {
            $this->db->insert_batch('user_answer', $_SESSION['user_answer']);
        }

        echo json_encode($result);
        exit;
    }

    /**
     * 答题排行
     * 处理问题级答案，进行排行, 并把排行保存到room_id中,清空缓存的数据(session,redis)
     */
    public function answerOrder()
    {
        $_SESSION['room_id'];
        $where = 'room_id='.$_SESSION['room_id'];
        $this->db->getAnswers($condition);
    }
}
