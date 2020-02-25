<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Fatura_model extends CI_Model
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

    function get($table, $fields, $where = '', $id_usuario, $perpage = 0, $start = 0, $one = false, $array = 'array')
    {

        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('vencimento', 'asc');
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

    function getLancamentosAssoc($table, $fields, $id_fatura, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('data_compra', 'id_assoc', 'asc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
            $this->db->where('status', 1);
            $this->db->where('id_fatura', $id_fatura);
        } else {
            $this->db->where('status', 1);
            $this->db->where('id_fatura', $id_fatura);
        }
        $query = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getLancamentos($table, $fields, $id_usuario, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {

        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('id_lancamento', 'asc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
            $this->db->where('status', 1);
            $this->db->where('id_usuario', $id_usuario);
        } else {
            $this->db->where('status', 1);
            $this->db->where('id_usuario', $id_usuario);
        }

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getDetalhesFatura($id_fatura)
    {
        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'id_fatura = ' . $id_fatura);
        $query = $this->db->get();
        $result = $query->row();

        return $result;
    }

    function getFaturaUsuario($id_fatura, $id_usuario)
    {
        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where('status', 1);
        $this->db->where('id_fatura', $id_fatura);
        $this->db->where('id_usuario', $id_usuario);

        return $this->db->count_all_results();

    }

    function getById($id)
    {
        $this->db->where('id_fatura', $id);
        $this->db->limit(1);
        return $this->db->get('faturas')->row();
    }

    function getLancamentoEditavel($mes, $ano)
    {
        $this->db->select('id_lancamento');
        $this->db->from('lancamentos_faturas_assoc');
        $this->db->where('n_parcela', 1);
        $this->db->where('status', 1);
        $this->db->where('mes_referencia', $mes);
        $this->db->where('ano_referencia', $ano);

        $rows = $this->db->count_all_results('', false);

        if ($rows > 0) {
            foreach ($this->db->get()->result() as $row) {
                $data[] = $row->id_lancamento;
            }
            return $data;
        } else {
            return $this->db->get()->result();
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

    function insert_id($table)
    {
        return $this->db->insert_id($table);
    }

    function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if (($this->db->error()['code'] != 0)) {
            return $this->db->error()['code']['message']; // Or do whatever you gotta do here to raise an error
        } else {
            return true;
        }

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

    function excluirFatura($id)
    {
        $data = array(
            'status' => 0,
        );
        $this->db->where('id_fatura', $id);
        $this->db->update('faturas', $data);
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

    function count($table, $where)
    {

        $this->db->from($table);
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    function getTotalQuitadas($id_usuario, $id_cliente = null)
    {
        if (!$id_cliente == null) {
            $this->db
                ->select('SUM(valor) AS total')
                ->from('pendencias')
                ->where('status = 1 AND quitado = 1 AND id_usuario  = ' . $id_usuario . ' AND id_cliente = ' . $id_cliente);
        } else {
            $this->db
                ->select('SUM(valor) AS total')
                ->from('pendencias')
                ->where('status = 1 AND quitado = 1 AND id_usuario  = ' . $id_usuario);
        }

        return $this->db->get()->row();

    }

    function getTotalPendencias($id_usuario, $id_cliente = null)
    {
        if (!$id_cliente == null) {
            $this->db
                ->select('SUM(valor) AS total')
                ->from('pendencias')
                ->where('status = 1 AND quitado = 0 AND id_usuario = ' . $id_usuario . ' AND id_cliente = ' . $id_cliente);

        } else {
            $this->db
                ->select('SUM(valor) AS total')
                ->from('pendencias')
                ->where('status = 1 AND quitado = 0 AND id_usuario = ' . $id_usuario);

        }
        return $this->db->get()->row();

    }

    function getTotal($id_usuario)
    {
        $this->db
            ->select('SUM(valor) AS total')
            ->from('pendencias')
            ->where('status = 1 AND id_usuario = ' . $id_usuario);
        return $this->db->get()->row();

    }

    function getFaturaAbertaUsuario($id)
    {

        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'fatura_aberta = 1 AND status = 1 AND id_usuario = ' . $id);

        return $this->db->count_all_results();
    }

    function getFaturaAberta($id)
    {

        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'status = 1 AND fatura_aberta = 1 AND id_usuario = ' . $id);

        return $this->db->count_all_results();
    }

    function getFaturaPaga($id)
    {

        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'fatura_paga = 1 AND id_fatura = ' . $id);

        return $this->db->count_all_results();
    }

    function getUltimaFaturaAberta($id_usuario)
    {
        $one = false;
        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'fatura_aberta = 1 AND status = 1 AND id_usuario = ' . $id_usuario);
        $this->db->order_by('vencimento', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row();

        return $result;
    }

    function getUltimaFatura($id_usuario)
    {
        $one = false;
        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'status = 1 AND id_usuario = ' . $id_usuario);
        $this->db->order_by('vencimento', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row();

        return $result;
    }

    function abrirFatura($data)
    {
        $this->db->insert('faturas', $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        return false;
    }

    function getFaturaReferencia($id_usuario, $mes, $ano)
    {
        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'status = 1 AND id_usuario = ' . $id_usuario .
            ' AND mes_referencia = ' . $mes . ' AND ano_referencia = ' . $ano);
        $query = $this->db->get();
        $result = $query->row();

        return $query;
    }

    function getValorTotalFatura($id_fatura)
    {
        $this->db
            ->select('SUM(valor_parcela) AS valor_total')
            ->from('lancamentos_faturas_assoc')
            ->where('status = 1 AND id_fatura = ' . $id_fatura);

        return $this->db->get()->row();

    }

    function getValorTotalFaturaAtual($id_usuario)
    {
        $this->db
            ->select('id_fatura')
            ->from('faturas')
            ->where('status = 1 AND fatura_aberta = 1 AND id_usuario = ' . $id_usuario);
        $result = $this->db->get()->row();

        $this->db
            ->select('SUM(valor_parcela) AS total')
            ->from('lancamentos_faturas_assoc')
            ->where('status = 1 AND id_fatura = ' . $result->id_fatura);

        $result = $this->db->get()->row();
        return $result;

    }

    function getSaldoFaturasPendentes($id_usuario)
    {
        $this->db
            ->select('SUM(a.valor_parcela) AS total')
            ->from('lancamentos_faturas_assoc AS a')
            ->join('faturas AS b', 'b.id_fatura = a.id_fatura AND b.status = a.status')
            ->where('b.id_usuario = ' . $id_usuario . ' AND b.fatura_paga != 1 AND a.status = 1')
            ->where('date(b.vencimento) > date(now())');
        return $this->db->get()->row();

    }

    function getSaldoFaturasVencidas($id_usuario)
    {
        $this->db
            ->select('SUM(a.valor_parcela) AS total')
            ->from('lancamentos_faturas_assoc AS a')
            ->join('faturas AS b', 'b.id_fatura = a.id_fatura AND b.status = a.status')
            ->where('b.id_usuario = ' . $id_usuario . ' AND b.fatura_paga != 1 AND a.status = 1')
            ->where('date(b.vencimento) < date(now())');
        return $this->db->get()->row();

    }

    function getSaldoFaturasPagas($id_usuario)
    {
        $this->db
            ->select('SUM(a.valor_parcela) AS total')
            ->from('lancamentos_faturas_assoc AS a')
            ->join('faturas AS b', 'b.id_fatura = a.id_fatura AND b.status = a.status')
            ->where('b.id_usuario = ' . $id_usuario . ' AND b.fatura_paga = 1 AND a.status = 1');
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
}
