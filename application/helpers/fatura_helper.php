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

function vinculoAutomaticoFaturas(): bool
{
    $CI = get_instance();
    $CI->load->model('fatura_model');
    $CI->load->model('cartoes_model');

    if (!$CI->fatura_model->getAutoLinkUser()) return false;

    $todayDate     = date('Y-m-d');
    $todayArray    = explode('-', $todayDate);
    $monthsCount   = 3;
    $cartoesAtivos = $CI->cartoes_model->getCartoesAtivosUsuario(getUserId());

    foreach ($cartoesAtivos as $cartao) {
        $faturas         = $CI->fatura_model->getFaturasCartaoUser($cartao->id_cartao);
        $mounthReference = $todayArray[1];
        $yearReference   = $todayArray[0];

        for ($i = 1; $i <= $monthsCount; $i++) {
            $faturaReferencia = $CI->fatura_model->getFaturaReferencia($cartao->id_cartao, $mounthReference, $yearReference);

            if (!$faturaReferencia) continue;

            vinculaFatura($faturaReferencia->id_fatura);
            $mounthReference++;

            if ($mounthReference == 13) {
                $mounthReference = 1;
                $yearReference++;
            }
        }

        if ($faturas) {
            foreach ($faturas as $fatura) {
                monitoraPagamentosFaturasVinculadas($fatura->id_fatura);
            }
        }
    }

    return true;
}

function monitoraPagamentosFaturasVinculadas($idFatura)
{
    if (!$idFatura) return false;

    $CI = get_instance();
    $CI->load->model('fatura_model');

    $vinculo = $CI->fatura_model->getVinculoFaturaComModuloLancamentos($idFatura);

    if ($vinculo) {
        if ($vinculo->baixado) {
            $dataToUpdate = [
                'data_pagamento' => $vinculo->data_pagamento,
                'forma_pgto'     => $vinculo->forma_pgto,
                'fatura_paga'    => 1
            ];

            $CI->fatura_model->setFlagFaturaPaga($vinculo->id_fatura, $dataToUpdate);
        }
    }
    return true;
}

function atualizaValorVinculoFaturas($idFatura = null): bool
{
    $CI = get_instance();
    $CI->load->model('fatura_model');
    $CI->load->model('cartoes_model');

    vinculoAutomaticoFaturas();

    if ($idFatura) {
        $valorTotalFatura = $CI->fatura_model->getValorTotalFatura($idFatura);
        $vinculoFatura    = $CI->fatura_model->getVinculoFatura($idFatura);
        $data             = [
            'valor' => '-' . $valorTotalFatura
        ];

        if (!$vinculoFatura) return false;

        if ($CI->fatura_model->edit('lancamentos', $data, 'id_fatura', $idFatura))
            return true;

        return false;
    }

    $todayDate     = date('Y-m-d');
    $todayArray    = explode('-', $todayDate);
    $mesReferencia = $todayArray[1];
    $anoReferencia = $todayArray[0];
    $cartoesAtivos = $CI->cartoes_model->getCartoesAtivosUsuario(getUserId());

    foreach ($cartoesAtivos as $cartao) {
        $faturaReferencia = $CI->fatura_model->getFaturaReferencia($cartao->id_cartao, $mesReferencia, $anoReferencia);

        if (!$faturaReferencia) continue;

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

        if ($vinculoFatura)
            $CI->financeiro_model->edit('lancamentos', $lancamentosList, 'id_lancamento', $vinculoFatura->id_lancamento);

    }
    return true;
}

function vinculaFatura($idFatura)
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
