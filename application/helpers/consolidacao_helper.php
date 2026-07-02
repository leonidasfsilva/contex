<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function reconciliacaoPendenteUsuario($idUsuario = null, $origem = 'login')
{
    if (!$idUsuario) {
        return false;
    }

    if ($origem == 'login') {
        return true;
    }

    $CI = get_instance();
    $CI->load->database();

    if (!$CI->db->table_exists('consolidacoes_financeiras')) {
        return false;
    }

    $rotina = 'financeiro';
    $intervaloHoras = (int) env('FINANCIAL_SYNC_HOURS', 2);

    if ($intervaloHoras <= 0) {
        $intervaloHoras = 2;
    }

    $ultimaExecucao = $CI->db
        ->where('id_usuario', $idUsuario)
        ->where('rotina', $rotina)
        ->order_by('iniciado_em', 'DESC')
        ->get('consolidacoes_financeiras')
        ->row();

    if (!$ultimaExecucao) {
        return true;
    }

    if ($ultimaExecucao->status == 'executando') {
        $timeoutMinutos = (int) env('FINANCIAL_SYNC_TIMEOUT_MINUTES', 15);

        if ($timeoutMinutos <= 0) {
            $timeoutMinutos = 15;
        }

        return strtotime($ultimaExecucao->iniciado_em) <= strtotime("-{$timeoutMinutos} minutes");
    }

    if ($ultimaExecucao->status == 'sucesso') {
        return strtotime($ultimaExecucao->iniciado_em) <= strtotime("-{$intervaloHoras} hours");
    }

    return true;
}

function reconciliarFinanceiroUsuario($idUsuario = null, $origem = 'login')
{
    if (!$idUsuario) {
        return false;
    }

    $CI = get_instance();
    $CI->load->database();
    $CI->load->model('financeiro_model');

    if (!$CI->db->table_exists('consolidacoes_financeiras')) {
        return false;
    }

    $rotina = 'financeiro';
    $intervaloHoras = (int) env('FINANCIAL_SYNC_HOURS', 2);
    $timeoutMinutos = (int) env('FINANCIAL_SYNC_TIMEOUT_MINUTES', 15);

    if ($intervaloHoras <= 0) {
        $intervaloHoras = 2;
    }

    if ($timeoutMinutos <= 0) {
        $timeoutMinutos = 15;
    }

    $ultimaExecucao = $CI->db
        ->where('id_usuario', $idUsuario)
        ->where('rotina', $rotina)
        ->order_by('iniciado_em', 'DESC')
        ->get('consolidacoes_financeiras')
        ->row();

    if ($origem != 'login' && $ultimaExecucao && $ultimaExecucao->status == 'sucesso') {
        $limiteExecucao = strtotime("-{$intervaloHoras} hours");

        if (strtotime($ultimaExecucao->iniciado_em) > $limiteExecucao) {
            return true;
        }
    }

    if ($ultimaExecucao && $ultimaExecucao->status == 'executando') {
        $limiteTimeout = strtotime("-{$timeoutMinutos} minutes");

        if (strtotime($ultimaExecucao->iniciado_em) > $limiteTimeout) {
            return false;
        }

        $CI->db
            ->where('id', $ultimaExecucao->id)
            ->update('consolidacoes_financeiras', [
                'status'        => 'erro',
                'finalizado_em' => date('Y-m-d H:i:s'),
                'msg_erro'      => 'Execução interrompida por timeout.',
            ]);
    }

    $dataExecucao = [
        'id_usuario'     => $idUsuario,
        'rotina'         => $rotina,
        'origem'         => $origem,
        'status'        => 'executando',
        'iniciado_em'   => date('Y-m-d H:i:s'),
        'finalizado_em' => null,
        'msg_erro'      => null,
    ];

    $CI->db->insert('consolidacoes_financeiras', $dataExecucao);
    $idConsolidacao = $CI->db->insert_id();

    try {
        integracaoDespesasUsuario();
        atualizaValorVinculoFaturas();
        vinculoAutomaticoComprasTerceiros();

        $CI->db
            ->where('id', $idConsolidacao)
            ->update('consolidacoes_financeiras', [
                'status'        => 'sucesso',
                'finalizado_em' => date('Y-m-d H:i:s'),
                'msg_erro'      => null,
            ]);

        return true;
    } catch (Throwable $e) {
        $CI->db
            ->where('id', $idConsolidacao)
            ->update('consolidacoes_financeiras', [
                'status'        => 'erro',
                'finalizado_em' => date('Y-m-d H:i:s'),
                'msg_erro'      => substr($e->getMessage(), 0, 1000),
            ]);

        return false;
    }
}
