<?php
class Base_model extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
        #mysql_query("SET NAMES GBK"); //·ÀÖ¹ÖÐÎÄÂÒÂë
    }

}
