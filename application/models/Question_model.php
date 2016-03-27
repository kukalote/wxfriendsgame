<?php
class Question_model extends Base_model{
    public function __construct(){
        parent::__construct();
    }

    public function get_last_ten_entries()
    {
        $query = $this->db->get('question', 10);
        return $query->result_array();
    }

    public function getQuestions()
    {
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
