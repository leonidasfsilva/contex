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

function vinculoAutomaticoFaturas($idUsuario = null): bool
{
    $CI = get_instance();
    $CI->load->model('fatura_model');
    $CI->load->model('cartoes_model');

    $idUsuario = $idUsuario ?: getUserId();

    if (!$CI->fatura_model->getAutoLinkUser($idUsuario)) return false;

    $todayDate     = date('Y-m-d');
    $todayArray    = explode('-', $todayDate);
    $monthsCount   = 3;
    $cartoesAtivos = $CI->cartoes_model->getCartoesAtivosUsuario($idUsuario);

    foreach ($cartoesAtivos as $cartao) {
        $faturas         = $CI->fatura_model->getFaturasCartaoUser($cartao->id_cartao, $idUsuario);
        $mounthReference = $todayArray[1];
        $yearReference   = $todayArray[0];

        for ($i = 1; $i <= $monthsCount; $i++) {
            $faturaReferencia = $CI->fatura_model->getFaturaReferencia($cartao->id_cartao, $mounthReference, $yearReference);

            if (!$faturaReferencia) continue;

            vinculaFatura($faturaReferencia->id_fatura, $idUsuario);
            $mounthReference++;

            if ($mounthReference == 13) {
                $mounthReference = 1;
                $yearReference++;
            }
        }

        if ($faturas) {
            foreach ($faturas as $fatura) {
                monitoraPagamentosFaturasVinculadas($fatura->id_fatura, $idUsuario);
            }
        }
    }

    return true;
}

function monitoraPagamentosFaturasUsuario($idUsuario = null): bool
{
    $CI = get_instance();
    $CI->load->model('fatura_model');
    $CI->load->model('cartoes_model');

    $idUsuario = $idUsuario ?: getUserId();
    $cartoesAtivos = $CI->cartoes_model->getCartoesAtivosUsuario($idUsuario);

    foreach ($cartoesAtivos as $cartao) {
        $faturas = $CI->fatura_model->get(
            'faturas',
            '*',
            $cartao->id_cartao,
            'status = 1',
            null,
            0,
            0,
            0,
            [
                'ano_referencia' => 'desc',
                'mes_referencia' => 'desc',
            ]
        );

        if (!$faturas) continue;

        foreach ($faturas as $fatura) {
            monitoraPagamentosFaturasVinculadas($fatura->id_fatura, $idUsuario);
        }
    }

    return true;
}

function monitoraPagamentosFaturasVinculadas($idFatura, $idUsuario = null)
{
    if (!$idFatura) return false;

    $CI = get_instance();
    $CI->load->model('fatura_model');

    $fatura = $CI->fatura_model->getFatura($idFatura);

    if (!$fatura) return false;

    if ($fatura->fatura_aberta != 0) {
        return $CI->fatura_model->setFlagFaturaPaga($idFatura, [
            'data_pagamento' => null,
            'forma_pgto'     => null,
            'fatura_paga'    => null
        ]);
    }

    $vinculo = $CI->fatura_model->getVinculoFaturaComModuloLancamentos($idFatura, $idUsuario);

    if ($vinculo) {
        $dataToUpdate = [
            'data_pagamento' => $vinculo->baixado ? $vinculo->data_pagamento : null,
            'forma_pgto'     => $vinculo->baixado ? $vinculo->forma_pgto : null,
            'fatura_paga'    => $vinculo->baixado ? 1 : 2
        ];

        $CI->fatura_model->setFlagFaturaPaga($vinculo->id_fatura, $dataToUpdate);
    }
    return true;
}

function sincronizaPagamentoTerceiroPorLancamento($idLancamento, $idUsuario, $pago)
{
    if (!$idLancamento || !$idUsuario) {
        return false;
    }

    $CI = get_instance();
    $CI->load->model('fatura_model');

    $vinculos = $CI->fatura_model->getVinculosTerceiroPorLancamento($idLancamento, $idUsuario);

    if (!$vinculos) {
        return false;
    }

    $idsLancamentosFaturasAssoc = array_column($vinculos, 'id_lancamento_fatura_assoc');

    if (!$CI->fatura_model->setParcelasTerceiroPagoPorAssoc($idsLancamentosFaturasAssoc, $pago)) {
        return false;
    }

    foreach ($idsLancamentosFaturasAssoc as $idAssoc) {
        if (!sincronizaPagamentoRecebidoTerceiroPorParcela($idAssoc, $idUsuario, $pago)) {
            return false;
        }
    }

    return true;
}

