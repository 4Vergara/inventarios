<?php
class Usuarios_model extends MY_Model {
    public function __construct() {
        parent::__construct();
    }
    public $table = "usuarios";
    public $table_id = "id_usuario";

    /**
     * ? Funcion para traer los datos de un usuario por correo electrónico
     * @param string $correo
     * @return object|null
     */
    public function usuario_correo($correo) {
        return $this->db->select('u.*, r.nombre AS rol')
            ->from("$this->table AS u")
            ->join('roles AS r', 'u.id_rol = r.id', 'left')
            ->where('u.correo', $correo)
            ->get($this->table)
            ->row();
    }
}
