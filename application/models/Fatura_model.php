<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Fatura_model extends CI_Model
{

    /**
     * author: Leônidas Ferreira
     * email: leonidas.f.silva@hotmail.com
     */

    protected string $faturasTable                 = 'faturas';
    protected string $lancamentosFaturasTable      = 'lancamentos_faturas';
    protected string $lancamentosFaturasAssocTable = 'lancamentos_faturas_assoc';
    protected        $userId;

    function __construct()
    {
        parent::__construct();
        $this->userId = getUserId();
    }

    function get($table, $fields, $id_cartao, $where = null, $limit = null, $rows = 0, $perpage = 0, $start = 0, $order_by = null, $one = false, $array = 'array')
    {

        $this->db->select($fields);
        $this->db->from($table);
        $this->db->limit($perpage, $start);

        if ($where) {
            $this->db->where($where);
        }
        $this->db->where('status', 1);
        $this->db->where('id_cartao', $id_cartao);
        // este trecho do codigo foi comentado para tornar visivel aos titulares as faturas dos cartoes adicionais
        // $this->db->where('id_usuario', $id_usuario);

        if ($order_by) {
            if (is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            } else {
                $this->db->order_by('vencimento', $order_by);
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
        // getSqlStatement();
        return $result;
    }

    function getLancamentosAssoc($table, $fields, $id_fatura, $where = '', $perpage = 0, $start = 0, $order_by = null, $one = false)
    {
        $this->db->select($fields);
        $this->db->from($table);
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

        $query  = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getLancamentos($table, $fields, $id_usuario, $where = '', $perpage = 0, $start = 0, $order_by = null, $one = false)
    {

        $this->db->select($fields);
        $this->db->from($table);
        $this->db->limit($perpage, $start);

        if ($where) {
            $this->db->where($where);
            $this->db->where('status', 1);
            $this->db->where('id_usuario', $id_usuario);
        } else {
            $this->db->where('status', 1);
            $this->db->where('id_usuario', $id_usuario);
        }

        if ($order_by) {
            if (is_array($order_by)) {
                foreach ($order_by as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            } else {
                $this->db->order_by('id_lancamento', $order_by);
            }
        }

        $query  = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    function getDetalhesFatura($id_fatura)
    {
        $this->db->select('*');
        $this->db->from('faturas');
        $this->db->where(
            'id_fatura = ' . $id_fatura
        );
        $query  = $this->db->get();
        $result = $query->row();

        return $result;
    }

    function getFatura($id_fatura)
    {
        return $this->db
            ->where('status', 1)
            ->where('id_fatura', $id_fatura)
            ->get('faturas')
            ->row();
    }

    function getFaturaUsuario($id_fatura, $idUser)
    {
        return $this->db
            ->where('status', 1)
            ->where('id_fatura', $id_fatura)
            ->where('id_usuario', $idUser)
            ->get('faturas')
            ->row();
    }

    function getFaturasAbertasCartao($id_cartao)
    {
        return $this->db
            ->where('status', 1)
            ->where('id_cartao', $id_cartao)
            ->where_not_in('fatura_aberta', 0)
            ->get('faturas')
            ->result();
    }

    function getFaturasCartaoUser($id_cartao)
    {
        return $this->db
            ->where('status', 1)
            ->where('id_cartao', $id_cartao)
            ->where('id_usuario', $this->userId)
            ->get('faturas')
            ->result();
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
        if ($this->db->affected_rows() == 1) {
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

    function getLancamentoAssocTerceiroUsuario($idAssoc, $idUsuario)
    {
        return $this->db
            ->select('lfa.*, lf.nome_cliente, lf.descricao, lf.id_usuario')
            ->from('lancamentos_faturas_assoc AS lfa')
            ->join('lancamentos_faturas AS lf', 'lf.id_lancamento = lfa.id_lancamento', 'inner')
            ->where('lfa.id_assoc', $idAssoc)
            ->where('lfa.status', 1)
            ->where('lf.status', 1)
            ->where('lf.compra_terceiros', 1)
            ->where('lf.id_usuario', $idUsuario)
            ->get()
            ->row();
    }

    function setParcelaTerceiroPago($idAssoc, $pago)
    {
        $this->db->where('id_assoc', $idAssoc);
        $this->db->update('lancamentos_faturas_assoc', [
            'parcela_terceiro_pago' => $pago ? 1 : null
        ]);

        return ($this->db->error()['code'] == 0);
    }

    function setCompraTerceiroPago($idLancamento, $idUsuario, $pago)
    {
        $parcelas = $this->db
            ->select('lfa.id_assoc')
            ->from('lancamentos_faturas_assoc AS lfa')
            ->join('lancamentos_faturas AS lf', 'lf.id_lancamento = lfa.id_lancamento', 'inner')
            ->where('lfa.id_lancamento', $idLancamento)
            ->where('lfa.status', 1)
            ->where('lf.status', 1)
            ->where('lf.compra_terceiros', 1)
            ->where('lf.id_usuario', $idUsuario)
            ->get()
            ->result_array();

        if (!$parcelas) {
            return false;
        }

        $idsAssoc = array_column($parcelas, 'id_assoc');

        $this->db->where_in('id_assoc', $idsAssoc);
        $this->db->update('lancamentos_faturas_assoc', [
            'parcela_terceiro_pago' => $pago ? 1 : null
        ]);

        return ($this->db->error()['code'] == 0);
    }

    function sincronizarVinculoTerceiroPorParcela($idAssoc, $idUsuario)
    {
        $parcela = $this->getLancamentoAssocTerceiroUsuario($idAssoc, $idUsuario);

        if (!$parcela) {
            return false;
        }

        $idsLancamentos = array_column(
            $this->getLancamentosTerceirosVinculadosPorAssoc($idAssoc, $idUsuario),
            'id_lancamento'
        );

        $vinculoPeriodo = $this->getVinculoTerceiroPeriodo(
            $idUsuario,
            $parcela->nome_cliente,
            $parcela->mes_referencia,
            $parcela->ano_referencia
        );

        if ($vinculoPeriodo) {
            $idsLancamentos[] = $vinculoPeriodo->id_lancamento;

            if ($parcela->parcela_terceiro_pago === null) {
                if (!$this->vincularParcelaTerceiroAoLancamento($vinculoPeriodo->id_lancamento, $parcela)) {
                    return false;
                }
            }
        }

        $idsLancamentos = array_values(array_unique(array_filter($idsLancamentos)));

        return $this->sincronizarLancamentosTerceiros($idsLancamentos, $idUsuario);
    }

    function sincronizarVinculosTerceiroPorCompra($idLancamentoFatura, $idUsuario)
    {
        $idsLancamentos = array_column(
            $this->getLancamentosTerceirosVinculadosPorCompra($idLancamentoFatura, $idUsuario),
            'id_lancamento'
        );

        $parcelas = $this->db
            ->select('lfa.*, lf.nome_cliente, lf.descricao, lf.id_usuario')
            ->from('lancamentos_faturas_assoc AS lfa')
            ->join('lancamentos_faturas AS lf', 'lf.id_lancamento = lfa.id_lancamento', 'inner')
            ->where('lfa.id_lancamento', $idLancamentoFatura)
            ->where('lfa.status', 1)
            ->where('lf.status', 1)
            ->where('lf.compra_terceiros', 1)
            ->where('lf.id_usuario', $idUsuario)
            ->get()
            ->result();

        foreach ($parcelas as $parcela) {
            $vinculoPeriodo = $this->getVinculoTerceiroPeriodo(
                $idUsuario,
                $parcela->nome_cliente,
                $parcela->mes_referencia,
                $parcela->ano_referencia
            );

            if (!$vinculoPeriodo) {
                continue;
            }

            $idsLancamentos[] = $vinculoPeriodo->id_lancamento;

            if ($parcela->parcela_terceiro_pago === null) {
                if (!$this->vincularParcelaTerceiroAoLancamento($vinculoPeriodo->id_lancamento, $parcela)) {
                    return false;
                }
            }
        }

        $idsLancamentos = array_values(array_unique(array_filter($idsLancamentos)));

        return $this->sincronizarLancamentosTerceiros($idsLancamentos, $idUsuario);
    }

    function getLancamentosTerceirosVinculadosPorAssoc($idAssoc, $idUsuario)
    {
        return $this->db
            ->select('DISTINCT ltv.id_lancamento', false)
            ->from('lancamentos_terceiros_vinculos AS ltv')
            ->join('lancamentos AS l', 'l.id_lancamento = ltv.id_lancamento', 'inner')
            ->where('ltv.id_lancamento_fatura_assoc', $idAssoc)
            ->where('l.id_usuario', $idUsuario)
            ->where('l.status', 1)
            ->get()
            ->result_array();
    }

    function getPeriodosTerceirosVinculadosAtivos($idUsuario)
    {
        return $this->db
            ->select('
                DISTINCT ltv.id_lancamento,
                lf.nome_cliente,
                lfa.mes_referencia,
                lfa.ano_referencia
            ', false)
            ->from('lancamentos_terceiros_vinculos AS ltv')
            ->join('lancamentos AS l', 'l.id_lancamento = ltv.id_lancamento', 'inner')
            ->join('lancamentos_faturas_assoc AS lfa', 'lfa.id_assoc = ltv.id_lancamento_fatura_assoc', 'inner')
            ->join('lancamentos_faturas AS lf', 'lf.id_lancamento = lfa.id_lancamento', 'inner')
            ->where('l.id_usuario', $idUsuario)
            ->where('l.status', 1)
            ->where('lf.id_usuario', $idUsuario)
            ->where('lf.status', 1)
            ->where('lf.compra_terceiros', 1)
            ->where('lfa.status', 1)
            ->get()
            ->result_array();
    }

    function sincronizarVinculoTerceiroPeriodo($idLancamento, $idUsuario, $nome, $mesReferencia, $anoReferencia)
    {
        $parcelasPendentes = $this->getParcelasTerceiroPeriodoParaVinculo(
            $idUsuario,
            $nome,
            $mesReferencia,
            $anoReferencia
        );

        if ($parcelasPendentes) {
            foreach ($parcelasPendentes as $parcela) {
                $this->vincularParcelaTerceiroAoLancamento($idLancamento, (object) $parcela);
            }
        }

        return $this->sincronizarLancamentoTerceiroVinculado($idLancamento, $idUsuario);
    }

    function vincularParcelaTerceiroAoLancamento($idLancamento, $parcela)
    {
        $vinculoExistente = $this->db
            ->select('id')
            ->from('lancamentos_terceiros_vinculos')
            ->where('id_lancamento', $idLancamento)
            ->where('id_lancamento_fatura_assoc', $parcela->id_assoc)
            ->limit(1)
            ->get()
            ->row();

        if ($vinculoExistente) {
            return true;
        }

        if (empty($parcela->id_lancamento) || empty($parcela->id_fatura) || empty($parcela->id_assoc)) {
            return false;
        }

        $this->db->insert('lancamentos_terceiros_vinculos', [
            'id_lancamento'              => $idLancamento,
            'id_lancamento_fatura'       => $parcela->id_lancamento,
            'id_fatura'                  => $parcela->id_fatura,
            'id_lancamento_fatura_assoc' => $parcela->id_assoc
        ]);

        return ($this->db->error()['code'] == 0);
    }

    function getParcelasTerceiroPeriodoParaVinculo($idUsuario, $nome, $mesReferencia, $anoReferencia)
    {
        if (!is_string($nome) || is_numeric($nome)) {
            return false;
        }

        return $this->db
            ->select('
                lfa.id_assoc,
                lfa.id_lancamento,
                lfa.valor_parcela,
                lfa.mes_referencia,
                lfa.ano_referencia,
                f.id_fatura,
                f.id_cartao,
                f.vencimento,
                c.apelido AS cartao_apelido,
                c.bandeira AS cartao_bandeira,
                c.numero AS cartao_numero
            ')
            ->from('lancamentos_faturas_assoc AS lfa')
            ->join('lancamentos_faturas AS lf', 'lf.id_lancamento = lfa.id_lancamento', 'inner')
            ->join('faturas AS f', 'f.id_fatura = lfa.id_fatura', 'inner')
            ->join('cartoes AS c', 'c.id_cartao = f.id_cartao', 'left')
            ->where('lf.id_usuario', $idUsuario)
            ->where('lf.status', 1)
            ->where('lf.compra_terceiros', 1)
            ->where('lf.nome_cliente', $nome)
            ->where('lfa.status', 1)
            ->where('lfa.mes_referencia', $mesReferencia)
            ->where('lfa.ano_referencia', $anoReferencia)
            ->where('lfa.parcela_terceiro_pago IS NULL', null, false)
            ->order_by('f.vencimento', 'ASC')
            ->get()
            ->result_array();
    }

    function getVinculoTerceiroPeriodo($idUsuario, $nome, $mesReferencia, $anoReferencia)
    {
        if (!is_string($nome) || is_numeric($nome)) {
            return false;
        }

        return $this->db
            ->select('l.*')
            ->from('lancamentos_terceiros_vinculos AS ltv')
            ->join('lancamentos AS l', 'l.id_lancamento = ltv.id_lancamento', 'inner')
            ->join('lancamentos_faturas_assoc AS lfa', 'lfa.id_assoc = ltv.id_lancamento_fatura_assoc', 'inner')
            ->join('lancamentos_faturas AS lf', 'lf.id_lancamento = lfa.id_lancamento', 'inner')
            ->where('l.status', 1)
            ->where('l.id_usuario', $idUsuario)
            ->where('lf.id_usuario', $idUsuario)
            ->where('lf.status', 1)
            ->where('lf.compra_terceiros', 1)
            ->where('lf.nome_cliente', $nome)
            ->where('lfa.status', 1)
            ->where('lfa.mes_referencia', $mesReferencia)
            ->where('lfa.ano_referencia', $anoReferencia)
            ->limit(1)
            ->get()
            ->row();
    }

    function vincularLancamentoTerceiro($idLancamento, array $idsLancamentosFaturasAssoc)
    {
        if (!$idLancamento || !$idsLancamentosFaturasAssoc) {
            return false;
        }

        $parcelas = $this->db
            ->select('id_assoc, id_lancamento, id_fatura')
            ->from('lancamentos_faturas_assoc')
            ->where_in('id_assoc', $idsLancamentosFaturasAssoc)
            ->get()
            ->result_array();

        if (!$parcelas) {
            return false;
        }

        foreach ($parcelas as $parcela) {
            $this->db->insert('lancamentos_terceiros_vinculos', [
                'id_lancamento'              => $idLancamento,
                'id_lancamento_fatura'       => $parcela['id_lancamento'],
                'id_fatura'                  => $parcela['id_fatura'],
                'id_lancamento_fatura_assoc' => $parcela['id_assoc']
            ]);

            if ($this->db->error()['code'] != 0) {
                return false;
            }
        }

        return true;
    }

    function getVinculosTerceiroPorLancamento($idLancamento, $idUsuario)
    {
        return $this->db
            ->select('ltv.id_lancamento_fatura_assoc')
            ->from('lancamentos_terceiros_vinculos AS ltv')
            ->join('lancamentos AS l', 'l.id_lancamento = ltv.id_lancamento', 'inner')
            ->where('ltv.id_lancamento', $idLancamento)
            ->where('l.id_usuario', $idUsuario)
            ->where('l.status', 1)
            ->get()
            ->result_array();
    }

    function getLancamentosTerceirosVinculadosPorCompra($idLancamentoFatura, $idUsuario)
    {
        return $this->db
            ->select('DISTINCT ltv.id_lancamento', false)
            ->from('lancamentos_terceiros_vinculos AS ltv')
            ->join('lancamentos AS l', 'l.id_lancamento = ltv.id_lancamento', 'inner')
            ->where('ltv.id_lancamento_fatura', $idLancamentoFatura)
            ->where('l.id_usuario', $idUsuario)
            ->where('l.status', 1)
            ->get()
            ->result_array();
    }

    function getMapaVinculosTerceiroPorCompra($idLancamentoFatura, $idUsuario)
    {
        return $this->db
            ->select('DISTINCT ltv.id_lancamento, ltv.id_fatura', false)
            ->from('lancamentos_terceiros_vinculos AS ltv')
            ->join('lancamentos AS l', 'l.id_lancamento = ltv.id_lancamento', 'inner')
            ->where('ltv.id_lancamento_fatura', $idLancamentoFatura)
            ->where('l.id_usuario', $idUsuario)
            ->where('l.status', 1)
            ->get()
            ->result_array();
    }

    function reconstruirVinculosTerceiroCompra($idLancamentoFatura, array $mapaVinculos, $idUsuario)
    {
        if (!$mapaVinculos) {
            return true;
        }

        $faturasPorLancamento = [];

        foreach ($mapaVinculos as $vinculo) {
            $faturasPorLancamento[$vinculo['id_lancamento']][] = $vinculo['id_fatura'];
        }

        foreach ($faturasPorLancamento as $idLancamento => $idsFaturas) {
            $this->removerVinculosTerceiroCompra($idLancamento, $idLancamentoFatura);

            $parcelasAtuais = $this->db
                ->select('lfa.id_assoc, lfa.id_fatura')
                ->from('lancamentos_faturas_assoc AS lfa')
                ->join('lancamentos_faturas AS lf', 'lf.id_lancamento = lfa.id_lancamento', 'inner')
                ->where('lfa.id_lancamento', $idLancamentoFatura)
                ->where_in('lfa.id_fatura', array_unique($idsFaturas))
                ->where('lfa.status', 1)
                ->where('lf.status', 1)
                ->where('lf.compra_terceiros', 1)
                ->where('lf.id_usuario', $idUsuario)
                ->get()
                ->result_array();

            foreach ($parcelasAtuais as $parcela) {
                $this->db->insert('lancamentos_terceiros_vinculos', [
                    'id_lancamento'              => $idLancamento,
                    'id_lancamento_fatura'       => $idLancamentoFatura,
                    'id_fatura'                  => $parcela['id_fatura'],
                    'id_lancamento_fatura_assoc' => $parcela['id_assoc']
                ]);

                if ($this->db->error()['code'] != 0) {
                    return false;
                }
            }
        }

        return true;
    }

    function removerVinculosTerceiroCompra($idLancamento, $idLancamentoFatura)
    {
        $this->db
            ->where('id_lancamento', $idLancamento)
            ->where('id_lancamento_fatura', $idLancamentoFatura)
            ->delete('lancamentos_terceiros_vinculos');

        return ($this->db->error()['code'] == 0);
    }

    function sincronizarLancamentosTerceiros(array $idsLancamentos, $idUsuario)
    {
        if (!$idsLancamentos) {
            return true;
        }

        foreach ($idsLancamentos as $idLancamento) {
            if (!$this->sincronizarLancamentoTerceiroVinculado($idLancamento, $idUsuario)) {
                return false;
            }
        }

        return true;
    }

    function sincronizarLancamentoTerceiroVinculado($idLancamento, $idUsuario)
    {
        $parcelas = $this->db
            ->select('
                ltv.id_lancamento_fatura_assoc,
                lfa.valor_parcela,
                f.vencimento,
                c.apelido AS cartao_apelido,
                c.bandeira AS cartao_bandeira,
                c.numero AS cartao_numero
            ')
            ->from('lancamentos_terceiros_vinculos AS ltv')
            ->join('lancamentos AS l', 'l.id_lancamento = ltv.id_lancamento', 'inner')
            ->join('lancamentos_faturas_assoc AS lfa', 'lfa.id_assoc = ltv.id_lancamento_fatura_assoc', 'inner')
            ->join('lancamentos_faturas AS lf', 'lf.id_lancamento = lfa.id_lancamento', 'inner')
            ->join('faturas AS f', 'f.id_fatura = lfa.id_fatura', 'inner')
            ->join('cartoes AS c', 'c.id_cartao = f.id_cartao', 'left')
            ->where('ltv.id_lancamento', $idLancamento)
            ->where('l.id_usuario', $idUsuario)
            ->where('l.status', 1)
            ->where('lf.id_usuario', $idUsuario)
            ->where('lf.status', 1)
            ->where('lfa.status', 1)
            ->where('lfa.parcela_terceiro_pago IS NULL', null, false)
            ->order_by('f.vencimento', 'ASC')
            ->get()
            ->result_array();

        if (!$parcelas) {
            $parcelasAtivas = $this->db
                ->select('ltv.id_lancamento_fatura_assoc')
                ->from('lancamentos_terceiros_vinculos AS ltv')
                ->join('lancamentos AS l', 'l.id_lancamento = ltv.id_lancamento', 'inner')
                ->join('lancamentos_faturas_assoc AS lfa', 'lfa.id_assoc = ltv.id_lancamento_fatura_assoc', 'inner')
                ->join('lancamentos_faturas AS lf', 'lf.id_lancamento = lfa.id_lancamento', 'inner')
                ->where('ltv.id_lancamento', $idLancamento)
                ->where('l.id_usuario', $idUsuario)
                ->where('l.status', 1)
                ->where('lf.id_usuario', $idUsuario)
                ->where('lf.status', 1)
                ->where('lfa.status', 1)
                ->limit(1)
                ->get()
                ->row();

            if ($parcelasAtivas) {
                return $this->edit('lancamentos', [
                    'valor'       => 0,
                    'observacoes' => null,
                ], 'id_lancamento', $idLancamento);
            }

            $this->removerVinculosTerceiroPorLancamento($idLancamento);
            $this->edit('lancamentos', ['status' => 0], 'id_lancamento', $idLancamento);
            return ($this->db->error()['code'] == 0);
        }

        $idsAssocAtivos = array_column($parcelas, 'id_lancamento_fatura_assoc');
        $total          = 0;
        $vencimento     = $parcelas[0]['vencimento'];
        $totaisCartao   = [];

        foreach ($parcelas as $parcela) {
            $total += $parcela['valor_parcela'];

            if (strtotime($parcela['vencimento']) < strtotime($vencimento)) {
                $vencimento = $parcela['vencimento'];
            }

            $numeroCartao = decriptar($parcela['cartao_numero']);
            $partesCartao = explode(' ', trim($numeroCartao));
            $finalCartao  = end($partesCartao);
            $cartaoLabel  = $parcela['cartao_apelido'] ?: $parcela['cartao_bandeira'] . ' - FINAL ' . $finalCartao;

            if (!isset($totaisCartao[$cartaoLabel])) {
                $totaisCartao[$cartaoLabel] = 0;
            }

            $totaisCartao[$cartaoLabel] += $parcela['valor_parcela'];
        }

        $observacoes = [];

        foreach ($totaisCartao as $cartaoLabel => $valorCartao) {
            $observacoes[] = $cartaoLabel . ': R$ ' . number_format($valorCartao, 2, ',', '.');
        }

        $this->removerVinculosTerceiroInvalidos($idLancamento, $idsAssocAtivos);

        return $this->edit('lancamentos', [
            'valor'           => $total,
            'data_lancamento' => $vencimento,
            'data_pagamento'  => $vencimento,
            'observacoes'     => implode("\n", $observacoes),
        ], 'id_lancamento', $idLancamento);
    }

    function removerVinculosTerceiroInvalidos($idLancamento, array $idsAssocAtivos)
    {
        $this->db->where('id_lancamento', $idLancamento);

        if ($idsAssocAtivos) {
            $this->db->where_not_in('id_lancamento_fatura_assoc', $idsAssocAtivos);
        }

        $this->db->delete('lancamentos_terceiros_vinculos');

        return ($this->db->error()['code'] == 0);
    }

    function removerVinculosTerceiroPorLancamento($idLancamento)
    {
        $this->db->where('id_lancamento', $idLancamento);
        $this->db->delete('lancamentos_terceiros_vinculos');

        return ($this->db->error()['code'] == 0);
    }

    function setParcelasTerceiroPagoPorVinculo($idLancamento, $idUsuario, $pago)
    {
        $vinculos = $this->getVinculosTerceiroPorLancamento($idLancamento, $idUsuario);

        if (!$vinculos) {
            return false;
        }

        $idsLancamentosFaturasAssoc = array_column($vinculos, 'id_lancamento_fatura_assoc');

        $this->db->where_in('id_assoc', $idsLancamentosFaturasAssoc);
        $this->db->update('lancamentos_faturas_assoc', [
            'parcela_terceiro_pago' => $pago ? 1 : null
        ]);

        return ($this->db->error()['code'] == 0);
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
        if ($this->db->affected_rows() == 1) {
            return true;
        }

        return false;
    }

    function delete_real($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

    function count($table, $where, $idCartao)
    {
        $this->db->from($table);

        if ($where) {
            $this->db->where($where);
        }

        if ($idCartao) {
            $this->db->where('id_cartao', $idCartao);
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
        $query  = $this->db->get();
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
            $total   = null;

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
        }
        return false;
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
        $count = $this->db
            ->from('lancamentos')
            ->where('status', 1)
            ->where('id_fatura', $idFatura);

        if ($count->count_all_results() > 0) {
            $this->db
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

    function getLancamentoFaturaById($idLancamento, $idUser = null)
    {
        $this->db->from('lancamentos_faturas AS lf');
        $this->db->join('lancamentos_faturas_assoc AS lfa', 'lf.id_lancamento = lfa.id_lancamento AND lf.status = lfa.status');

        $this->db->where('lf.id_lancamento', $idLancamento);
        $this->db->where('lf.status', 1);

        if ($idUser) {
            $this->db->where('lf.id_usuario', $idUser);
        }
        return $this->db->get()->result()[0];
    }

    function getFaturaByLancamentosAssoc($idLancamento)
    {
        $this->db->from('lancamentos_faturas_assoc');
        $this->db->where('id_lancamento', $idLancamento);
        return $this->db->get()->result_array('id_fatura');
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
            WHERE lf.nome_cliente LIKE '%$nome%'
            AND lfa.mes_referencia = $mesReferencia
            AND lfa.ano_referencia = $anoReferencia
            AND f.id_usuario = $idUsuario
            AND lf.status = 1
            AND lfa.status = 1
            GROUP BY f.id_cartao
            ORDER BY lf.criado_em DESC";

        $resultQuery = $this->db->query($query);
        $result      = $resultQuery->result_array();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function pesquisaLancamentosFaturas($term, $limit = null, $rows = 0, $perpage = 0, $start = null, $idUser = null)
    {
        if (!$idUser) $idUser = getUserId();

        if (!is_string($term) || is_numeric($term)) {
            return false;
        }
        $term = $this->db->escape_like_str($term);

        $start = $start ?? '0';
        $query = "SELECT f.*, lf.*, lfa.*
            FROM faturas f
            INNER JOIN lancamentos_faturas lf
            ON lf.id_fatura = f.id_fatura
            INNER JOIN lancamentos_faturas_assoc lfa
            ON lfa.id_lancamento = lf.id_lancamento
            WHERE (
                lf.descricao LIKE '%$term%'
                OR lf.observacoes LIKE '%$term%'
                OR lf.nome_cliente LIKE '%$term%'
            )
            AND f.id_usuario = $idUser
            AND f.status = 1
            AND lf.status = 1
            AND lfa.status = 1
            GROUP BY lf.id_lancamento
            ORDER BY lf.criado_em DESC
            LIMIT $start, $perpage
            ";


        if ($limit) {
            if ($rows > $limit) {
                $this->db->limit($limit, ($rows - $limit));
            } else {
                $this->db->limit($limit, $start);
            }
        }

        $resultQuery = $this->db->query($query);
        $result      = $resultQuery->result_array();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function countPesquisaLancamentosFaturas($term, $idUser = null)
    {
        if (!$idUser) $idUser = getUserId();

        if (!is_string($term) || is_numeric($term)) {
            return false;
        }
        $term = $this->db->escape_like_str($term);

        $query = "SELECT f.*, lf.*, lfa.*
            FROM faturas f
            INNER JOIN lancamentos_faturas lf
            ON lf.id_fatura = f.id_fatura
            INNER JOIN lancamentos_faturas_assoc lfa
            ON lfa.id_lancamento = lf.id_lancamento
            WHERE (
                lf.descricao LIKE '%$term%'
                OR lf.observacoes LIKE '%$term%'
                OR lf.nome_cliente LIKE '%$term%'
            )
            AND f.id_usuario = $idUser
            AND f.status = 1
            AND lf.status = 1
            AND lfa.status = 1
            GROUP BY lf.id_lancamento
            ORDER BY lf.criado_em DESC";

        $resultQuery = $this->db->query($query);
        $result      = $resultQuery->num_rows();

        if (!$result) {
            return false;
        }
        return $result;
    }

    function getLancamentosTerceiros($idUsuario, $idCartao, $nome, $mesReferencia, $anoReferencia)
    {
        if (!is_string($nome) || is_numeric($nome)) {
            return false;
        }

        $query = "SELECT lf.*,
            lfa.*,
            f.id_cartao,
            (
                SELECT COUNT(*)
                FROM lancamentos_faturas_assoc lfa_total
                WHERE lfa_total.id_lancamento = lf.id_lancamento
                AND lfa_total.status = 1
            ) AS parcelas_compra_total,
            (
                SELECT COUNT(*)
                FROM lancamentos_faturas_assoc lfa_pagas
                WHERE lfa_pagas.id_lancamento = lf.id_lancamento
                AND lfa_pagas.status = 1
                AND lfa_pagas.parcela_terceiro_pago = 1
            ) AS parcelas_compra_pagas
            FROM lancamentos_faturas lf
            INNER JOIN faturas f
            ON lf.id_fatura = f.id_fatura
            INNER JOIN lancamentos_faturas_assoc lfa
            ON lfa.id_lancamento = lf.id_lancamento
            WHERE lf.nome_cliente LIKE '%$nome%'
            AND f.id_usuario = $idUsuario
            AND f.id_cartao = $idCartao
            AND lfa.mes_referencia = $mesReferencia
            AND lfa.ano_referencia = $anoReferencia
            AND lf.status = 1
            AND lfa.status = 1
            ORDER BY lf.criado_em DESC";

        $resultQuery = $this->db->query($query);
        $result      = $resultQuery->result_array();

        if (!$result) {
            return false;
        }

        return $result;
    }

    function getCompraTerceiro($idUsuario, $idLancamento)
    {
        return $this->db
            ->select('lf.*, c.apelido AS cartao_apelido, c.bandeira AS cartao_bandeira, c.numero AS cartao_numero')
            ->from('lancamentos_faturas AS lf')
            ->join('faturas AS f', 'f.id_fatura = lf.id_fatura', 'inner')
            ->join('cartoes AS c', 'c.id_cartao = f.id_cartao', 'left')
            ->where('lf.id_lancamento', $idLancamento)
            ->where('lf.id_usuario', $idUsuario)
            ->where('lf.status', 1)
            ->where('lf.compra_terceiros', 1)
            ->get()
            ->row();
    }

    function getParcelasCompraTerceiro($idUsuario, $idLancamento)
    {
        $query = "SELECT lfa.*,
            lf.descricao,
            lf.nome_cliente,
            lf.valor_total,
            lf.total_parcelas,
            lf.data_compra,
            lf.observacoes,
            lf.estorno,
            f.id_cartao,
            f.vencimento,
            f.fatura_aberta,
            c.apelido AS cartao_apelido,
            c.bandeira AS cartao_bandeira,
            c.numero AS cartao_numero
            FROM lancamentos_faturas_assoc lfa
            INNER JOIN lancamentos_faturas lf
            ON lf.id_lancamento = lfa.id_lancamento
            INNER JOIN faturas f
            ON f.id_fatura = lfa.id_fatura
            LEFT JOIN cartoes c
            ON c.id_cartao = f.id_cartao
            WHERE lfa.id_lancamento = $idLancamento
            AND lf.id_usuario = $idUsuario
            AND lf.status = 1
            AND lfa.status = 1
            AND lf.compra_terceiros = 1
            ORDER BY lfa.n_parcela ASC";

        $resultQuery = $this->db->query($query);
        $result      = $resultQuery->result_array();

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
            ->order_by('id_lancamento', 'desc')
            ->get('lancamentos_faturas');

        if ($query->num_rows() > 0) {
            $row_set = [];

            foreach ($query->result_array() as $row) {
                $row_set[] = [
                    'label' => $row['nome_cliente']
                ];
            }
            return json_encode($row_set);
        }
        return false;
    }

    function autoCompleteDescricao($query, $idUsuario)
    {
        $query = $this->db->select('*')
            ->limit(5)
            ->like('descricao', $query)
            ->where('id_usuario', $idUsuario)
            ->where('status', 1)
            ->group_by('descricao')
            ->order_by('id_lancamento', 'desc')
            ->get('lancamentos_faturas');

        if ($query->num_rows() > 0) {
            $row_set = [];

            foreach ($query->result_array() as $row) {
                $row_set[] = [
                    'label' => $row['descricao']
                ];
            }
            return json_encode($row_set);
        }
        return false;
    }

    function atualizaDescricao($term, $dataList)
    {
        $this->db->where('descricao', $term);
        $this->db->where('id_usuario', getUserId());
        $this->db->where('status', 1);
        $this->db->update('lancamentos_faturas', $dataList);

        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

    function getAllTerceiros($idCartao = null, $mesReferencia = null, $anoReferencia = null, $idUsuario = null)
    {
        if (!$idUsuario) {
            $idUsuario = getUserId();
        }

        $mainQuery = "SELECT DISTINCT
            lf.nome_cliente
            FROM lancamentos_faturas lf
            INNER JOIN faturas f
            ON lf.id_fatura = f.id_fatura
            INNER JOIN lancamentos_faturas_assoc lfa
            ON lfa.id_lancamento = lf.id_lancamento
            WHERE f.id_usuario = $idUsuario
            AND lfa.status = 1
            AND lf.nome_cliente IS NOT NULL
            AND lf.nome_cliente != ''
        ";

        $where = "";

        if ($idCartao) {
            $where .= " AND f.id_cartao = $idCartao ";
        }

        if ($mesReferencia) {
            $where .= " AND lfa.mes_referencia = $mesReferencia";
        }

        if ($anoReferencia) {
            $where .= " AND lfa.ano_referencia = $anoReferencia";
        }

        $groupBy     = " GROUP BY lf.nome_cliente";
        $orderBy     = " ORDER BY lf.nome_cliente ASC";
        $mainQuery   .= $where . $orderBy;
        $resultQuery = $this->db->query($mainQuery);

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

    function getLancamentosPendentesTerceiros($id_usuario, $referenceMonth, $referenceYear, $terceiro)
    {
        $query = "SELECT 1
            FROM lancamentos_faturas lf
            JOIN lancamentos_faturas_assoc lfa
            ON lf.id_lancamento = lfa.id_lancamento
            JOIN faturas f
            ON lfa.id_fatura = f.id_fatura
            WHERE lf.id_usuario = $id_usuario
            AND f.fatura_aberta IN (1, 2)
            AND lf.nome_cliente LIKE '%$terceiro%'
            AND lfa.mes_referencia = $referenceMonth
            AND lfa.ano_referencia = $referenceYear
            AND lfa.parcela_terceiro_pago IS NULL
            LIMIT 1
        ";

        $result      = (bool) $this->db->query($query)->row();

        if (!$result) {
            return false;
        }

        return $result;
    }

    function setAutoLinkToAllUserActiveCards()
    {
        $this->db->where('id_usuario', getUserId());
        $this->db->update('configs_faturas', ['auto_vinculo' => 1]);

        if (($this->db->error()['code'] != 0)) {
            return $this->db->error()['code']['message']; // Or do whatever you gotta do here to raise an error
        }
        return true;
    }

    function unsetAutoLinkToAllUserActiveCards()
    {
        $this->db->where('id_usuario', getUserId());
        $this->db->update('configs_faturas', ['auto_vinculo' => null]);

        if (($this->db->error()['code'] != 0)) {
            return $this->db->error()['code']['message']; // Or do whatever you gotta do here to raise an error
        }
        return true;
    }

    function getAutoLinkUser()
    {
        $this->db
            ->from('configs_faturas')
            ->where('id_usuario', getUserId())
            ->where('auto_vinculo', 1);

        if ($this->db->count_all_results()) {
            return true;
        }
        return false;
    }

    function getVinculoFaturaComModuloLancamentos($idFatura)
    {
        $count = $this->db
            ->from('lancamentos')
            ->where('status', 1)
            ->where('id_usuario', $this->userId)
            ->where('id_fatura', $idFatura);

        if ($count->count_all_results() > 0) {
            $this->db
                ->from('lancamentos')
                ->where('status', 1)
                ->where('id_usuario', $this->userId)
                ->where('id_fatura', $idFatura);
            return $this->db->get()->row();
        }
        return false;
    }

    function setFlagFaturaPaga($idFatura, $dataToUpdate)
    {
        if (!is_array($dataToUpdate)) return false;

        $this->db->where('id_fatura', $idFatura);
        $this->db->update($this->faturasTable, $dataToUpdate);

        if ($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }
}