function sincronizaPagamentoRecebidoTerceiroPorParcela($idAssoc, $idUsuario, $pago): bool
{
    if (!$idAssoc || !$idUsuario) {
        return false;
    }

    $CI = get_instance();
    $CI->load->model('fatura_model');

    $vinculo = $CI->fatura_model->garantirVinculoPagamentoTerceiroPorParcela($idAssoc, $idUsuario);

    if (!$vinculo) {
        return true;
    }

    if ($pago) {
        $periodoVencimento = getPeriodoVencimentoTerceiro($vinculo);
        $lancamento = $CI->fatura_model->getLancamentoRecebimentoTerceiroPeriodo(
            $idUsuario,
            $vinculo->nome_cliente,
            $periodoVencimento['mes'],
            $periodoVencimento['ano']
        );

        $idLancamento = $lancamento
            ? $lancamento->id_lancamento
            : $CI->fatura_model->criarLancamentoRecebimentoTerceiro($idUsuario, $vinculo->nome_cliente, $vinculo->vencimento);

        if (!$idLancamento) {
            return false;
        }

        if (!$CI->fatura_model->registrarPagamentoTerceiro($idLancamento, $idUsuario, $vinculo, 1)) {
            return false;
        }

        return $CI->fatura_model->sincronizarLancamentoRecebimentoTerceiro($idLancamento, $idUsuario);
    }

    $idsLancamentos = $CI->fatura_model->desativarPagamentoTerceiroPorVinculo(
        $vinculo->id_lancamento_terceiros_vinculo,
        $idUsuario
    );

    if ($idsLancamentos === false) {
        return false;
    }

    foreach ($idsLancamentos as $idLancamento) {
        if (!$CI->fatura_model->sincronizarLancamentoRecebimentoTerceiro($idLancamento, $idUsuario)) {
            return false;
        }
    }

    return true;
}

function getPeriodoVencimentoTerceiro($vinculo): array
{
    $vencimento = is_array($vinculo)
        ? ($vinculo['vencimento'] ?? null)
        : ($vinculo->vencimento ?? null);

    $mes = is_array($vinculo)
        ? ($vinculo['mes_vencimento'] ?? null)
        : ($vinculo->mes_vencimento ?? null);

    $ano = is_array($vinculo)
        ? ($vinculo['ano_vencimento'] ?? null)
        : ($vinculo->ano_vencimento ?? null);

    return [
        'mes' => $mes ?: ($vencimento ? date('m', strtotime($vencimento)) : null),
        'ano' => $ano ?: ($vencimento ? date('Y', strtotime($vencimento)) : null)
    ];
}

function sincronizaPagamentoRecebidoTerceiroPorCompra($idLancamentoFatura, $idUsuario, $pago): bool
{
    if (!$idLancamentoFatura || !$idUsuario) {
        return false;
    }

    $CI = get_instance();
    $CI->load->model('fatura_model');

    $vinculos = $CI->fatura_model->garantirVinculosPagamentoTerceiroPorCompra($idLancamentoFatura, $idUsuario);

    if (!$vinculos) {
        return true;
    }

    $idsLancamentos = [];
    $lancamentosPorPeriodo = [];

    foreach ($vinculos as $vinculo) {
        if ($pago) {
            $periodoVencimento = getPeriodoVencimentoTerceiro($vinculo);
            $chavePeriodo = $vinculo->nome_cliente . '|' . $periodoVencimento['mes'] . '|' . $periodoVencimento['ano'];

            if (!isset($lancamentosPorPeriodo[$chavePeriodo])) {
                $lancamento = $CI->fatura_model->getLancamentoRecebimentoTerceiroPeriodo(
                    $idUsuario,
                    $vinculo->nome_cliente,
                    $periodoVencimento['mes'],
                    $periodoVencimento['ano']
                );

                $lancamentosPorPeriodo[$chavePeriodo] = $lancamento
                    ? $lancamento->id_lancamento
                    : $CI->fatura_model->criarLancamentoRecebimentoTerceiro($idUsuario, $vinculo->nome_cliente, $vinculo->vencimento);
            }

            $idLancamento = $lancamentosPorPeriodo[$chavePeriodo];

            if (!$idLancamento) {
                return false;
            }

            if (!$CI->fatura_model->registrarPagamentoTerceiro($idLancamento, $idUsuario, $vinculo, 2)) {
                return false;
            }

            $idsLancamentos[] = $idLancamento;
            continue;
        }

        $idsDesativados = $CI->fatura_model->desativarPagamentoTerceiroPorVinculo(
            $vinculo->id_lancamento_terceiros_vinculo,
            $idUsuario
        );

        if ($idsDesativados === false) {
            return false;
        }

        $idsLancamentos = array_merge($idsLancamentos, $idsDesativados);
    }

    $idsLancamentos = array_values(array_unique(array_filter($idsLancamentos)));

    foreach ($idsLancamentos as $idLancamento) {
        if (!$CI->fatura_model->sincronizarLancamentoRecebimentoTerceiro($idLancamento, $idUsuario)) {
            return false;
        }
    }

    return true;
}

