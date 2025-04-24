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
            $lancamentosDespesa = $CI->despesa_model->getLancamentosDespesa($despesa->id);

            vinculoAutomaticoDespesaRecorrente($despesa->id);

            if ($lancamentosDespesa) {
                foreach ($lancamentosDespesa as $lancamento) {
                    //TODO: separar lógica de monitoramento de registros já vinculados
                    // implementar metodo monitoraIntegracaoAtivaComModuloLancamentos()

                    monitoraVinculoComModuloLancamentos($lancamento->id);
                    monitoraIntegracaoAtivaComModuloLancamentos($lancamento->id);


                    //TODO: separar logica de integração (vinculo automatico) para despesas do tipo RECORRENTE
                    //TODO: a despesa deve possuir flag auto_vinculo = 1
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

    // $CI->copiaRegistroEmModuloLancamentos($despesa->id, $lancamento->data_vencimento);

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

function vinculoAutomaticoDespesaRecorrente($idDespesa)
{
    $CI = get_instance();
    $CI->load->model('despesa_model');

    $todayDate   = date('Y-m-d');
    $todayArray  = explode('-', $todayDate);
    $monthsCount = 3;
    // $lancamento  = $CI->despesa_model->getDetalhesLancamentoDespesa($idLancamentoDespesa);
    $despesa = $CI->despesa_model->getDespesabyId($idDespesa);

    if (!$despesa) return false;

    if (!$despesa->auto_vinculo) return false;

    //TODO: separar logica de integração (vinculo automatico) para despesas do tipo RECORRENTE
    //TODO: a despesa deve possuir flag auto_vinculo = 1 e possuir pelo menos um registro com data de vencimento válida

    // if ($lancamento->data_vencimento && $lancamento->data_vencimento != '0000-00-00') {
    $monthReference = $todayArray[1];
    $yearReference  = $todayArray[0];

    for ($i = 1; $i <= $monthsCount; $i++) {
        $newDueDate = sprintf('%s-%s-%s', $yearReference, $monthReference, $despesa->dia_vencimento);
        //TODO: criar um novo registro na tabela lancamentos_despesas, e em seguida,

        $monthReference++;
        if ($monthReference < 10) {
            $monthReference = '0' . $monthReference;
        }

        if ($monthReference == '13') {
            $monthReference = '01';
            $yearReference++;
        }

        //TODO: realizar o vínculo do mesmo com modulo Lançamentos
        criaLancamentoDespesa($idDespesa, $newDueDate);
        copiaRegistroEmModuloLancamentos($despesa->id, $newDueDate);
    }
    // }

    return true;
}

function copiaRegistroEmModuloLancamentos($idDespesa, $dataReferencia)
{
    $CI = get_instance();
    $CI->load->model('despesa_model');
    $CI->load->model('financeiro_model');

    $lancamento     = $CI->despesa_model->getRegistroDespesaByDate($idDespesa, $dataReferencia);
    $vinculo        = $CI->despesa_model->getVinculoDespesaComModuloLancamentos($idDespesa, $dataReferencia);
    $despesa        = $CI->despesa_model->getDespesaById($idDespesa);
    $valorFormatado = sprintf('-%s', $lancamento->valor);
    $tipoLancamento = 2;

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

    // if ($detalhesDespesa->tipo_despesa == 2) {
    //     $newLancamentoDespesa['despesa_vinculada'] = 1;
    // }

    // if ($despesa->despesa_parcelada) {
    //     for ($i = 1; $i <= $despesa->total_parcelas; $i++) {
    //         (string)$contadorParcela = $i;
    //         $newLancamentoDespesa['num_parcela'] = $contadorParcela < 10 ? '0' . $contadorParcela : $contadorParcela;
    //
    //         if (!$CI->despesa_model->addLancamentoDespesa($newLancamentoDespesa)) return false;
    //     }
    //     return true;
    // }

    if (!$CI->despesa_model->addLancamentoDespesa($newLancamentoDespesa)) return false;

    return true;
}


function _getVinculoFatura($idFatura)
{
    if (!$idFatura) {
        return false;
    }
    $CI = get_instance();
    $CI->load->model('fatura_model', 'faturaModel');

    return $CI->faturaModel->getVinculoFatura($idFatura);
}

function _atualizaValorVinculoFaturas($idFatura = null): bool
{
    $CI = get_instance();
    $CI->load->model('fatura_model');
    $CI->load->model('cartoes_model');

    if ($idFatura) {
        $valorTotalFatura = $CI->fatura_model->getValorTotalFatura($idFatura);
        $vinculoFatura    = $CI->fatura_model->getVinculoFatura($idFatura);
        $data             = [
            'valor' => '-' . $valorTotalFatura
        ];

        if (!$vinculoFatura) {
            return false;
        }

        if ($CI->fatura_model->edit('lancamentos', $data, 'id_fatura', $idFatura)) {
            return true;
        }
        return false;
    }

    $todayDate     = date('Y-m-d');
    $todayArray    = explode('-', $todayDate);
    $mesReferencia = $todayArray[1];
    $anoReferencia = $todayArray[0];
    $cartoesAtivos = $CI->cartoes_model->getCartoesUsuarioFatura(getUserId());

    foreach ($cartoesAtivos as $cartao) {
        $faturaReferencia = $CI->fatura_model->getFaturaReferencia($cartao->id_cartao, $mesReferencia, $anoReferencia);

        if (!$faturaReferencia) {
            continue;
        }

        $vinculoFatura        = $CI->fatura_model->getVinculoFatura($faturaReferencia->id_fatura);
        $detalhesFatura       = $CI->fatura_model->getDetalhesFatura($faturaReferencia->id_fatura);
        $valorTotalFatura     = $CI->fatura_model->getValorTotalFatura($faturaReferencia->id_fatura);
        $detalhesCartaoFatura = $CI->cartoes_model->getCartao($detalhesFatura->id_cartao);
        $n_cartao             = explode(" ", trim(decriptar($detalhesCartaoFatura->numero)));
        $final                = $n_cartao[3];
        $apelido              = $detalhesCartaoFatura->apelido ? sprintf('- %s', $detalhesCartaoFatura->apelido) : null;

        $lancamentosList = [
            'id_usuario'         => getUserId(),
            'id_fatura'          => $faturaReferencia->id_fatura,
            'descricao'          => sprintf('FATURA CARTAO DE CREDITO %s', $apelido),
            'cliente_fornecedor' => $detalhesCartaoFatura->bandeira ? $detalhesCartaoFatura->bandeira . ' - FINAL ' . $final : null,
            'valor'              => sprintf('-%s', $valorTotalFatura),
            'data_lancamento'    => $detalhesFatura->vencimento ?? $detalhesFatura->data_pagamento,
            'data_pagamento'     => $detalhesFatura->data_pagamento ?? $detalhesFatura->vencimento,
            'forma_pgto'         => $detalhesFatura->forma_pgto ?? 5,
            'baixado'            => ($detalhesFatura->fatura_paga == 1),
            'tipo'               => 2
        ];

        if ($vinculoFatura) {
            $CI->financeiro_model->edit('lancamentos', $lancamentosList, 'id_lancamento', $vinculoFatura->id_lancamento);
        }
    }
    return true;
}

function _vincularFatura($idFatura): bool
{
    if (!$idFatura) {
        return false;
    }
    $CI = get_instance();
    $CI->load->model('fatura_model');

    $vinculoFatura = $CI->fatura_model->getVinculoFatura($idFatura);

    if ($vinculoFatura) {
        return false;
    }

    $data = [
        'fatura_vinculada' => 1
    ];

    if ($CI->fatura_model->edit('faturas', $data, 'id_fatura', $idFatura)) {
        $detalhesFatura       = $CI->fatura_model->getDetalhesFatura($idFatura);
        $valorTotalFatura     = $CI->fatura_model->getValorTotalFatura($idFatura);
        $detalhesCartaoFatura = $CI->cartoes_model->getCartao($detalhesFatura->id_cartao);
        $n_cartao             = explode(" ", trim(decriptar($detalhesCartaoFatura->numero)));
        $final                = $n_cartao[3];
        $apelido              = $detalhesCartaoFatura->apelido ? ' - ' . $detalhesCartaoFatura->apelido : null;

        $data = array(
            'id_usuario'         => getUserId(),
            'id_fatura'          => $idFatura,
            'descricao'          => 'FATURA CARTAO DE CREDITO' . $apelido,
            'cliente_fornecedor' => $detalhesCartaoFatura->bandeira ? $detalhesCartaoFatura->bandeira . ' - FINAL ' . $final : null,
            'valor'              => '-' . $valorTotalFatura,
            'data_lancamento'    => $detalhesFatura->vencimento ?? $detalhesFatura->data_pagamento,
            'data_pagamento'     => $detalhesFatura->data_pagamento ?? $detalhesFatura->vencimento,
            'forma_pgto'         => $detalhesFatura->forma_pgto ?? 5,
            'baixado'            => ($detalhesFatura->fatura_paga == 1),
            'tipo'               => 2
        );

        $CI->financeiro_model->add('lancamentos', $data);
    }
    return true;
}

function _desvincularFatura($idFatura): bool
{
    if (!$idFatura) {
        return false;
    }
    $CI = get_instance();
    $CI->load->model('fatura_model');

    $data = array(
        'fatura_vinculada' => 0
    );

    if ($CI->fatura_model->edit('faturas', $data, 'id_fatura', $idFatura)) {
        $vinculoFatura = $CI->fatura_model->getVinculoFatura($idFatura);
        if (!$vinculoFatura) {
            return false;
        }

        $CI->fatura_model->delete_real('lancamentos', 'id_fatura', $idFatura);
        return true;
    }
    return false;
}

function desvincularDespesa($idDespesa): bool
{
    return true;
}
