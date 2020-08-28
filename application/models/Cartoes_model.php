<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cartoes_model extends CI_Model
{

    /**
     * author: Leônidas Ferreira
     * email: leonidas.f.silva@hotmail.com
     *
     */

    function __construct()
    {
        parent::__construct();
    }

    function get($table, $fields, $where = '', $id_usuario, $perpage = 0, $start = 0, $one = false, $array = 'array')
    {

        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('vencimento', 'asc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where . ' AND status = 1 AND id_usuario = ' . $id_usuario);
        } else {
            $this->db->where('status = 1 AND id_usuario = ' . $id_usuario);
        }

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getLancamentosAssoc($table, $fields, $id_fatura, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('data_compra', 'id_assoc', 'asc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
            $this->db->where('status', 1);
            $this->db->where('id_fatura', $id_fatura);
        } else {
            $this->db->where('status', 1);
            $this->db->where('id_fatura', $id_fatura);
        }
        $query = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getLancamentos($table, $fields, $id_usuario, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {

        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('id_lancamento', 'asc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
            $this->db->where('status', 1);
            $this->db->where('id_usuario', $id_usuario);
        } else {
            $this->db->where('status', 1);
            $this->db->where('id_usuario', $id_usuario);
        }

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getDetalhesCartao($id_cartao)
    {
        return $this->db
            ->where('id_cartao', $id_cartao)
            ->get('cartoes')
            ->row();
    }

    function getCartoesUsuario($id_usuario, $id_cartao = null)
    {
        if ($id_cartao) {
            return $this->db
                ->where('id_usuario', $id_usuario)
                ->where('id_cartao', $id_cartao)
                ->where('status', 1)
                ->get('cartoes')
                ->result();
        } else {
            return $this->db
                ->where('status', 1)
                ->where('id_usuario', $id_usuario)
                ->or_where('id_usuario_titular', $id_usuario)
                ->where('status', 1)
                ->order_by('id_cartao', 'asc')
                ->get('cartoes')
                ->result();
        }
    }

    function getCartoesAdicionais($id_cartao)
    {
        return $this->db
            ->where('id_cartao_titular', $id_cartao)
            ->where('status', 1)
            ->get('cartoes')
            ->result();
    }

    function getPrimeiroCartaoUsuario($id_usuario)
    {
        return $this->db
            ->where('status', 1)
            ->where('id_usuario', $id_usuario)
            ->order_by('id_cartao')
            ->limit(1)
            ->get('cartoes')
            ->row();
    }

    function consultaFaturasCartao($id_cartao)
    {
        return $this->db
            ->where('status', 1)
            ->where('id_cartao', $id_cartao)
            ->get('faturas')
            ->row();
    }

    function cartaoExistente($id_cartao)
    {
        return $this->db
            ->where('id_cartao', $id_cartao)
            ->where('status', 1)
            ->get('cartoes')
            ->row();
    }

    function verificaCartaoAtivo($id_cartao)
    {
        if ($this->db
            ->where('id_cartao', $id_cartao)
            ->where('status', 1)
            ->get('cartoes')
            ->row()) {
            return true;
        } else {
            return false;
        }
    }

    function cartaoPertenceUsuario($id_usuario, $id_cartao)
    {
        return $this->db
            ->where('id_cartao', $id_cartao)
            ->where('status', 1)
            ->where('id_usuario', $id_usuario)
//            ->or_where('id_usuario_titular', $id_usuario)
            ->get('cartoes')
            ->row();
    }

    function cartaoPertenceTitular($id_usuario, $id_cartao)
    {
        return $this->db
            ->where('id_usuario_titular', $id_usuario)
            ->where('id_cartao', $id_cartao)
            ->where('status', 1)
            ->get('cartoes')
            ->row();
    }

    function getById($id)
    {
        $this->db->where('id_fatura', $id);
        $this->db->limit(1);
        return $this->db->get('faturas')->row();
    }

    function consultarUsuario($cpf)
    {
        return $this->db
            ->where('status', 1)
            ->where('cpf', $cpf)
            ->get('usuarios')
            ->row();
    }

    function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        return false;
    }

    function insert_id($table)
    {
        return $this->db->insert_id($table);
    }

    function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if (($this->db->error()['code'] != 0)) {
            return $this->db->error()['code']['message']; // Or do whatever you gotta do here to raise an error
        } else {
            return true;
        }

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

    function delete_real($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    function count($table, $where)
    {

        $this->db->from($table);
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

}
