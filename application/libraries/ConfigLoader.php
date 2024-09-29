<?php
defined('BASEPATH') or exit('No direct script access allowed.');

class ConfigLoader
{
	protected $CI;
	
	public function __construct()
	{
		$this->CI =& get_instance(); //read manual: create libraries
		
		// $data = []; // set here all your vars to views
		
		$config['topbarClass']     = ENVIRONMENT == 'development' ? 'navbar-danger' : 'navbar-midnightblue';
		$config['developmentText'] = ENVIRONMENT == 'development' ? 'DEVELOPMENT' : null;
		$config['maintenanceMode'] = false;
		$config['forcedLogout']    = false;
		
		if (checkMaintenanceMode()) {
			$config['developmentText'] = 'Modo de manutenção ativado';
			$config['maintenanceMode'] = true;
		}
		
		if (checkForcedLogout()) {
			$config['forcedLogout'] = true;
		}
		
		$this->CI->load->vars($config);
	}
}