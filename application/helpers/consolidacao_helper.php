<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function reconciliacaoPendenteUsuario($idUsuario = null, $origem = 'login')
{
    if (!$idUsuario) {
        return false;
    }

    if (in_array($origem, ['login', 'manual'])) {
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

function reconciliarFinanceiro($idUsuario = null, $origem = 'manual')
{
    if ($idUsuario) {
        $sucesso = reconciliarFinanceiroUsuario($idUsuario, $origem);

        return [
            'total'    => 1,
            'sucesso'  => $sucesso ? 1 : 0,
            'erro'     => $sucesso ? 0 : 1,
            'usuarios' => [
                [
                    'id_usuario' => (int) $idUsuario,
                    'status'     => $sucesso ? 'sucesso' : 'erro',
                ]
            ],
        ];
    }

    $CI = get_instance();
    $CI->load->model('usuarios_model');

    $usuarios = $CI->usuarios_model->getUsuariosAtivos();
    $resultado = [
        'total'    => count($usuarios),
        'sucesso'  => 0,
        'erro'     => 0,
        'usuarios' => [],
    ];

    foreach ($usuarios as $usuario) {
        $sucesso = reconciliarFinanceiroUsuario($usuario->id_usuarios, $origem);

        if ($sucesso) {
            $resultado['sucesso']++;
        } else {
            $resultado['erro']++;
        }

        $resultado['usuarios'][] = [
            'id_usuario' => (int) $usuario->id_usuarios,
            'status'     => $sucesso ? 'sucesso' : 'erro',
        ];
    }

    return $resultado;
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

    if (!in_array($origem, ['login', 'manual']) && $ultimaExecucao && $ultimaExecucao->status == 'sucesso') {
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
        integracaoDespesasUsuario($idUsuario);
        atualizaValorVinculoFaturas(null, $idUsuario);

        if (!sanitizaIntegracaoTerceirosUsuario($idUsuario)) {
            throw new RuntimeException('Erro ao sanitizar vínculos financeiros de terceiros.');
        }

        vinculoAutomaticoComprasTerceiros($idUsuario);
        sincronizaPagamentosRecebidosTerceirosUsuario($idUsuario);

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
