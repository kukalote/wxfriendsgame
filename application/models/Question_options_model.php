<?php
class Question_options_model extends Base_model{
    public function __construct(){
        parent::__construct();
    }


    public function getOptionsByWhere($condition)
    {
        $sql = 'select * from wx_question_options where question_id in ('.$condition.')';
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
}
