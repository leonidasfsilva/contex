<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DatabaseTimezone
{
    public function setTimezone()
    {
        $CI =& get_instance();

        if (!isset($CI->db) || !$CI->db->conn_id) {
            log_message('error', 'Não foi possível configurar o fuso da sessão do banco: conexão indisponível.');
            return;
        }

        if (!$CI->db->query("SET time_zone = '-03:00'")) {
            log_message('error', 'Não foi possível configurar o fuso da sessão do banco para -03:00.');
        }
    }
}
