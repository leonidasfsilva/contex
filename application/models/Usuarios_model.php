<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Usuarios_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function get($perpage = 0, $start = 0, $one = false)
    {

        $this->db->from('usuarios');
        $this->db->select('usuarios.*, permissoes.nome as permissao');
        $this->db->limit($perpage, $start);
        $this->db->join('permissoes', 'usuarios.permissoes_id = permissoes.idPermissao', 'left');
        $this->db->where('usuarios.status', 1);

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getAllTipos()
    {
        $this->db->where('situacao', 1);
        return $this->db->get('tiposUsuario')->result();
    }

    function getById($id)
    {
        $this->db->where('id_usuarios', $id);
        $this->db->limit(1);
        return $this->db->get('usuarios')->row();
    }

    function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    function delete($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    function count($table)
    {
        return $this->db->count_all($table);
    }

    public function verificaExisteUsuario($id)
    {
        $this->db->where('id_usuarios', $id);
        return $this->db->get('usuarios')->num_rows();
    }

    public function verificaExisteEmail($email)
    {
        $this->db->where('email', $email);
        return $this->db->get('usuarios')->num_rows();
    }

    public function verificaEmailUsuario($email, $id)
    {
        $this->db->where('email', $email);
        $this->db->where('id_usuarios', $id);
        return $this->db->get('usuarios')->num_rows();
    }

    public function getUsuariosAtivos()
    {
        return $this->db
            ->where('ativo', 1)
            ->where('status', 1)
            ->get('usuarios')
            ->result();
    }

    public function getDadosUsuarios()
    {
        return $this->db
            ->get('usuarios')
            ->result();
    }

    public function getLogsUsuario($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->get('logs')
            ->result();
    }

}
