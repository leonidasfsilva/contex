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

function atualizaValorVinculoFatura($idFatura)
{
    if (!$idFatura) {
        return false;
    }
    $CI = get_instance();
    $CI->load->model('fatura_model', 'faturaModel');
    $valorTotalFatura = $CI->faturaModel->getValorTotalFatura($idFatura);

    $data = [
        'valor' => '-' . $valorTotalFatura
    ];
    return $CI->faturaModel->edit('lancamentos', $data, 'id_fatura', $idFatura);
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
    }
    return false;
}
