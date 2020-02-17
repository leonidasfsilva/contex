<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Configs_model extends CI_Model
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

    public function getNotificacoesUsuario($id)
    {
        return $this->db
            ->where('id_usuarios', $id)
            ->get('notificacoes')
            ->result();
    }

    public function alterarSenha($id, $newSenha)
    {
        $this->db->set('senha', password_hash($newSenha, PASSWORD_DEFAULT));
        $this->db->where('id_usuarios', $id);
        return $this->db->update('usuarios');

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

    function count($table)
    {
        return $this->db->count_all($table);
    }

    function getOsAbertas()
    {
        $this->db->select('os.*, clientes.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where('os.status', 'Aberto');
        $this->db->limit(10);
        return $this->db->get()->result();
    }

    function getProdutosMinimo()
    {

        $sql = "SELECT * FROM produtos WHERE estoque <= estoqueMinimo LIMIT 10";
        return $this->db->query($sql)->result();

    }

    function getOsEstatisticas()
    {
        $sql = "SELECT status, COUNT(status) as total FROM os GROUP BY status ORDER BY status";
        return $this->db->query($sql)->result();
    }

    public function getEstatisticasFinanceiro()
    {
        $sql = "SELECT SUM(CASE WHEN baixado = 1 AND tipo = 'receita' THEN valor END) as total_receita, 
                       SUM(CASE WHEN baixado = 1 AND tipo = 'despesa' THEN valor END) as total_despesa,
                       SUM(CASE WHEN baixado = 0 AND tipo = 'receita' THEN valor END) as total_receita_pendente,
                       SUM(CASE WHEN baixado = 0 AND tipo = 'despesa' THEN valor END) as total_despesa_pendente FROM lancamentos";
        return $this->db->query($sql)->row();
    }

    public function getEmitente($id_usuario)
    {
        $this->db->where('id_usuario = ' . $id_usuario . ' AND status = ' . 1);
        $this->db->limit(1);
        return $this->db->get('emitente')->row();
    }

    public function getLogoEmitente($id_usuario)
    {
        $this->db->select('logomarca');
        $this->db->from('emitente');
        $this->db->where('id_usuario = ' . $id_usuario . ' AND status = ' . 1);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function editLogo($id, $logo)
    {
        $this->db->set('logomarca', $logo);
        $this->db->where('id_emitente', $id);
        return $this->db->update('emitente');
    }

    public function check_credentials($email)
    {
        $this->db->where('email', $email);
        $this->db->where('status', 1);
        $this->db->limit(1);
        return $this->db->get('usuarios')->row();
    }

    function gravaLog($data)
    {
        $this->db->insert('logs', $data);
        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    public function getAvatarUsuario($id_usuario)
    {
        $this->db->select('avatar');
        $this->db->from('usuarios');
        $this->db->where('id_usuarios = ' . $id_usuario . ' AND status = ' . 1);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function editAvatarUsuario($id, $logo)
    {
        $this->db->set('avatar', $logo);
        $this->db->where('id_usuarios', $id);
        return $this->db->update('usuarios');
    }

    public function excluirAvatarUsuario($id)
    {
        $this->db->set('avatar', null);
        $this->db->where('id_usuarios', $id);
        return $this->db->update('usuarios');
    }

    public function getDadosConfigs()
    {
        return $this->db
            ->get('configs_usuario')
            ->result();
    }

    function registraConfigsUsuario($data)
    {
        $this->db->insert('configs_usuario', $data);
        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    public function getConfigsUsuario($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->limit(1)
            ->get('configs_usuario_assoc')
            ->row();
    }


    public function getWidgetLancamentos($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->where('id_config_opcao', 3)
            ->get('configs_usuario_assoc')
            ->num_rows();
    }

    public function getWidgetCartaoCredito($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->where('id_config_opcao', 4)
            ->get('configs_usuario_assoc')
            ->num_rows();
    }

    public function getWidgetInvestimentos($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->where('id_config_opcao', 5)
            ->get('configs_usuario_assoc')
            ->num_rows();
    }

    public function getWidgetPendencias($id_usuario)
    {
        return $this->db
            ->where('id_usuario', $id_usuario)
            ->where('id_config_opcao', 6)
            ->get('configs_usuario_assoc')
            ->num_rows();
    }

    public function setWidgetLancamentos($id_usuario)
    {
        $this->db
            ->insert('configs_usuario_assoc',
                array(
                    'id_config_opcao' => 3,
                    'id_usuario' => $id_usuario
                )
            );
    }

    public function unsetWidgetLancamentos($id_usuario)
    {
        $this->db
            ->where('id_usuario', $id_usuario)
            ->where('id_config_opcao', 3)
            ->delete('configs_usuario_assoc');
    }

    public function setWidgetCartaoCredito($id_usuario)
    {
        $this->db
            ->insert('configs_usuario_assoc',
                array(
                    'id_config_opcao' => 4,
                    'id_usuario' => $id_usuario
                )
            );
    }

    public function unsetWidgetCartaoCredito($id_usuario)
    {
        $this->db
            ->where('id_usuario', $id_usuario)
            ->where('id_config_opcao', 4)
            ->delete('configs_usuario_assoc');
    }

    public function setWidgetInvestimentos($id_usuario)
    {
        $this->db
            ->insert('configs_usuario_assoc',
                array(
                    'id_config_opcao' => 5,
                    'id_usuario' => $id_usuario
                )
            );
    }

    public function unsetWidgetInvestimentos($id_usuario)
    {
        $this->db
            ->where('id_usuario', $id_usuario)
            ->where('id_config_opcao', 5)
            ->delete('configs_usuario_assoc');
    }

    public function setWidgetPendencias($id_usuario)
    {
        $this->db
            ->insert('configs_usuario_assoc',
                array(
                    'id_config_opcao' => 6,
                    'id_usuario' => $id_usuario
                )
            );
    }

    public function unsetWidgetPendencias($id_usuario)
    {
        $this->db
            ->where('id_usuario', $id_usuario)
            ->where('id_config_opcao', 6)
            ->delete('configs_usuario_assoc');
    }


}
