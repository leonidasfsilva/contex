<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Financeiro_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get($table, $fields, $where = '', $id_usuario, $limit = null, $rows = null, $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->limit($perpage, $start);

        if ($where) {
            $this->db->where($where . ' AND status = 1 AND id_usuario = ' . $id_usuario);
        } else {
            $this->db->where('status = 1 AND id_usuario = ' . $id_usuario);
        }

        if ($limit) {
            if ($rows > $limit) {
                $this->db->order_by('data_lancamento', 'asc');
                $this->db->limit($limit, ($rows - $limit));
            }
        }

        $this->db->order_by('data_lancamento', 'asc');
        $query = $this->db->get();
        $result = !$one ? $query->result() : $query->row();

        return $result;
    }


    function getById($id)
    {
        $this->db->where('idClientes', $id);
        $this->db->limit(1);
        return $this->db->get('clientes')->row();
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

        if ($this->db->affected_rows() >= 0) {
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

    function count($table, $where, $id_usuario)
    {
        $this->db->from($table);
        if ($where) {
            $this->db->where($where . ' AND status = 1 AND id_usuario = ' . $id_usuario);
        } else {
            $this->db->where('status = 1 AND id_usuario = ' . $id_usuario);
        }
        return $this->db->count_all_results();
    }

    function getSaidasPendentes($id_usuario)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('lancamentos')
            ->where('status = 1 AND tipo = 2 AND baixado = 0 AND id_usuario = ' . $id_usuario);

        return $this->db->get()->row();

    }

    function getEntradasPendentes($id_usuario)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('lancamentos')
            ->where('status = 1 AND tipo = 1 AND baixado = 0 AND id_usuario = ' . $id_usuario);

        return $this->db->get()->row();

    }

    function getTotal($id_usuario)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('lancamentos')
            ->where(' baixado = 1 AND status = 1 AND id_usuario = ' . $id_usuario);

        return $this->db->get()->row();
    }

    function getTotalProvisorio($id_usuario)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('lancamentos')
            ->where('status = 1 AND id_usuario = ' . $id_usuario);

        return $this->db->get()->row();
    }

    function getFormasPagamento()
    {
        $one = false;
        $this->db->where('status = 1');
        $query = $this->db->get('formas_pagamento');

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }
}
