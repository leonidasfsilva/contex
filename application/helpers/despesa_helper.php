<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function integracaoDespesasUsuario()
{
    $CI = get_instance();
    $CI->load->model('despesa_model');

    $despesas = $CI->despesa_model->getDespesas();

    if ($despesas) {
        foreach ($despesas as $despesa) {
            vinculoAutomaticoDespesa($despesa->id);

            $lancamentosDespesa = $CI->despesa_model->getLancamentosDespesa($despesa->id);

            if ($lancamentosDespesa) {
                foreach ($lancamentosDespesa as $lancamento) {
                    monitoraVinculoComModuloLancamentos($lancamento->id);
                    monitoraIntegracaoAtivaComModuloLancamentos($lancamento->id);
                }
            }
        }
    }
    return true;
}

function monitoraVinculoComModuloLancamentos($idLancamentoDespesa)
{
    $CI = get_instance();
    $CI->load->model('despesa_model');

    $lancamento = $CI->despesa_model->getDetalhesLancamentoDespesa($idLancamentoDespesa);
    $vinculo    = null;

    if ($lancamento->data_vencimento && $lancamento->data_vencimento != '0000-00-00')
        $vinculo = $CI->despesa_model->getVinculoDespesaComModuloLancamentos($lancamento->id_despesa, $lancamento->data_vencimento);

    if (!$vinculo)
        $CI->despesa_model->unsetFlagRegistroVinculado($lancamento->id);

    if ($vinculo) {
        $CI->despesa_model->setFlagRegistroVinculado($lancamento->id);

        if ($vinculo->baixado)
            $CI->despesa_model->setFlagRegistroPago($lancamento->id);

        if (!$vinculo->baixado)
            $CI->despesa_model->unsetFlagRegistroPago($lancamento->id);
    }
    return true;
}

function monitoraIntegracaoAtivaComModuloLancamentos($idLancamentoDespesa)
{
    $CI = get_instance();
    $CI->load->model('despesa_model');

    $lancamento = $CI->despesa_model->getDetalhesLancamentoDespesa($idLancamentoDespesa);
    $vinculo    = null;

    if (!$lancamento->registro_vinculado) return false;

    if ($lancamento->data_vencimento && $lancamento->data_vencimento != '0000-00-00')
        $vinculo = $CI->despesa_model->getVinculoDespesaComModuloLancamentos($lancamento->id_despesa, $lancamento->data_vencimento);

    if ($vinculo) {
        if ($vinculo->baixado)
            $CI->despesa_model->setFlagRegistroPago($lancamento->id);

        if (!$vinculo->baixado)
            $CI->despesa_model->unsetFlagRegistroPago($lancamento->id);

        if ($lancamento->valor != abs($vinculo->valor))
            $CI->despesa_model->atualizaValorRegistro($lancamento->id, abs($vinculo->valor));
    }
    return true;
}

function vinculoAutomaticoDespesa($idDespesa)
{
    $CI = get_instance();
    $CI->load->model('despesa_model');

    $todayDate   = date('Y-m-d');
    $todayArray  = explode('-', $todayDate);
    $monthsCount = 3;
    $despesa     = $CI->despesa_model->getDespesabyId($idDespesa);
    $lancamentos = $CI->despesa_model->getLancamentosDespesa($idDespesa);

    if (!$despesa) return false;

    // if ($despesa->tipo_despesa != 1) return false;

    if (!$despesa->auto_vinculo) return false;

    $monthReference = $todayArray[1];
    $yearReference  = $todayArray[0];

    if ($lancamentos && count($lancamentos) == 1) {
        $monthReference = $lancamentos[0]->mes_referencia;
        $yearReference  = $lancamentos[0]->ano_referencia;
    }

    for ($i = 1; $i <= $monthsCount; $i++) {
        if ($lancamentos) {
            $monthsCount = 4;
            if ($monthReference < $lancamentos[0]->mes_referencia) {
                continue;
            }
        }

        if ($monthReference < 10) {
            $monthReference = sprintf('0%s', abs($monthReference));
        }

        if ($monthReference == '13') {
            $monthReference = '01';
            $yearReference++;
        }

        $newDueDate = sprintf('%s-%s-%s', $yearReference, $monthReference, $despesa->dia_vencimento);
        criaLancamentoDespesa($idDespesa, $newDueDate);
        copiaRegistroEmModuloLancamentos($despesa->id, $newDueDate);
        $monthReference++;
    }
    return true;
}

