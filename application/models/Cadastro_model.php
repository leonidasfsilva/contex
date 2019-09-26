<?php

class Cadastro_model extends CI_Model
{

    /**
     * author: Leônidas Ferreira
     * email: leonidas.f.silva@hotmail.com
     *
     */

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

    public function alterarSenha($newSenha, $oldSenha, $id)
    {

        $this->db->where('id_usuarios', $id);
        $this->db->limit(1);
        $usuario = $this->db->get('usuarios')->row();

        if (password_verify($oldSenha, $usuario->senha)) {
            $this->db->set('senha', password_hash($newSenha, PASSWORD_DEFAULT));
            $this->db->where('id_usuarios', $id);
            return $this->db->update('usuarios');
        } else {
            return false;
        }


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

    function verificaEmailExistente($email)
    {
        $this->db->from('usuarios');
        $this->db->where('email', $email);
        $this->db->where('status', 1);

        if($this->db->count_all_results() == 0) {
            return false;
        } else {
            return true;
        }
    }

    function gravaPreCadastro($data)
    {
        $this->db->insert('pre_cadastro', $data);
        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function gravaValidacao($data)
    {
        $this->db->insert('validacao_conta', $data);
        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function registraUsuario($data)
    {
        $this->db->insert('usuarios', $data);
        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function validaTokenById($id)
    {
        $this->db
            ->where('id_validacao', $id)
            ->where('status', 1);
        return $this->db->get('validacao_conta');
    }

    function validaPreCadastro($id)
    {
        $this->db
            ->set('validado', 1)
            ->where('id_pre_cadastro', $id)
            ->update('pre_cadastro');
    }

    function verificaValidadeToken($id)
    {
        $this->db
            ->select('*, TIMESTAMPDIFF(MINUTE , data_solicitacao, CURRENT_TIMESTAMP) AS validade')
            ->where('id_validacao', $id);
        return $this->db->get('validacao_conta');
    }

    function invalidaToken($id)
    {
        $this->db
            ->set('status', 0)
            ->where('id_validacao', $id)
            ->update('validacao_conta');
    }

    function getPreCadastroById($id)
    {
        $this->db->where('id_pre_cadastro', $id);
        return $this->db->get('pre_cadastro');
    }

    function atualizaAdmin($id, $data)
    {
        $this->db->where('id_usuarios', $id);
        $this->db->update('usuarios', $data);
    }

    public function getEmitente($id_usuario)
    {
        $this->db->where('id_usuario', $id_usuario);
        return $this->db->get('emitente')->result();
    }

    public function addEmitente($nome, $cnpj, $ie, $logradouro, $numero, $bairro, $cidade, $uf, $telefone, $email, $logo)
    {

        $this->db->set('nome', $nome);
        $this->db->set('cnpj', $cnpj);
        $this->db->set('ie', $ie);
        $this->db->set('rua', $logradouro);
        $this->db->set('numero', $numero);
        $this->db->set('bairro', $bairro);
        $this->db->set('cidade', $cidade);
        $this->db->set('uf', $uf);
        $this->db->set('telefone', $telefone);
        $this->db->set('email', $email);
        $this->db->set('url_logo', $logo);
        return $this->db->insert('emitente');
    }

    public function editEmitente($id, $nome, $cnpj, $ie, $logradouro, $numero, $bairro, $cidade, $uf, $telefone, $email)
    {

        $this->db->set('nome', $nome);
        $this->db->set('cnpj', $cnpj);
        $this->db->set('ie', $ie);
        $this->db->set('rua', $logradouro);
        $this->db->set('numero', $numero);
        $this->db->set('bairro', $bairro);
        $this->db->set('cidade', $cidade);
        $this->db->set('uf', $uf);
        $this->db->set('telefone', $telefone);
        $this->db->set('email', $email);
        $this->db->where('id', $id);
        return $this->db->update('emitente');
    }

    public function editLogo($id, $logo)
    {

        $this->db->set('url_logo', $logo);
        $this->db->where('id', $id);
        return $this->db->update('emitente');

    }

    public function check_credentials($email)
    {
        $this->db->where('email', $email);
        $this->db->where('situacao', 1);
        $this->db->limit(1);
        return $this->db->get('usuarios')->row();
    }
}
