<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Despesa_model extends CI_Model
{

    /**
     * author: Leônidas Ferreira
     * email: leonidas.f.silva@hotmail.com
     */

    protected string $despesasTable            = 'despesas';
    protected string $lancamentosDespesasTable = 'lancamentos_despesas';
    protected        $userId;

    function __construct()
    {
        parent::__construct();
        $this->userId = getUserId();
    }

    function get($where = null, $limit = null, $rows = 0, $perpage = 0, $start = 0, $order_by = null, $one = false)
    {
        $this->db->from($this->despesasTable);
        $this->db->limit($perpage, $start);
        $this->db->where('status', 1);
        $this->db->where('id_usuario', getUserId());

        if ($where) {
            $this->db->where($where);
        }

        if ($order_by) {
            if (is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            } else {
                $this->db->order_by('criado_em', $order_by);
            }
        }

        if ($limit) {
            if ($rows > $limit) {
                $this->db->limit($limit, ($rows - $limit));
            } else {
                $this->db->limit($limit, $start);
            }
        }

        $query = $this->db->get();
        // getSqlStatement(); // uncomment to retrieve the sql statement query string on screen
        return !$one ? $query->result() : $query->row();
    }

    function getDespesas($where = '', $perpage = 0, $start = 0, $order_by = null, $one = false)
    {
        $this->db->from($this->despesasTable);
        $this->db->limit($perpage, $start);

        $idUsuario = getUserId();

        if ($where) {
            $this->db->where($where);
        }

        $this->db->where('status', 1);
        $this->db->where('id_usuario', $idUsuario);

        if ($order_by) {
            if (is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            } else {
                $this->db->order_by('id', $order_by);
            }
        }

        $query  = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getLancamentosDespesa($idDespesa, $where = '', $limit = null, $rows = 0, $perpage = 0, $start = 0, $order_by = null, $one = false)
    {
        $this->db->from($this->lancamentosDespesasTable);
        $this->db->limit($perpage, $start);

        if ($where) {
            $this->db->where($where);
        }

        $this->db->where('status', 1);
        $this->db->where('id_despesa', $idDespesa);

        if ($order_by) {
            if (is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            } else {
                $this->db->order_by('id', $order_by);
            }
        }

        if ($limit) {
            if ($rows > $limit) {
                $this->db->limit($limit, ($rows - $limit));
            } else {
                $this->db->limit($limit, $start);
            }
        }

        $query  = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        // getSqlStatement(); // uncomment to retrieve the sql statement query string on screen
        return $result;
    }

    function getLancamentosVinculadosDespesa($idDespesa, $fields = null, $where = null, $limit = null, $rows = 0, $perpage = 0, $start = 0, $one = false)
    {
        $this->db->select();
        if ($fields) $this->db->select($fields);

        $this->db->from($this->lancamentosDespesasTable);
        $this->db->limit($perpage, $start);
        $this->db->where('status', 1);
        $this->db->where('registro_vinculado', 1);
        $this->db->where('id_despesa', $idDespesa);

        if ($where) $this->db->where($where);

        if ($limit) {
            if ($rows > $limit) {
                $this->db->limit($limit, ($rows - $limit));
            } else {
                $this->db->limit($limit, $start);
            }
        }

        $query = $this->db->get();
        // getSqlStatement(); // uncomment to retrieve the sql statement query string on screen
        $result = !$one ? $query->result() : $query->row();
        return $result ?? false;
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
        $result      = $resultQuery->result_array();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function getParcelasPagas($idDespesa)
    {
        if (!is_numeric($idDespesa)) {
            return false;
        }

        $idUsuario = getUserId();

        $query = "SELECT ld.*
            FROM despesas d
            LEFT JOIN lancamentos_despesas ld
            ON ld.id_despesa = d.id
            WHERE d.id = ?
            AND d.id_usuario = ?
            AND d.status = 1
            AND ld.registro_pago = 1
        ";

        $result = $this->db->query($query, [$idDespesa, $idUsuario]);
        $result = $result->num_rows();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function getDespesaById($idDespesa)
    {
        if (!is_numeric($idDespesa)) {
            return false;
        }

        $this->db
            ->from($this->despesasTable)
            ->where('id_usuario', $this->userId)
            ->where('id', $idDespesa)
            ->where('status', 1);

        $result = $this->db->get()->row();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function getDetalhesLancamentoDespesa($idLancamentoDespesa)
    {
        if (!is_numeric($idLancamentoDespesa)) {
            return false;
        }

        $this->db
            ->from($this->lancamentosDespesasTable)
            ->where('id', $idLancamentoDespesa)
            ->where('status', 1);

        $result = $this->db->get()->row();

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
        $result      = $resultQuery->result_array();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function add($data)
    {
        $this->db->insert($this->despesasTable, $data);

        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

    function addLancamentoDespesa($data)
    {
        $this->db->insert($this->lancamentosDespesasTable, $data);

        if ($this->db->affected_rows()) return true;

        return false;
    }

    function editLancamentosDespesa($data)
    {
        if (!isset($data['id_despesa']) || !$data['id_despesa']) return false;

        $this->db->where('id_despesa', $data['id_despesa']);
        $this->db->update($this->lancamentosDespesasTable, $data);

        if ($this->db->affected_rows()) return true;

        return false;
    }

    function editLancamentoDespesa($data)
    {
        if (!isset($data['id']) || !$data['id']) return false;

        $this->db->where('id', $data['id']);
        $this->db->update($this->lancamentosDespesasTable, $data);

        if ($this->db->affected_rows()) return true;

        return false;
    }

    function lastInsertedId($table = null)
    {
        if (!$table) {
            $table = $this->despesasTable;
        }
        return $this->db->insert_id($table);
    }

    function edit($dataToUpdate, $fieldID, $id)
    {
        try {
            $this->db->where($fieldID, $id);
            $this->db->update($this->despesasTable, $dataToUpdate);
        } catch (\Exception $e) {
            return $this->db->error()['code']['message']; // Or do whatever you gotta do here to raise an error
        }

        if (($this->db->error()['code'] != 0)) {
            return false;
        }
        return true;
    }

    function deleteDespesa($id)
    {
        $data = [
            'status' => 0,
        ];

        $this->db->where('id', $id);
        $this->db->update($this->despesasTable, $data);

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
        $this->db->update($this->lancamentosDespesasTable, $data);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function deleteLancamentosDespesa($idDespesa)
    {
        $this->db->where('id_despesa', $idDespesa);
        $this->db->delete($this->lancamentosDespesasTable);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function excluiRegistroEmModuloLancamentos($where, $dataUpdate)
    {
        $this->db->where($where);
        $this->db->update('lancamentos', $dataUpdate);

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function countDespesasFromUser($where = null)
    {
        $this->db->from($this->despesasTable);
        $this->db->where('id_usuario', $this->userId);
        $this->db->where('status', 1);

        if ($where) {
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    function countLancamentosFromDespesa($idDespesa, $where = null)
    {
        $this->db
            ->from($this->lancamentosDespesasTable)
            ->where('id_despesa', $idDespesa)
            ->where('status', 1);

        if ($where) $this->db->where($where);

        $result = $this->db->count_all_results();
        // getSqlStatement();
        return $result;
    }

    function getLancamentoDespesaById($id)
    {
        $count = $this->db->select('*')
            ->from($this->lancamentosDespesasTable)
            ->where('status', 1)
            ->where('id', $id);

        if ($count->count_all_results() > 0) {
            $this->db
                ->select('*')
                ->from($this->lancamentosDespesasTable)
                ->where('status', 1)
                ->where('id', $id);
            return $this->db->get()->row();
        }
        return false;
    }

    function getUltimaParcelaDespesa($idDespesa)
    {
        $count = $this->db
            ->from($this->lancamentosDespesasTable)
            ->where('status', 1)
            ->where('id_despesa', $idDespesa)
            ->where('num_parcela !=', null);

        if ($count->count_all_results() > 0) {
            $this->db
                ->from($this->lancamentosDespesasTable)
                ->where('status', 1)
                ->where('id_despesa', $idDespesa)
                ->order_by('num_parcela', 'DESC');
            return $this->db->get()->row();
        }
        return false;
    }

    function getRegistroDespesaByDate($idDespesa, $dataReferencia)
    {
        $count = $this->db
            ->from($this->lancamentosDespesasTable)
            ->where('status', 1)
            ->where('id_despesa', $idDespesa)
            ->where('data_vencimento', $dataReferencia);

        if ($count->count_all_results() > 0) {
            $this->db
                ->from($this->lancamentosDespesasTable)
                ->where('status', 1)
                ->where('id_despesa', $idDespesa)
                ->where('data_vencimento', $dataReferencia);
            return $this->db->get()->row();
        }
        return false;
    }

    function getLancamentoDespesaByVencimento($idDespesa, $dataVencimento)
    {
        $count = $this->db
            ->from($this->lancamentosDespesasTable)
            ->where('status', 1)
            ->where('id_despesa', $idDespesa)
            ->where('data_vencimento', $dataVencimento);

        if ($count->count_all_results() > 0) {
            $this->db
                ->from($this->lancamentosDespesasTable)
                ->where('status', 1)
                ->where('id_despesa', $idDespesa)
                ->where('data_vencimento', $dataVencimento);
            return $this->db->get()->row();
        }
        return false;
    }

    function getDespesasAutoVinculo()
    {
        $count = $this->db
            ->from($this->despesasTable)
            ->where('auto_vinculo', 1)
            ->where('status', 1);

        if ($count->count_all_results() > 0) {
            $this->db
                ->from($this->despesasTable)
                ->where('auto_vinculo', 1)
                ->where('status', 1);
            return $this->db->get()->result();
        }
        return false;
    }

    function getVinculoDespesaComModuloLancamentos($idDespesa, $dataReferencia)
    {
        $count = $this->db
            ->from('lancamentos')
            ->where('status', 1)
            ->where('data_lancamento', $dataReferencia)
            ->where('id_despesa', $idDespesa);

        if ($count->count_all_results() > 0) {
            $this->db
                ->from('lancamentos')
                ->where('status', 1)
                ->where('data_lancamento', $dataReferencia)
                ->where('id_despesa', $idDespesa);
            return $this->db->get()->row();
        }
        return false;
    }

    function ativaDespesa($id)
    {
        $this->db->where('id', $id);
        $this->db->update($this->despesasTable, ['auto_vinculo' => 1]);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function desativaDespesa($id)
    {
        $this->db->where('id', $id);
        $this->db->update($this->despesasTable, ['auto_vinculo' => 0]);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function setFlagRegistroVinculado($id)
    {
        $this->db->where('id', $id);
        $this->db->update($this->lancamentosDespesasTable, ['registro_vinculado' => 1]);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function unsetFlagRegistroVinculado($id)
    {
        $this->db->where('id', $id);
        $this->db->update($this->lancamentosDespesasTable, ['registro_vinculado' => null]);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function atualizaValorRegistro($id, $valor)
    {
        $this->db->where('id', $id);
        $this->db->update($this->lancamentosDespesasTable, ['valor' => $valor]);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function setFlagRegistroPago($id)
    {
        $this->db->where('id', $id);
        $this->db->update($this->lancamentosDespesasTable, ['registro_pago' => 1]);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function unsetFlagRegistroPago($id)
    {
        $this->db->where('id', $id);
        $this->db->update($this->lancamentosDespesasTable, ['registro_pago' => null]);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }


    function autoCompleteTerceiros($term, int $idUsuario)
    {
        $resultQuery = $this->db->query("
			SELECT u.terceiro
			FROM
			  (SELECT lf.nome_cliente AS terceiro, lf.id_usuario
			   FROM lancamentos_faturas lf
			   UNION SELECT d.nome_terceiro AS terceiro, d.id_usuario
			   FROM despesas d) AS u
			WHERE u.id_usuario = ?
			  AND u.terceiro LIKE ? GROUP BY u.terceiro
			LIMIT 5
		", [$idUsuario, sprintf('%%%s%%', $term)]);

        if ($resultQuery->num_rows() > 0) {
            $row_set = [];

            foreach ($resultQuery->result_array() as $row) {
                $row_set[] = [
                    'label' => $row['terceiro']
                ];
            }
            echo json_encode($row_set);
        }
    }

    function autoCompleteFornecedor($term, int $idUsuario)
    {
        $resultQuery = $this->db->query("
			SELECT u.fornecedor
			FROM
			  (SELECT l.cliente_fornecedor AS fornecedor, l.id_usuario
			   FROM lancamentos l
			   UNION SELECT d.fornecedor, d.id_usuario
			   FROM despesas d) AS u
			WHERE u.id_usuario = ?
			  AND u.fornecedor LIKE ? GROUP BY u.fornecedor
			LIMIT 5
		", [$idUsuario, sprintf('%%%s%%', $term)]);

        if ($resultQuery->num_rows() > 0) {
            $row_set = [];

            foreach ($resultQuery->result_array() as $row) {
                $row_set[] = [
                    'label' => $row['fornecedor']
                ];
            }
            echo json_encode($row_set);
        }
    }

    function autoCompleteDescricao($term, int $idUsuario)
    {
        $resultQuery = $this->db->query("
			SELECT u.descricao
			FROM
			  (SELECT l.descricao, l.id_usuario
			   FROM lancamentos l
			   UNION SELECT lf.descricao, lf.id_usuario
			   FROM lancamentos_faturas lf
			   UNION SELECT d.descricao, d.id_usuario
			   FROM despesas d) AS u
			WHERE u.id_usuario = ?
			  AND u.descricao LIKE ? GROUP BY u.descricao
			LIMIT 5
		", [$idUsuario, sprintf('%%%s%%', $term)]);

        if ($resultQuery->num_rows() > 0) {
            $row_set = [];

            foreach ($resultQuery->result_array() as $row) {
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

}