function sincronizaPagamentosRecebidosTerceiroPorCompra($idLancamentoFatura, $idUsuario = null): bool
{
    if (!$idLancamentoFatura) {
        return true;
    }

    $CI = get_instance();
    $CI->load->model('fatura_model');

    $idUsuario = $idUsuario ?: getUserId();
    $vinculos  = $CI->fatura_model->garantirVinculosPagamentoTerceiroPorCompra($idLancamentoFatura, $idUsuario);

    if (!$vinculos) {
        return true;
    }

    $idsLancamentos = [];
    $lancamentosPorPeriodo = [];

    foreach ($vinculos as $vinculo) {
        if ($vinculo->parcela_terceiro_pago == 1) {
            $periodoVencimento = getPeriodoVencimentoTerceiro($vinculo);
            $chavePeriodo = $vinculo->nome_cliente . '|' . $periodoVencimento['mes'] . '|' . $periodoVencimento['ano'];

            if (!isset($lancamentosPorPeriodo[$chavePeriodo])) {
                $lancamento = $CI->fatura_model->getLancamentoRecebimentoTerceiroPeriodo(
                    $idUsuario,
                    $vinculo->nome_cliente,
                    $periodoVencimento['mes'],
                    $periodoVencimento['ano']
                );

                $lancamentosPorPeriodo[$chavePeriodo] = $lancamento
                    ? $lancamento->id_lancamento
                    : $CI->fatura_model->criarLancamentoRecebimentoTerceiro($idUsuario, $vinculo->nome_cliente, $vinculo->vencimento);
            }

            $idLancamento = $lancamentosPorPeriodo[$chavePeriodo];

            if (!$idLancamento) {
                return false;
            }

            if (!$CI->fatura_model->registrarPagamentoTerceiro($idLancamento, $idUsuario, $vinculo, 2)) {
                return false;
            }

            $idsLancamentos[] = $idLancamento;
            continue;
        }

        $idsDesativados = $CI->fatura_model->desativarPagamentoTerceiroPorVinculo(
            $vinculo->id_lancamento_terceiros_vinculo,
            $idUsuario
        );

        if ($idsDesativados === false) {
            return false;
        }

        $idsLancamentos = array_merge($idsLancamentos, $idsDesativados);
    }

    $idsLancamentos = array_values(array_unique(array_filter($idsLancamentos)));

    foreach ($idsLancamentos as $idLancamento) {
        if (!$CI->fatura_model->sincronizarLancamentoRecebimentoTerceiro($idLancamento, $idUsuario)) {
            return false;
        }
    }

    return true;
}

