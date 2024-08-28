<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

function differenceInHours($startdate, $enddate)
{
	$starttimestamp = strtotime($startdate);
	$endtimestamp   = strtotime($enddate);
	$return         = abs($endtimestamp - $starttimestamp) / 3600;
	//    print_array($starttimestamp);
	//    print_array($endtimestamp);
	return $return;
}

function versionApp()
{
	return VERSION_APP;
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
	exit();
}

function varDump($a)
{
	// xdebug without limit
	// ini_set('xdebug.var_display_max_depth', -1);
	// ini_set('xdebug.var_display_max_children', -1);
	// ini_set('xdebug.var_display_max_data', -1);
	
	//xdebug with safe deep
	ini_set('xdebug.var_display_max_depth', 10);
	ini_set('xdebug.var_display_max_children', 256);
	ini_set('xdebug.var_display_max_data', 1024);
	
	echo '<pre>';
	var_dump($a);
	echo '</pre>';
}

function varDumpExit($a)
{
	// xdebug without limit
	// ini_set('xdebug.var_display_max_depth', -1);
	// ini_set('xdebug.var_display_max_children', -1);
	// ini_set('xdebug.var_display_max_data', -1);
	
	//xdebug with safe deep
	ini_set('xdebug.var_display_max_depth', 10);
	ini_set('xdebug.var_display_max_children', 256);
	ini_set('xdebug.var_display_max_data', 1024);
	
	echo '<pre>';
	var_dump($a);
	echo '</pre>';
	exit();
}

function getSqlStatement()
{
	// xdebug without limit
	// ini_set('xdebug.var_display_max_depth', -1);
	// ini_set('xdebug.var_display_max_children', -1);
	// ini_set('xdebug.var_display_max_data', -1);
	
	//xdebug with safe deep
	ini_set('xdebug.var_display_max_depth', 10);
	ini_set('xdebug.var_display_max_children', 256);
	ini_set('xdebug.var_display_max_data', 1024);
	
	$CI    = get_instance();
	$query = $CI->db->last_query();
	
	echo '<pre>';
	var_dump($query);
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
	// $str = preg_replace('/[,(),;:|!"#$%&\/=?~^><ªº-]/', '_', $str);
	$str = preg_replace('/[^a-z0-9\/\-_., ]/i', '', $str);
	$str = preg_replace('/[,]/', '.', $str);
	// $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
	return strtoupper($str);
}

function capsLock($str)
{
	return mb_convert_case($str, MB_CASE_UPPER, 'UTF-8');
}

// Refactor this method to receive an array instead of multiple parameters
function gravaLog($id_usuario = null, $nome = null, $email = null, $acao = null, $ip = null)
{
	$CI = get_instance();
	$CI->load->model('mxcode_model');
	
	$data = array(
		'id_usuario' => $id_usuario,
		'nome'       => $nome,
		'email'      => $email,
		'descricao'  => $acao,
		'ip'         => $ip,
	);
	
	$CI->mxcode_model->gravaLog($data);
}

function getUserId()
{
	$CI = get_instance();
	return $CI->session->userdata('id');
}

function getUserPermission()
{
	$CI = get_instance();
	return $CI->session->userdata('permissao');
}

function getUserName()
{
	$CI = get_instance();
	return $CI->session->userdata('nome');
}

function getUserEmail()
{
	$CI = get_instance();
	return $CI->session->userdata('email');
}

function returnURL($get = null)
{
	$CI = get_instance();
	if ((!session_id()) || (!$CI->session->userdata('logado'))) {
		$notRedirectedUlrs = [
			'',
			'mxcode',
			'phpinfo',
			'mxcode/login',
			'cadastro',
			'mxcode/verificarLogin'
		];
		
		if (!in_array(uri_string(), $notRedirectedUlrs, true) && !$get) {
			$currentURL = current_url(); //for simple URL
			$params     = $_SERVER['QUERY_STRING']; //for parameters
			$fullURL    = $currentURL . '?' . $params; //full URL with parameter
			$CI->session->set_userdata('last_url', $fullURL);
		}
	}
}

function encriptar($value)
{
	return base64_encode(base64_encode($value));
}

function decriptar($value)
{
	return base64_decode(base64_decode($value));
}

function getUserTickets()
{
	$CI = get_instance();
	$CI->load->model('chamados_model');
	
	return $CI->chamados_model->usuarioTemNotificacoes(getUserId());
}

function getAdminTickets()
{
	$CI = get_instance();
	$CI->load->model('chamados_model');
	
	return $CI->chamados_model->adminTemNotificacoes(getUserId());
}

function setNotification($idUsuario = null, $titulo = null, $descricao = null, $icone = null, $link = null, $prioridade = null)
{
	$CI = get_instance();
	$CI->load->model('notificacoes_model');
	
	if ($idUsuario == null) {
		$idUsuario = getUserId();
	}
	
	$data = array(
		'id_usuario' => $idUsuario != null ? $idUsuario : getUserId(),
		'titulo'     => $titulo != null ? $titulo : '',
		'descricao'  => $descricao != null ? $descricao : '',
		'icone'      => $icone != null ? $icone : '',
		'link'       => $link != null ? $link : '',
		'prioridade' => $prioridade != null ? $prioridade : '',
	);
	
	return $CI->notificacoes_model->setNotification($data);
}

