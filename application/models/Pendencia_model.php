<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pendencia_model extends CI_Model
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


    function get($table, $fields, $where = '', $id_usuario, $limit = null, $rows, $perpage = 0, $start = 0, $one = false, $array = 'array')
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
                $this->db->limit($limit, ($rows - $limit));
//                $this->db->order_by('id_pendencia', 'asc');
            }
        }
        $this->db->order_by('quitado', 'desc');
        $this->db->order_by('id_pendencia', 'asc');
        $this->db->order_by('data_vencimento', 'asc');
        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getById($id)
    {
        $this->db->where('id_pendencia', $id);
        $this->db->limit(1);
        return $this->db->get('pendencias')->row();
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

    function count($table, $where)
    {

        $this->db->from($table);
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    function getPendenciasParcialCredito($id_usuario, $id_cliente = null, $where = null)
    {
        if ($where == null) {
            if (!$id_cliente == null) {
                $this->db
                    ->select('SUM(valor) AS total')
                    ->from('pendencias')
                    ->where('status = 1 AND tipo = 1 AND quitado = 0 AND id_usuario  = ' . $id_usuario . ' AND id_cliente = ' . $id_cliente);
            } else {
                $this->db
                    ->select('SUM(valor) AS total')
                    ->from('pendencias')
                    ->where('status = 1 AND tipo = 1 AND quitado = 0 AND id_usuario  = ' . $id_usuario);
            }

        } else {
            if (!$id_cliente == null) {
                $this->db
                    ->select('SUM(valor) AS total')
                    ->from('pendencias')
                    ->where($where . ' AND status = 1 AND tipo = 1 AND quitado = 0 AND id_usuario  = ' . $id_usuario . ' AND id_cliente = ' . $id_cliente);
            } else {
                $this->db
                    ->select('SUM(valor) AS total')
                    ->from('pendencias')
                    ->where($where . ' AND status = 1 AND tipo = 1 AND quitado = 0 AND id_usuario  = ' . $id_usuario);
            }
        }
        return $this->db->get()->row();
    }

    function getPendenciasParcialDebito($id_usuario, $id_cliente = null, $where = null)
    {
        if ($where == null) {
            if (!$id_cliente == null) {
                $this->db
                    ->select('SUM(valor) AS total')
                    ->from('pendencias')
                    ->where('status = 1 AND tipo = 2 AND quitado = 0 AND id_usuario  = ' . $id_usuario . ' AND id_cliente = ' . $id_cliente);
            } else {
                $this->db
                    ->select('SUM(valor) AS total')
                    ->from('pendencias')
                    ->where('status = 1 AND tipo = 2 AND quitado = 0 AND id_usuario  = ' . $id_usuario);
            }
        } else {
            if (!$id_cliente == null) {
                $this->db
                    ->select('SUM(valor) AS total')
                    ->from('pendencias')
                    ->where($where . ' AND status = 1 AND tipo = 2 AND quitado = 0 AND id_usuario  = ' . $id_usuario . ' AND id_cliente = ' . $id_cliente);
            } else {
                $this->db
                    ->select('SUM(valor) AS total')
                    ->from('pendencias')
                    ->where($where . ' AND status = 1 AND tipo = 2 AND quitado = 0 AND id_usuario  = ' . $id_usuario);
            }
        }
        return $this->db->get()->row();
    }

    function getPendenciasTotalCredito($id_usuario)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('pendencias')
            ->where('status = 1 AND tipo = 1 AND quitado = 0 AND id_usuario  = ' . $id_usuario);

        return $this->db->get()->row();
    }

    function getPendenciasTotalDebito($id_usuario)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('pendencias')
            ->where('status = 1 AND tipo = 2 AND quitado = 0 AND id_usuario  = ' . $id_usuario);

        return $this->db->get()->row();
    }

    function getTotalDebito($id_usuario)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('pendencias')
            ->where('status = 1 AND tipo = 2 AND quitado = 0 AND id_usuario  = ' . $id_usuario);
        return $this->db->get()->row();

    }

    function getClientes($id_usuario)
    {
        $one = false;
        $this->db->where('status = 1 AND id_usuario = ' . $id_usuario);
        $query = $this->db->get('clientes');

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }
}
