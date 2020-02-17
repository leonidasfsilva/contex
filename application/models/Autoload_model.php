<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Autoload_model extends CI_Model {

    public function __construct(){

        parent::__construct();

        // Scan directory where this (Autoload_models_model.php) file is located
        $model_files = scandir(__DIR__);

        foreach($model_files as $file){
            // Make sure we are not reloading autoload_models_model
            // Make sure we have a PHP file
            if(
                (explode('.', $file)[0]) !== (__CLASS__) &&
                (explode('.', $file)[1]) === 'php')
            {
//                $this->load->model(strtolower($file));
                $this->load->model(explode('.', $file)[0]);
            }
        }
    }
}