function getCurrentFullUrl()
{
	$currentURL = current_url();
	if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != null) {
		$params  = $_SERVER['QUERY_STRING'];
		$fullUrl = $currentURL . '?' . $params;
	} else {
		$fullUrl = $currentURL;
	}
	return $fullUrl;
}

function getExtendedMonthName($monthNumber, $formatter = null)
{
	$dateFormatterExtended = new \IntlDateFormatter(
		'pt_BR',
		\IntlDateFormatter::FULL,
		\IntlDateFormatter::NONE,
		'America/Sao_Paulo',
		\IntlDateFormatter::GREGORIAN,
		"MMMM"
	);
	
	if ($formatter) {
		$dateFormatterExtended = new \IntlDateFormatter(
			'pt_BR',
			\IntlDateFormatter::FULL,
			\IntlDateFormatter::NONE,
			'America/Sao_Paulo',
			\IntlDateFormatter::GREGORIAN,
			$formatter
		);
	}
	
	$dateObj = DateTime::createFromFormat('!m', ($monthNumber));
	return str_replace('.', '', mb_strtoupper($dateFormatterExtended->format($dateObj)));
}

function translateMonth($referenceMonth, $abbreviate = false, $returnMonthNumber = false)
{
	$monthFormatString = 'MMMM';
	
	if ($abbreviate) {
		$monthFormatString = 'MMM';
	}
	
	if ($returnMonthNumber) {
		$monthFormatString = 'MM';
	}
	
	return getExtendedMonthName($referenceMonth, $monthFormatString);
}

function getQuarterOfCurrentYear()
{
	$dateFormatterExtended = new \IntlDateFormatter(
		'pt_BR',
		\IntlDateFormatter::FULL,
		\IntlDateFormatter::NONE,
		'America/Sao_Paulo',
		\IntlDateFormatter::GREGORIAN,
		'Q'
	);
	
	$dateObj = DateTime::createFromFormat('Y-m-d', date('Y-m-d', time()));
	return str_replace('.', '', ($dateFormatterExtended->format($dateObj)));
}

function translateWeekDay($date, $formatter = 'EEEE')
{
	$dateFormatterExtended = new \IntlDateFormatter(
		'pt_BR',
		\IntlDateFormatter::FULL,
		\IntlDateFormatter::NONE,
		'America/Sao_Paulo',
		\IntlDateFormatter::GREGORIAN,
		$formatter
	);
	
	$dateObj = DateTime::createFromFormat('Y-m-d', ($date));
	return str_replace('.', '', ($dateFormatterExtended->format($dateObj)));
}

function getExtendedWeekDayName($referenceDate, $abbreviate = false, $uppercase = false)
{
	$weekDayFormatString = 'EEEE';
	
	if ($abbreviate) {
		$weekDayFormatString = 'EE';
	}
	
	$weekday = translateWeekDay($referenceDate, $weekDayFormatString);
	
	if ($uppercase) {
		$weekday = mb_strtoupper($weekday);
	}
	
	return $weekday;
}

function buildStartEndDate($referenceMonth = null, $referenceYear = null)
{
	$return     = [];
	$todayDate  = date('Y-m-d');
	$todayArray = explode('-', $todayDate);
	
	$daysInMonth              = cal_days_in_month(CAL_GREGORIAN, $todayArray[1], $todayArray[0]);
	$return['startDate']      = $todayArray[0] . '-' . $todayArray[1] . '-01';
	$return['endDate']        = $todayArray[0] . '-' . $todayArray[1] . '-' . $daysInMonth;
	$return['referenceMonth'] = $todayArray[1];
	
	if ($referenceMonth) {
		$daysInMonth              = cal_days_in_month(CAL_GREGORIAN, $referenceMonth, $todayArray[0]);
		$return['startDate']      = $todayArray[0] . '-' . $referenceMonth . '-01';
		$return['endDate']        = $todayArray[0] . '-' . $referenceMonth . '-' . $daysInMonth;
		$return['referenceMonth'] = $referenceMonth;
	}
	
	$return['referenceYear'] = $todayArray[0];
	
	if ($referenceYear) {
		$return['startDate']     = $referenceYear . '-' . $referenceMonth . '-01';
		$return['endDate']       = $referenceYear . '-' . $referenceMonth . '-' . $daysInMonth;
		$return['referenceYear'] = $referenceYear;
	}
	return $return;
}

function dd()
{
	foreach (func_get_args() as $arg) {
		var_dump($arg);
	}
	exit();
}

function checkMaintenanceMode()
{
	$CI = get_instance();
	$CI->load->model('configs_model');
	
	if ($CI->configs_model->getMaintenanceMode()) {
		return true;
	}
	return false;
}

function checkForcedLogout()
{
	$CI = get_instance();
	$CI->load->model('configs_model');
	
	if ($CI->configs_model->getForcedLogout()) {
		if ((session_id()) && ($CI->session->userdata('logado')) && ($CI->session->userdata('permissao') != 1)) {
			gravaLog(getUserId(), getUserName(), getUserEmail(), 'Logout forçado: sistema em manutenção', getenv("REMOTE_ADDR"));
			$CI->session->sess_destroy();
		}
		return true;
	}
	return false;
}
