<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function differenceInHours($startdate, $enddate)
{
    $starttimestamp = strtotime($startdate);
    $endtimestamp = strtotime($enddate);
    $return =  abs($endtimestamp - $starttimestamp) / 3600;
//    print_array($starttimestamp);
//    print_array($endtimestamp);
    return $return;
}

function versionApp() {
    return '10.02';
}

function print_array($a)
{
    echo '<pre>';
    print_r($a);
    echo '</pre>';
}

function print_array_exit($a)
{
    echo '<pre>';
    print_r($a);
    echo '</pre>';
    exit;
}

function print_var($a)
{
    echo '<pre>';
    var_dump($a);
    echo '</pre>';
}

function print_var_exit($a)
{
    echo '<pre>';
    var_dump($a);
    echo '</pre>';
    exit;
}


function clean_header($array)
{
    $CI = get_instance();
    $CI->load->helper('inflector');
    foreach ($array as $a) {
        $arr[] = humanize($a);
    }
    return $arr;
}

function validate_money($valor)
{

    if (preg_match("/^([0-9]*)\.(\d{2})$/", $valor)) {
        return true;
    }
    return false;

}

function padronizarString($str)
{
    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);
//    $str = preg_replace('/[,(),;:|!"#$%&\/=?~^><ªº-]/', '_', $str);
    $str = preg_replace('/[^a-z0-9\/\-_. ]/i', '', $str);
//    $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
    return strtoupper($str);
}

function gravaLog($id_usuario = null, $nome = null, $email = null, $acao = null, $ip = null)
{
    $CI = get_instance();
    $CI->load->model('mxcode_model');
    $data = array(
        'id_usuario' => $id_usuario,
        'nome' => $nome,
        'email' => $email,
        'acao' => $acao,
        'ip' => $ip,
    );

    $CI->mxcode_model->gravaLog($data);

}

function id_usuario()
{
    $CI = get_instance();
    return $CI->session->userdata('id');
}

function nome_usuario()
{
    $CI = get_instance();
    return $CI->session->userdata('nome');
}

function email_usuario()
{
    $CI = get_instance();
    return $CI->session->userdata('email');
}
