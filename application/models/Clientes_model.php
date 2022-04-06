<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Clientes_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function get($table, $fields, $where = '', $id_usuario, $perpage = 0, $start = 0, $one = false, $array = 'array')
    {

        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('nome', 'asc');
        //        $this->db->order_by('id_clientes', 'asc');
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

    function getById($id)
    {
        $this->db->where('id_clientes', $id);
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

    function count($table)
    {
        return $this->db->count_all($table);
    }

    function countClientesUsuario()
    {
        $this->db->from('clientes');
        $this->db->where('id_usuario', getUserId());
        $this->db->where('status', 1);
        return $this->db->count_all_results();
    }

    public function getOsByCliente($id)
    {
        $this->db->where('id_cliente', $id);
        $this->db->order_by('idOs', 'desc');
        $this->db->limit(10);
        return $this->db->get('os')->result();
    }

    public function getPendenciasByCliente($id)
    {
        $this->db->where('id_cliente', $id);
        $this->db->where('status', 1);
        $this->db->order_by('data_vencimento', 'asc');
        $this->db->order_by('id_pendencia', 'asc');
        return $this->db->get('pendencias')->result();
    }

    public function verificaClienteUsuario($id, $id_usuario)
    {
        $this->db->where('id_clientes = ' . $id . ' AND id_usuario = ' . $id_usuario . ' AND status = 1');
        return $this->db->get('clientes')->num_rows();
    }

    function getPendenciasCreditoCliente($id_usuario, $id_cliente)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('pendencias')
            ->where('status = 1 AND tipo = 1 AND quitado = 0 AND id_usuario  = ' . $id_usuario . ' AND id_cliente =' . $id_cliente);

        return $this->db->get()->row();
    }

    function getPendenciasDebitoCliente($id_usuario, $id_cliente)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('pendencias')
            ->where('status = 1 AND tipo = 2 AND quitado = 0 AND id_usuario  = ' . $id_usuario . ' AND id_cliente =' . $id_cliente);

        return $this->db->get()->row();
    }

    function autoCompleteCliente($q, $id_usuario)
    {
        $query = $this->db->select('*')
            ->limit(5)
            ->like('nome', $q)
            ->where('id_usuario', $id_usuario)
            ->where('status', 1)
            ->get('clientes');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = array('label' => $row['nome'], 'id' => $row['id_clientes']);
            }
            echo json_encode($row_set);
        }
    }

    public function getClientName($clientId)
    {
        $query = $this->db->select('nome')
            ->where('id_clientes', $clientId)
            ->get('clientes');

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
}
