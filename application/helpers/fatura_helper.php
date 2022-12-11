<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function getVinculoFatura($idFatura)
{
    if (!$idFatura) {
        return false;
    }
    $CI = get_instance();
    $CI->load->model('fatura_model', 'faturaModel');

    return $CI->faturaModel->getVinculoFatura($idFatura);
}

function atualizaValorVinculoFaturas($idFatura = null)
{
    $CI = get_instance();
    $CI->load->model('fatura_model');
    $CI->load->model('cartoes_model');

    if ($idFatura) {
        $valorTotalFatura   = $CI->fatura_model->getValorTotalFatura($idFatura);
        $vinculoFatura      = $CI->fatura_model->getVinculoFatura($idFatura);
        $data               = [
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

    $todayDate      = date('Y-m-d');
    $todayArray     = explode('-', $todayDate);
    $mesReferencia  = $todayArray[1];
    $anoReferencia  = $todayArray[0];
    $cartoesAtivos  = $CI->cartoes_model->getCartoesUsuarioFatura(getUserId());

    foreach ($cartoesAtivos as $cartao) {
        $faturaReferencia = $CI->fatura_model->getFaturaReferencia($cartao->id_cartao, $mesReferencia, $anoReferencia);

        if (!$faturaReferencia) {
            continue;
        }

        $vinculoFatura          = $CI->fatura_model->getVinculoFatura($faturaReferencia->id_fatura);
        $detalhesFatura         = $CI->fatura_model->getDetalhesFatura($faturaReferencia->id_fatura);
        $valorTotalFatura       = $CI->fatura_model->getValorTotalFatura($faturaReferencia->id_fatura);
        $detalhesCartaoFatura   = $CI->cartoes_model->getCartao($detalhesFatura->id_cartao);
        $n_cartao               = explode(" ", trim(decriptar($detalhesCartaoFatura->numero)));
        $final                  = $n_cartao[3];
        $apelido                = $detalhesCartaoFatura->apelido ? sprintf('- %s', $detalhesCartaoFatura->apelido) : null;

        $lancamentosList = [
            'id_usuario'            => getUserId(),
            'id_fatura'             => $faturaReferencia->id_fatura,
            'descricao'             => sprintf('FATURA CARTAO DE CREDITO %s', $apelido),
            'cliente_fornecedor'    => $detalhesCartaoFatura->bandeira ? $detalhesCartaoFatura->bandeira . ' - FINAL ' . $final : null,
            'valor'                 => sprintf('-%s', $valorTotalFatura),
            'data_lancamento'       => $detalhesFatura->vencimento ?? $detalhesFatura->data_pagamento,
            'data_pagamento'        => $detalhesFatura->data_pagamento ?? $detalhesFatura->vencimento,
            'forma_pgto'            => $detalhesFatura->forma_pgto ?? 5,
            'baixado'               => ($detalhesFatura->fatura_paga == 1),
            'tipo'                  => 2
        ];

        if ($vinculoFatura) {
            $CI->financeiro_model->edit('lancamentos', $lancamentosList, 'id_lancamento', $vinculoFatura->id_lancamento);
        }
    }
    return true;
}

function desvinculaFatura($idFatura)
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
