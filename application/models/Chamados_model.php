<?php

class Chamados_model extends CI_Model
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

        if ($this->db->affected_rows() >= 0) {
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

    function count($table)
    {
        return $this->db->count_all($table);
    }

    function getAssuntos()
    {
        return $this->db
            ->where('status', 1)
            ->get('chamados_assuntos')
            ->result();
    }

    function getChamados()
    {
        return $this->db
//            ->where('status', 1)
            ->get('chamados')
            ->result();
    }

    function getChamadosUsuario($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->get('chamados')
            ->result();
    }

    function getNomeUsuario($id_usuario)
    {
        return $this->db
            ->where('id_usuarios', $id_usuario)
            ->get('usuarios')
            ->row('nome');
    }

    function getAvatarUsuario($id_usuario)
    {
        return $this->db
            ->where('id_usuarios', $id_usuario)
            ->get('usuarios')
            ->row('avatar');
    }


    function getDetalhesChamado($id_chamado)
    {
        return $this->db
            ->where('id_chamado', $id_chamado)
            ->get('chamados')
            ->row();
    }

    function verificaChamadoPertenceUsuario($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->get('chamados')
            ->num_rows();
    }

    function getRespostasChamado($id_chamado)
    {
        return $this->db
            ->where('id_chamado', $id_chamado)
            ->get('chamados_respostas')
            ->result();
    }
}