function copiaRegistroEmModuloLancamentos($idDespesa, $dataReferencia)
{
    $CI = get_instance();
    $CI->load->model('despesa_model');
    $CI->load->model('financeiro_model');

    $lancamento = $CI->despesa_model->getRegistroDespesaByDate($idDespesa, $dataReferencia);
    $vinculo    = $CI->despesa_model->getVinculoDespesaComModuloLancamentos($idDespesa, $dataReferencia);
    $despesa    = $CI->despesa_model->getDespesaById($idDespesa);

    if (!$lancamento) return false;

    $valorFormatado = sprintf('-%s', $lancamento->valor);
    $tipoLancamento = 2;
    $data           = [];

    if (!$despesa) return false;

    if ($vinculo) return false;

    if ($despesa->despesa_terceiros) {
        $valorFormatado = sprintf('%s', $lancamento->valor);
        $tipoLancamento = 1;
    }

    $data = [
        'id_usuario'         => getUserId(),
        'id_despesa'         => $despesa->id,
        'descricao'          => $despesa->descricao,
        'observacoes'        => $despesa->observacoes ?? null,
        'cliente_fornecedor' => $despesa->nome_terceiro ?? $despesa->fornecedor ?? null,
        'valor'              => $valorFormatado,
        'data_lancamento'    => $dataReferencia,
        'data_pagamento'     => $lancamento->data_pagamento ?: null,
        'forma_pgto'         => $despesa->id_forma_pagamento,
        'baixado'            => ($lancamento->registro_pago) ?: 0,
        'tipo'               => $tipoLancamento
    ];

    if ($despesa->despesa_parcelada) {
        $parcela           = $lancamento->num_parcela;
        $totalParcelas     = $despesa->total_parcelas;
        $data['descricao'] = sprintf('%s - PARC %s/%s', $data['descricao'], $parcela, $totalParcelas);
    }

    if (!$CI->financeiro_model->add('lancamentos', $data))
        return false;

    return true;
}

function criaLancamentoDespesa($idDespesa, $dataVencimento)
{
    $CI = get_instance();
    $CI->load->model('despesa_model');

    $despesa    = $CI->despesa_model->getDespesaById($idDespesa);
    $lancamento = $CI->despesa_model->getRegistroDespesaByDate($idDespesa, $dataVencimento);

    if (!$despesa) return false;

    if ($lancamento) return false;

    $vencimento = explode('-', $dataVencimento);

    $newLancamentoDespesa = [
        'id_despesa'         => $despesa->id,
        'valor'              => $despesa->valor_parcela,
        'id_forma_pagamento' => $despesa->id_forma_pagamento,
        'data_vencimento'    => $dataVencimento ?? null,
        'mes_referencia'     => $vencimento[1],
        'ano_referencia'     => $vencimento[0],
    ];

    if ($despesa->despesa_parcelada) {
        $parcela       = '01';
        $ultimaParcela = $CI->despesa_model->getUltimaParcelaDespesa($idDespesa);

        if ($ultimaParcela) {
            $parcela = $ultimaParcela->num_parcela + 1;

            if ($parcela < 10) $parcela = '0' . $parcela;
        }

        if ($parcela > $despesa->total_parcelas) return false;

        $newLancamentoDespesa['num_parcela'] = (string)$parcela;
    }

    if (!$CI->despesa_model->addLancamentoDespesa($newLancamentoDespesa)) return false;

    return true;
}