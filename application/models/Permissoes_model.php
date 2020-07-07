<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Permissoes_model extends CI_Model
{


    /**
     * author: Ramon Silva
     * email: silva018-mg@yahoo.com.br
     *
     */
    
    function __construct()
    {
        parent::__construct();
    }

    
    function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
            $this->db->where('status', 1);
        } else {
            $this->db->where('status', 1);
        }
        
        $query = $this->db->get();
        
        $result =  !$one  ? $query->result() : $query->row();
        return $result;
    }

    function getActive($table, $fields)
    {
        
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
        ;
    }

    function getById($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        return $this->db->get('permissoes')->row();
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
    
    function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
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

    function count($table)
    {
        return $this->db->count_all($table);
    }

    function getAtividades($id)
    {
        $this->db->select('atividade');
        $this->db->where('id_permissao', $id);
        return $this->db->get('permissoes_assoc')->result();
    }

}

/* End of file permissoes_model.php */
/* Location: ./application/models/permissoes_model.php */
