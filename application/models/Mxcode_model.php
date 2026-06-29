<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mxcode_model extends CI_Model
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
        $this->db->select('usuarios.*, permissoes.nome as permissao');
        $this->db->join('permissoes', 'permissoes.id_permissao = usuarios.permissoes_id', 'left');
        $this->db->where('id_usuarios', $id);
        $this->db->limit(1);
        return $this->db->get('usuarios')->row();
    }

    public function getUsuario($id)
    {
        return $this->db
            ->where('id_usuarios', $id)
            ->get('usuarios')
            ->row();
    }

    public function alterarSenha($id, $newSenha)
    {
        $this->db->set('senha', password_hash($newSenha, PASSWORD_DEFAULT));
        $this->db->where('id_usuarios', $id);
        return $this->db->update('usuarios');

    }

    function pesquisar($termo, $id, $modulosBusca = null)
    {
        $data = array(
            'lancamentos' => array(),
            'faturas'     => array(),
            'despesas'    => array(),
            'clientes'    => array(),
            'cartoes'     => array(),
        );

        $termo = trim((string)$termo);

        if ($termo === '') {
            return $data;
        }

        $termo = $this->db->escape_like_str($termo);
        $modulosBusca = array_merge(
            array(
                'lancamentos' => true,
                'faturas'     => true,
                'despesas'    => true,
                'clientes'    => true,
                'cartoes'     => true,
            ),
            (array)$modulosBusca
        );

        // buscando lançamentos
        if ($modulosBusca['lancamentos']) {
            $this->db
                ->select('id_lancamento, descricao, cliente_fornecedor, valor, data_lancamento, tipo, baixado')
                ->where("(descricao LIKE '%$termo%' OR cliente_fornecedor LIKE '%$termo%' OR observacoes LIKE '%$termo%')")
                ->where('status', 1)
                ->where('id_usuario', $id)
                ->order_by('data_lancamento', 'desc')
                ->order_by('id_lancamento', 'desc')
                ->limit(5);
            $data['lancamentos'] = $this->db->get('lancamentos')->result();
        }

        // buscando faturas/compras
        if ($modulosBusca['faturas']) {
            $this->db
                ->select('lf.id_lancamento, lf.id_fatura, f.id_cartao, lf.descricao, lf.nome_cliente, lf.valor_total, lf.total_parcelas, lf.data_compra, f.mes_referencia, f.ano_referencia, c.apelido AS cartao_apelido')
                ->from('lancamentos_faturas AS lf')
                ->join('faturas AS f', 'f.id_fatura = lf.id_fatura', 'inner')
                ->join('cartoes AS c', 'c.id_cartao = f.id_cartao', 'left')
                ->where("(lf.descricao LIKE '%$termo%' OR lf.nome_cliente LIKE '%$termo%' OR lf.observacoes LIKE '%$termo%')")
                ->where('lf.status', 1)
                ->where('f.status', 1)
                ->where('f.id_usuario', $id)
                ->group_by('lf.id_lancamento')
                ->order_by('lf.criado_em', 'desc')
                ->limit(5);
            $data['faturas'] = $this->db->get()->result();
        }

        // buscando despesas
        if ($modulosBusca['despesas']) {
            $this->db
                ->select('id, descricao, fornecedor, nome_terceiro, valor_total, valor_parcela, data_pagamento, tipo_despesa, despesa_parcelada, total_parcelas')
                ->where("(descricao LIKE '%$termo%' OR fornecedor LIKE '%$termo%' OR nome_terceiro LIKE '%$termo%' OR observacoes LIKE '%$termo%')")
                ->where('status', 1)
                ->where('id_usuario', $id)
                ->order_by('criado_em', 'desc')
                ->order_by('id', 'desc')
                ->limit(5);
            $data['despesas'] = $this->db->get('despesas')->result();
        }

        // buscando clientes
        if ($modulosBusca['clientes']) {
            $this->db
                ->select('id_clientes, nome, cpf, email, telefone')
                ->where("(nome LIKE '%$termo%' OR cpf LIKE '%$termo%' OR email LIKE '%$termo%' OR telefone LIKE '%$termo%')")
                ->where('status', 1)
                ->where('id_usuario', $id)
                ->order_by('nome', 'asc')
                ->limit(5);
            $data['clientes'] = $this->db->get('clientes')->result();
        }

        // buscando cartões
        if ($modulosBusca['cartoes']) {
            $this->db
                ->select('id_cartao, apelido, bandeira, nome, ativo, principal')
                ->where("(apelido LIKE '%$termo%' OR bandeira LIKE '%$termo%' OR nome LIKE '%$termo%')")
                ->where('status', 1)
                ->where('id_usuario', $id)
                ->order_by('apelido', 'asc')
                ->limit(5);
            $data['cartoes'] = $this->db->get('cartoes')->result();
        }

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

    public function verificaCPF($cpf)
    {
        $result = $this->db
            ->where('cpf', $cpf)
            ->get('usuarios')
            ->row();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    function getTokenbyToken($username, $token)
    {
        $this->db
            ->where('username', $username)
            ->where('token', $token)
            ->where('status', 1);
        return $this->db->get('clientes_api');
    }
}
