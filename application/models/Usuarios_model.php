<?php
class Usuarios_model extends MY_Model {
    public function __construct() {
        parent::__construct();
    }
    public $table = "usuarios";
    public $table_id = "id_usuario";
}
