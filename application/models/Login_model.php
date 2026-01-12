<?php
class Login_model extends MY_Model {
    public function __construct() {
        parent::__construct();
    }
    public $table = "login";
    public $table_id = "usuario";
}
