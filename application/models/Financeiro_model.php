<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Financeiro_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get($table, $fields, $where = null, $id_usuario, $limit = null, $rows, $perpage = 0, $start = 0, $order_by = null, $one = false)
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->limit($perpage, $start);

        if ($where) {
            $this->db->where($where . ' AND status = 1 AND id_usuario = ' . $id_usuario);
        } else {
            $this->db->where('status = 1 AND id_usuario = ' . $id_usuario);
        }

        if ($order_by) {
            if (is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            } else {
                $this->db->order_by('data_lancamento', $order_by);
            }
        }

        if ($limit) {
            if ($rows > $limit) {
                $this->db->limit($limit, ($rows - $limit));
            } else {
                $this->db->limit($limit, $start);
            }
        }

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

    function count($table, $id_usuario, $where = null, $limit = null)
    {
        $this->db->from($table);
        if ($where) {
            $this->db->where($where . ' AND status = 1 AND id_usuario = ' . $id_usuario);
        } else {
            $this->db->where('status = 1 AND id_usuario = ' . $id_usuario);
        }
        return $this->db->count_all_results();
    }

    function countLancamentos($id_usuario, $where = null, $limit = null, $start = null)
    {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->where('id_usuario', $id_usuario);
        $this->db->where('status', 1);
        if ($limit) {
            $this->db->limit($limit, $start);
        }
        return $this->db->count_all_results('lancamentos');
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

    function pesquisa($termo, $id)
    {
        //buscando lancamentos
        $value = $this->db->escape_like_str($termo);
        $this->db->where("(descricao LIKE '%$value%' || cliente_fornecedor LIKE '%$value%')");
        $this->db->where('status', 1);
        $this->db->where('id_usuario', $id);
        //        $this->db->limit(5);
        return $this->db->get('lancamentos')->result();
    }
}
