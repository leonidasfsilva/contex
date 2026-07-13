<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ClientesApi_model extends CI_Model
{
    public function get($limit = 0, $start = 0)
    {
        $this->db
            ->where('status', 1)
            ->order_by('id', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get('clientes_api')->result();
    }

    public function getById($id)
    {
        return $this->db
            ->where('id', $id)
            ->where('status', 1)
            ->get('clientes_api')
            ->row();
    }

    public function usernameExists($username, $ignoreId = null)
    {
        $this->db
            ->where('username', $username)
            ->where('status', 1);

        if ($ignoreId) {
            $this->db->where('id !=', $ignoreId);
        }

        return $this->db->get('clientes_api')->num_rows() > 0;
    }

    public function add($data)
    {
        return $this->db->insert('clientes_api', $data);
    }

    public function edit($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update('clientes_api', $data);
    }

    public function count()
    {
        return $this->db
            ->where('status', 1)
            ->count_all_results('clientes_api');
    }
}
