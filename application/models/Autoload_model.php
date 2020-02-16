<?php


class Autoload_model extends CI_Model {

    public function __construct(){

        parent::__construct();

        // Scan directory where this (Autoload_models_model.php) file is located
        $model_files = scandir(__DIR__);

        foreach($model_files as $file){
            // Make sure we are not reloading autoload_models_model
            // Make sure we have a PHP file
            if(
                strtolower(explode('.', $file)[0]) !== strtolower(__CLASS__) &&
                strtolower(explode('.', $file)[1]) === 'php')
            {
                $this->load->model(explode('.', $file)[0]);
            }
        }
    }
}
