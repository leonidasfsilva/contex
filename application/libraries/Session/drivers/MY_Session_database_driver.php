<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Session_database_driver extends CI_Session_database_driver
{
    private $passiveRequest;

    public function __construct(&$params)
    {
        parent::__construct($params);
        $this->passiveRequest = $this->isPassiveRequest();
    }

    public function read($sessionId)
    {
        $sessionData = parent::read($sessionId);

        if (!$this->rowIsExpired($sessionId)) {
            return $sessionData;
        }

        $this->discardExpiredRow($sessionId);

        return '';
    }

    public function write($sessionId, $sessionData)
    {
        if (!$this->passiveRequest) {
            return parent::write($sessionId, $sessionData);
        }

        if ($this->_row_exists === false) {
            return $this->_success;
        }

        if ($this->_fingerprint === md5($sessionData)) {
            return $this->_success;
        }

        $this->_db->reset_query();
        $this->_db->where('id', $sessionId);

        if ($this->_config['match_ip']) {
            $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
        }

        $storedData = $this->_platform === 'postgre'
            ? base64_encode($sessionData)
            : $sessionData;

        if (!$this->_db->update($this->_config['save_path'], array('data' => $storedData))) {
            return $this->_failure;
        }

        $this->_fingerprint = md5($sessionData);

        return $this->_success;
    }

    public function updateTimestamp($sessionId, $data)
    {
        if ($this->passiveRequest) {
            return true;
        }

        return parent::updateTimestamp($sessionId, $data);
    }

    private function isPassiveRequest()
    {
        return strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '') === 'GET'
            && uri_string() === 'api/v1/auth/session';
    }

    private function rowIsExpired($sessionId)
    {
        $expiration = (int)$this->_config['expiration'];

        if ($expiration <= 0 || $this->_row_exists === false) {
            return false;
        }

        $this->_db->reset_query();
        $this->_db
            ->select('timestamp')
            ->from($this->_config['save_path'])
            ->where('id', $sessionId);

        if ($this->_config['match_ip']) {
            $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
        }

        $timestamp = $this->_db->get()->row('timestamp');

        return $timestamp !== null && (int)$timestamp < time() - $expiration;
    }

    private function discardExpiredRow($sessionId)
    {
        $this->_db->reset_query();
        $this->_db->where('id', $sessionId);

        if ($this->_config['match_ip']) {
            $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
        }

        $this->_db->delete($this->_config['save_path']);
        $this->_row_exists = false;
        $this->_fingerprint = md5('');
    }
}