function atualizaValorVinculoFaturas($idFatura = null, $idUsuario = null): bool
{
    $CI = get_instance();
    $CI->load->model('fatura_model');
    $CI->load->model('cartoes_model');

    $idUsuario = $idUsuario ?: getUserId();

    vinculoAutomaticoFaturas($idUsuario);
    monitoraPagamentosFaturasUsuario($idUsuario);

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
    $cartoesAtivos = $CI->cartoes_model->getCartoesAtivosUsuario($idUsuario);

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
            'id_usuario'         => $idUsuario,
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

function vinculoAutomaticoComprasTerceiros($idUsuario = null): bool
{
    $CI = get_instance();
    $CI->load->model('fatura_model');

    $idUsuario = $idUsuario ?: getUserId();
    $periodosVinculados = $CI->fatura_model->getPeriodosTerceirosVinculadosAtivos($idUsuario);

    if (!$periodosVinculados) {
        return true;
    }

    foreach ($periodosVinculados as $periodo) {
        $CI->fatura_model->sincronizarVinculoTerceiroPeriodo(
            $periodo['id_lancamento'],
            $idUsuario,
            $periodo['nome_cliente'],
            $periodo['mes_referencia'],
            $periodo['ano_referencia']
        );
    }

    return true;
}

function sincronizaPagamentosRecebidosTerceirosUsuario($idUsuario = null): bool
{
    $CI = get_instance();
    $CI->load->model('fatura_model');
    $CI->load->database();

    if (!$CI->db->table_exists('lancamentos_terceiros_pagamentos')) {
        return true;
    }

    $idUsuario = $idUsuario ?: getUserId();
    $idsLancamentos = [];

    $idsInvalidos = $CI->fatura_model->desativarPagamentosRecebidosInvalidos($idUsuario);

    if ($idsInvalidos === false) {
        return false;
    }

    $idsLancamentos = array_merge($idsLancamentos, $idsInvalidos);

    $periodosVinculados = $CI->fatura_model->getPeriodosTerceirosVinculadosAtivos($idUsuario);

    foreach ($periodosVinculados as $periodo) {
        if (!$CI->fatura_model->sincronizarVinculosTerceiroPagosPeriodo(
            $periodo['id_lancamento'],
            $idUsuario,
            $periodo['nome_cliente'],
            $periodo['mes_referencia'],
            $periodo['ano_referencia']
        )) {
            return false;
        }
    }

    $vinculosPagos = $CI->fatura_model->getVinculosTerceiroPagosAtivos($idUsuario);
    $vinculosPorPeriodo = [];

    foreach ($vinculosPagos as $vinculo) {
        $periodoVencimento = getPeriodoVencimentoTerceiro($vinculo);
        $chavePeriodo = $vinculo['nome_cliente'] . '|' . $periodoVencimento['mes'] . '|' . $periodoVencimento['ano'];
        $vinculosPorPeriodo[$chavePeriodo][] = $vinculo;
    }

    foreach ($vinculosPorPeriodo as $vinculosPeriodo) {
        $primeiroVinculo = $vinculosPeriodo[0];
        $idsVinculos     = array_column($vinculosPeriodo, 'id_lancamento_terceiros_vinculo');

        $lancamento = $CI->fatura_model->getLancamentoRecebimentoTerceiroPorVinculos($idUsuario, $idsVinculos);
        $idLancamento = $lancamento
            ? $lancamento->id_lancamento
            : $CI->fatura_model->criarLancamentoRecebimentoTerceiro($idUsuario, $primeiroVinculo['nome_cliente'], $primeiroVinculo['vencimento']);

        if (!$idLancamento) {
            return false;
        }

        foreach ($vinculosPeriodo as $vinculo) {
            if (!$CI->fatura_model->registrarPagamentoTerceiro($idLancamento, $idUsuario, (object) $vinculo, 1)) {
                return false;
            }

            $idsLancamentos[] = $idLancamento;
        }
    }

    $lancamentos = $CI->fatura_model->getLancamentosRecebimentosTerceirosAtivos($idUsuario);

    foreach ($lancamentos as $lancamento) {
        $idsLancamentos[] = $lancamento['id_lancamento'];
    }

    $idsLancamentos = array_values(array_unique(array_filter($idsLancamentos)));

    foreach ($idsLancamentos as $idLancamento) {
        if (!$CI->fatura_model->sincronizarLancamentoRecebimentoTerceiro($idLancamento, $idUsuario)) {
            return false;
        }
    }

    return true;
}

function sincronizaVinculosTerceiroPorCompra($idLancamentoFatura, $idUsuario = null): bool
{
    if (!$idLancamentoFatura) {
        return true;
    }

    $CI = get_instance();
    $CI->load->model('fatura_model');

    $idUsuario = $idUsuario ?: getUserId();

    if (!$CI->fatura_model->garantirLancamentosTotalDevidoTerceiroPorCompra(
        $idLancamentoFatura,
        $idUsuario
    )) {
        return false;
    }

    if (!$CI->fatura_model->sincronizarVinculosTerceiroPorCompra(
        $idLancamentoFatura,
        $idUsuario
    )) {
        return false;
    }

    return sincronizaPagamentosRecebidosTerceiroPorCompra($idLancamentoFatura, $idUsuario);
}

function vinculaFatura($idFatura, $idUsuario = null)
{
    if (!$idFatura) {
        return false;
    }
    $CI = get_instance();
    $CI->load->model('fatura_model');

    $idUsuario = $idUsuario ?: getUserId();
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
            'id_usuario'         => $idUsuario,
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
