<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Anuncios_model extends CI_Model
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

    function getById($id)
    {
        $this->db->from('usuarios');
        $this->db->select('usuarios.*, permissoes.nome as permissao');
        $this->db->join('permissoes', 'permissoes.idPermissao = usuarios.permissoes_id', 'left');
        $this->db->where('id_usuarios', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    function pesquisar($termo, $id)
    {
        $data = array();
        // buscando clientes
        $this->db->like('nome', $termo);
        $this->db->where('id_usuario', $id);
        $this->db->limit(5);
        $data['clientes'] = $this->db->get('clientes')->result();

        // buscando os
        $this->db->like('idOs', $termo);
        $this->db->where('id_usuario', $id);
        $this->db->limit(5);
        $data['os'] = $this->db->get('os')->result();

        // buscando produtos
        $this->db->like('descricao', $termo);
        $this->db->where('id_usuario', $id);
        $this->db->limit(5);
        $data['produtos'] = $this->db->get('produtos')->result();

        //buscando serviços
        $this->db->like('nome', $termo);
        $this->db->where('id_usuario', $id);
        $this->db->limit(5);
        $data['servicos'] = $this->db->get('servicos')->result();

        return $data;

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

//        if ($this->db->affected_rows() > 0) {
//            return true;
//        }
        return true;
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

    function count($table)
    {
        return $this->db->count_all($table);
    }

    function getAnuncios()
    {
        return $this->db
            ->where('status', 1)
            ->get('anuncios')
            ->result();
    }

    function getDetalhesAnuncio($id_anuncio)
    {
        return $this->db
            ->where('id_anuncio', $id_anuncio)
            ->get('anuncios')
            ->row();
    }

    function autoCompleteUsuario($q)
    {
        $query = $this->db->select('*')
            ->limit(5)
            ->like('nome', $q)
            ->where('status', 1)
            ->get('usuarios');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = array('label' => $row['nome'], 'id' => $row['id_usuarios']);
            }
            echo json_encode($row_set);
        }
    }

}
