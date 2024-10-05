<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Despesa_model extends CI_Model
{

    /**
     * author: Leônidas Ferreira
     * email: leonidas.f.silva@hotmail.com
     */

    protected $mainTable    = 'despesas';
    protected $assocTable   = 'lancamentos_despesas';

    function __construct()
    {
        parent::__construct();
    }

    function get($where = null, $id_usuario, $id_cartao, $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select('*');
        $this->db->from($this->mainTable);
        $this->db->order_by('vencimento', 'asc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }
        $this->db->where('status', 1);
        // este trecho do codigo foi comentado para tornar visivel aos titulares as faturas do cartoes adicionais
        // $this->db->where('id_usuario', $id_usuario);
        $this->db->where('id_cartao', $id_cartao);
        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getDespesas($where = '', $perpage = 0, $start = 0, $order_by = null, $one = false)
    {

        $this->db->select('*');
        $this->db->from($this->mainTable);
        $this->db->limit($perpage, $start);

        $idUsuario = getUserId();

        if ($where) {
            $this->db->where($where);
            $this->db->where('status', 1);
            $this->db->where('id_usuario', $idUsuario);
        } else {
            $this->db->where('status', 1);
            $this->db->where('id_usuario', $idUsuario);
        }

        if ($order_by) {
            if (is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            } else {
                $this->db->order_by('id', $order_by);
            }
        }

        $query = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getLancamentosAssoc($id_fatura, $where = '', $perpage = 0, $start = 0, $order_by = null, $one = false)
    {
        $this->db->select('*');
        $this->db->from($this->assocTable);
        $this->db->limit($perpage, $start);

        if ($where) {
            $this->db->where($where);
            $this->db->where('status', 1);
            $this->db->where('id_fatura', $id_fatura);
        } else {
            $this->db->where('status', 1);
            $this->db->where('id_fatura', $id_fatura);
        }

        if ($order_by) {
            if (is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            } else {
                $this->db->order_by('data_compra', $order_by);
                $this->db->order_by('id_assoc', $order_by);
            }
        }

        $query = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getDetalhesDespesasById($id)
    {
        if (is_string($id) || !is_numeric($id)) {
            return false;
        }

        $idUsuario = getUserId();

        $query = "SELECT d.*,
            ld.*
            FROM despesas d
            LEFT JOIN lancamentos_despesas ld
            ON ld.id_despesa = d.id
            WHERE ld.id_despesa = $id
            AND d.id_usuario = $idUsuario
            AND d.status = 1
            ORDER BY ld.criado_em DESC
        ";

        $resultQuery = $this->db->query($query);
        $result = $resultQuery->result_array();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function getDetalhesDespesaByReference($mesReferencia, $anoReferencia)
    {
        if (is_string($mesReferencia) || !is_numeric($mesReferencia)) {
            return false;
        }

        if (is_string($anoReferencia) || !is_numeric($anoReferencia)) {
            return false;
        }

        $idUsuario = getUserId();

        $query = "SELECT d.*,
            ld.*
            FROM despesas d
            LEFT JOIN lancamentos_despesas ld
            ON ld.id_despesa = d.id
            WHERE ld.mes_referencia = $mesReferencia
            AND ld.ano_referencia = $anoReferencia
            AND d.id_usuario = $idUsuario
            ORDER BY ld.criado_em DESC
        ";

        $resultQuery = $this->db->query($query);
        $result = $resultQuery->result_array();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function addDespesa($data)
    {
        $this->db->insert($this->mainTable, $data);

        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

    function addLancamentoDespesa($data)
    {
        $this->db->insert($this->assocTable, $data);

        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

    function lastInsertedId($table = null)
    {
        if (!$table) {
            $table = $this->mainTable;
        }
        return $this->db->insert_id($table);
    }

    function editDespesa($data, $fieldID, $id)
    {
        $this->db->where($fieldID, $id);
        $this->db->update($this->mainTable, $data);

        if (($this->db->error()['code'] != 0)) {
            return $this->db->error()['code']['message']; // Or do whatever you gotta do here to raise an error
        }
        return true;
    }

    function editLancamentoDespesa($data, $fieldID, $id)
    {
        $this->db->where($fieldID, $id);
        $this->db->update($this->assocTable, $data);

        if (($this->db->error()['code'] != 0)) {
            return $this->db->error()['code']['message']; // Or do whatever you gotta do here to raise an error
        }
        return true;
    }

    function deleteDespesa($id)
    {
        $data = [
            'status' => 0,
        ];

        $this->db->where('id', $id);
        $this->db->update($this->mainTable, $data);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function deleteLancamentoDespesa($id)
    {
        $data = [
            'status' => 0,
        ];

        $this->db->where('id', $id);
        $this->db->update($this->assocTable, $data);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function deleteReal($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function countDespesas($where = null)
    {
        $this->db->from($this->mainTable);

        if ($where) {
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    function countLancamentosDespesas($where = null)
    {
        $this->db->from($this->assocTable);

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

    function getFaturaAbertaUsuario($id_usuario, $id_cartao)
    {

        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where('fatura_aberta = 1 AND status = 1 AND id_usuario = ' . $id_usuario);
        $this->db->where('id_cartao', $id_cartao);

        return $this->db->count_all_results();
    }

    function getFaturaAberta($id)
    {

        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'status = 1 AND fatura_aberta = 1 AND id_usuario = ' . $id
        );

        return $this->db->count_all_results();
    }

    function getFaturaPaga($id)
    {
        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'fatura_paga = 1 AND id_fatura = ' . $id
        );

        return $this->db->count_all_results();
    }

    function getFaturaAtual($id_fatura)
    {
        return $this->db
            ->where('id_fatura', $id_fatura)
            ->get('faturas')
            ->row();
    }

    function getUltimaFatura($id_cartao)
    {
        $one = false;
        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where('status', 1);
        $this->db->where('id_cartao', $id_cartao);
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

    function getFaturaReferencia($id_cartao, $mes, $ano)
    {
        return $this->db
            ->where('status', 1)
            ->where('id_cartao', $id_cartao)
            ->where('mes_referencia', $mes)
            ->where('ano_referencia', $ano)
            ->get('faturas')
            ->row();
    }

    function getValorTotalFatura($id_fatura)
    {
        return $this->db
            ->select('SUM(valor_parcela) AS valor_total')
            ->where('status = 1 AND id_fatura = ' . $id_fatura)
            ->get('lancamentos_faturas_assoc')
            ->row('valor_total');
    }

    function getValorTotalFaturaAtual($id_usuario)
    {
        $cartoes = $this->db
            ->where('status', 1)
            ->where('id_usuario', $id_usuario)
            ->or_where('id_usuario_titular', $id_usuario)
            ->where('status', 1)
            ->get('cartoes')
            ->result();

        foreach ($cartoes as $c) {
            $this->db
                ->where('status', 1)
                ->where('fatura_aberta', 1)
                ->where('id_cartao', $c->id_cartao);
            $faturas[] = $this->db->get('faturas')->row();
        }

        if (isset($faturas) && $faturas) {
            $results = [];
            $total = null;

            foreach ($faturas as $f) {
                if ($f != null) {
                    $this->db
                        ->select('SUM(valor_parcela) AS total')
                        ->where('status', 1)
                        ->where('id_fatura', $f->id_fatura)
                        ->where('mes_referencia', $f->mes_referencia)
                        ->where('ano_referencia', $f->ano_referencia);
                    $results[] = $this->db->get('lancamentos_faturas_assoc')->row();
                }
            }

            foreach ($results as $r) {
                $total += $r->total;
            }

            return $total;
        }
        return null;
    }

    function existeConfiguracao($id_cartao)
    {
        $this->db->from('configs_faturas');
        $this->db->where('id_cartao', $id_cartao);

        if ($this->db->count_all_results()) {
            return true;
        } else {
            return false;
        }
    }

    function getDiaVencimentoFatura($id_cartao)
    {
        return $this->db
            ->where('id_cartao', $id_cartao)
            ->get('configs_faturas')
            ->row('dia_vencimento');
    }

    function getSaldoFaturasPendentes($id_cartao)
    {
        $this->db
            ->select('SUM(a.valor_parcela) AS total')
            ->from('lancamentos_faturas_assoc AS a')
            ->join('faturas AS b', 'b.id_fatura = a.id_fatura AND b.status = a.status')
            ->where('b.fatura_paga != 1 AND a.status = 1')
            ->where('id_cartao', $id_cartao)
            ->where('date(b.vencimento) > date(now())');
        return $this->db->get()->row();
    }

    function getSaldoFaturasVencidas($id_cartao)
    {
        $this->db
            ->select('SUM(a.valor_parcela) AS total')
            ->from('lancamentos_faturas_assoc AS a')
            ->join('faturas AS b', 'b.id_fatura = a.id_fatura AND b.status = a.status')
            ->where('b.fatura_paga != 1 AND a.status = 1')
            ->where('id_cartao', $id_cartao)
            ->where('date(b.vencimento) < date(now())');
        return $this->db->get()->row();
    }

    function getSaldoFaturasPagas($id_cartao)
    {
        $this->db
            ->select('SUM(a.valor_parcela) AS total')
            ->from('lancamentos_faturas_assoc AS a')
            ->join('faturas AS b', 'b.id_fatura = a.id_fatura AND b.status = a.status')
            ->where('id_cartao', $id_cartao)
            ->where('b.fatura_paga = 1 AND a.status = 1');
        return $this->db->get()->row();
    }

    function getClientesPorFatura($idFatura)
    {
        return $this->db
            ->select('c.id_clientes, c.nome')
            ->join('lancamentos_faturas AS lf', 'lf.id_cliente = c.id_clientes', 'inner')
            ->join('lancamentos_faturas_assoc AS lfa', 'lfa.id_lancamento = lf.id_lancamento', 'inner')
            ->join('faturas AS f', 'f.id_fatura = lfa.id_fatura', 'inner')
            ->where('lfa.status', 1)
            ->where('f.id_fatura', $idFatura)
            ->group_by('c.id_clientes')
            ->order_by('c.nome')
            ->get('clientes AS c')
            ->result();
    }

    function getVinculoFatura($idFatura)
    {
        $count = $this->db->select('*')
            ->from('lancamentos')
            ->where('status', 1)
            ->where('id_fatura', $idFatura);

        if ($count->count_all_results() > 0) {
            $this->db
                ->select('*')
                ->from('lancamentos')
                ->where('status', 1)
                ->where('id_fatura', $idFatura);
            return $this->db->get()->row();
        }
        return false;
    }

    function getFaturaByLancamento($idLancamento)
    {
        $this->db->from('lancamentos_faturas');
        $this->db->where('id_lancamento', $idLancamento);
        return $this->db->get()->row('id_fatura');
    }

    function getFaturasTerceiros($idUsuario, $nome, $mesReferencia, $anoReferencia)
    {
        if (!is_string($nome) || is_numeric($nome)) {
            return false;
        }

        $query = "SELECT f.*,
            lfa.mes_referencia
            FROM faturas f
            INNER JOIN lancamentos_faturas lf
            ON lf.id_fatura = f.id_fatura
            INNER JOIN lancamentos_faturas_assoc lfa
            ON lfa.id_lancamento = lf.id_lancamento
            WHERE lf.nome_cliente LIKE '$nome'
            AND lfa.mes_referencia = $mesReferencia
            AND lfa.ano_referencia = $anoReferencia
            AND f.id_usuario = $idUsuario
            GROUP BY f.id_cartao
            ORDER BY lf.criado_em DESC";

        $resultQuery = $this->db->query($query);
        $result = $resultQuery->result_array();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function getLancamentosTerceiros($idUsuario, $idCartao, $nome, $mesReferencia)
    {
        if (!is_string($nome) || is_numeric($nome)) {
            return false;
        }

        $query = "SELECT lfa.*,
            lf.nome_cliente,
            lf.descricao,
            f.id_cartao
            FROM lancamentos_faturas lf
            INNER JOIN faturas f
            ON lf.id_fatura = f.id_fatura
            INNER JOIN lancamentos_faturas_assoc lfa
            ON lfa.id_lancamento = lf.id_lancamento
            WHERE lf.nome_cliente LIKE '$nome'
            AND f.id_usuario = $idUsuario
            AND f.id_cartao = $idCartao
            AND lfa.mes_referencia = $mesReferencia
            AND lf.status = 1
            AND lfa.status = 1
            ORDER BY lf.criado_em DESC";

        $resultQuery = $this->db->query($query);
        $result = $resultQuery->result_array();

        if (!$result) {
            return false;
        }

        return $result;
    }

    function autoCompleteTerceiros($q, $id_usuario)
    {
        $query = $this->db->select('*')
            ->limit(5)
            ->like('nome_cliente', $q)
            ->where('id_usuario', $id_usuario)
            ->where('status', 1)
            ->group_by('nome_cliente')
            ->get('lancamentos_faturas');

        if ($query->num_rows() > 0) {
            $row_set = [];

            foreach ($query->result_array() as $row) {
                $row_set[] = [
                    'label' => $row['nome_cliente']
                ];
            }
            echo json_encode($row_set);
        }
    }

    function autoCompleteDescricao($query, $idUsuario)
    {
        $query = $this->db->select('*')
            ->limit(5)
            ->like('descricao', $query)
            ->where('id_usuario', $idUsuario)
            ->where('status', 1)
            ->group_by('descricao')
            ->order_by('id_lancamento')
            ->get('lancamentos_faturas');

        if ($query->num_rows() > 0) {
            $row_set = [];

            foreach ($query->result_array() as $row) {
                $row_set[] = [
                    'label' => $row['descricao']
                ];
            }
            echo json_encode($row_set);
        }
    }

    function getAllTerceiros($idCartao = null, $mesReferencia = null, $anoReferencia = null, $idUsuario = null)
    {
        if (!$idUsuario) {
            $idUsuario = getUserId();
        }

        if ($idCartao && $mesReferencia && $anoReferencia) {
            $query = "SELECT
                lf.*
                FROM lancamentos_faturas lf
                INNER JOIN faturas f
                ON lf.id_fatura = f.id_fatura
                INNER JOIN lancamentos_faturas_assoc lfa
                ON lfa.id_lancamento = lf.id_lancamento
                WHERE f.id_usuario = $idUsuario
                AND f.id_cartao = $idCartao
                AND lfa.mes_referencia = $mesReferencia
                AND lfa.ano_referencia = $anoReferencia
                AND lfa.status = 1
                AND lf.nome_cliente IS NOT NULL
                GROUP BY lf.nome_cliente ASC
            ";
        } else {
            $query = "SELECT
                lf.*
                FROM lancamentos_faturas lf
                INNER JOIN faturas f
                ON lf.id_fatura = f.id_fatura
                INNER JOIN lancamentos_faturas_assoc lfa
                ON lfa.id_lancamento = lf.id_lancamento
                WHERE f.id_usuario = $idUsuario
                AND lfa.status = 1
                AND lf.nome_cliente IS NOT NULL
                GROUP BY lf.nome_cliente ASC
            ";
        }

        $resultQuery = $this->db->query($query);

        if ($resultQuery->num_rows() > 0) {
            $row_set = [];

            foreach ($resultQuery->result_array() as $row) {
                $row_set[] = [
                    'nome' => $row['nome_cliente']
                ];
            }
            return $row_set;
        }
        return false;
    }

    function fecharTodasFaturasAbertas($id_cartao, $id_usuario)
    {
        $this->db->select('*');
        $this->db->where('fatura_aberta', 1);
        $this->db->where('status', 1);
        $this->db->where('id_usuario', $id_usuario);
        $this->db->where('id_cartao', $id_cartao);
        $query = $this->db->get('faturas');

        $results = $query->result_array();

        if ($results) {
            foreach ($results as $result) {
                try {
                    $update = [
                        'fatura_aberta' => 0
                    ];

                    $this->edit('faturas', $update, 'id_fatura', $result['id_fatura']);
                } catch (\Exception $e) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
