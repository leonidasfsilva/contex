<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Consumo_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('codegen_helper'));
    }

    function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }


    function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == 1) {
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

    function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    function countConsumoUsuario($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->where('status', 1)
            ->count_all_results('consumo');
    }

    function getConfigsConsumo($id_usuario)
    {

        return $this->db
            ->where('id_usuario', $id_usuario)
            ->get('configs_consumo_assoc')
            ->row();
    }

    function getConsumoUsuario($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->where('status', 1)
            ->order_by('data_leitura', 'desc')
            ->get('consumo')
            ->row();
    }

    function getConsumosUsuario($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->where('status', 1)
            ->order_by('data_leitura', 'desc')
            ->get('consumo')
            ->result();
    }

    function getConsumoByID($id)
    {
        return $this->db
            ->where('id', $id)
            ->where('status', 1)
            ->get('consumo')
            ->row();
    }
}
