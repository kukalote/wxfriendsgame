<?php
class User_answer_model extends Base_model{
    public function __construct(){
        parent::__construct();
    }

    public function getAnswers($fields, $condition, $limit=0)
    {
        if(empty($limit))
        {
            $limit = '';
        }
        else
        {
            $limit = ' limit '. $limit;
        }

        $sql = 'SELECT '.$fields.' FROM wx_user_answer WHERE 1 AND '.$condition.' '.$limit;
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    public function sortOrder($condition)
    {

        $sql = ' SELECT user_id, count( * ) num '.
               ' FROM `wx_user_answer` '.
               ' WHERE 1 '.$condition.
               ' AND is_true =1 '.
               ' GROUP BY user_id ';

        $query  = $this->db->query($sql);
        $result = $query->result_array();


        return $result;






        $questions = $this->get_last_ten_entries();
        foreach($questions as $v)
        {
            $qids .= ','.$v['id'];
        }
        $condition = ltrim($qids, ',');
        $this->load->model('question_options_model');
        $options = $this->question_options_model->getOptionsByWhere($condition);

        foreach($questions as $key=>$question)
        {
            $questions[$key]['options'] = array();
            foreach($options as $option)
            {
                if($option['question_id']==$question['id'])
                {
                    $questions[$key]['options'][] = $option;
                }
            }
        }
        return $questions;
    }
}
