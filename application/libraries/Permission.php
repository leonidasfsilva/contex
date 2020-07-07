<?php  if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Permission Class
 *
 * Biblioteca para controle de permissões
 *
 * @author      Ramon Silva
 * @copyright           Copyright (c) 2013, Ramon Silva.
 * @since       Version 1.0
 * v... Visualizar
 * e... Editar
 * d... Deletar ou Desabilitar
 * a... Adicionar ou Cadastrar
 */



class Permission
{
    var $Permission = array();
    var $table = 'permissoes_assoc';//Nome tabela onde ficam armazenadas as permissões
    var $pk = 'id_permissao';// Nome da chave primaria da tabela
    var $select = 'atividade';// Campo onde fica o array de permissoes.

    public function __construct()
    {
        log_message('debug', "Permission Class Initialized");
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function checkPermission($idPermissao = null, $atividade = null)
    {
        if ($idPermissao == null || $atividade == null) {
            return false;
        }
        // Se as permissões não estiverem carregadas, requisita o carregamento
        if ($this->Permission == null) {
            // Se não carregar retorna falso
            if (!$this->loadPermission($idPermissao)) {
                return false;
            }
        }

        if (is_array($this->Permission)) {
            foreach ($this->Permission as $p) {
                // compara a atividade requisitada com a permissão.
                if ($p == $atividade) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    private function loadPermission($id = null)
    {
        if ($id != null) {
            $this->CI->db->select($this->select);
            $this->CI->db->where($this->pk, $id);
//            $this->CI->db->limit(1);
            $array = $this->CI->db->get($this->table)->result();

            if ($array) {
                foreach ($array as $a) {
                    //Atribui as permissoes ao atributo permission
                    $this->Permission[] = $a->atividade;
                }
//                $array = unserialize($array[$this->select]);
                return true;
            }
            return false;
        }
        return false;
    }
}
