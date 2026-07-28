<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SpaCsrf
{
    const SESSION_KEY = 'spa_csrf_token';

    /** @var CI_Session */
    private $session;

    public function __construct()
    {
        $CI = get_instance();
        $this->session = $CI->session;
    }

    public function getToken()
    {
        $token = $this->session->userdata(self::SESSION_KEY);

        if (!is_string($token) || strlen($token) !== 64) {
            return $this->rotateToken();
        }

        return $token;
    }

    public function rotateToken()
    {
        $token = bin2hex(random_bytes(32));
        $this->session->set_userdata(self::SESSION_KEY, $token);

        return $token;
    }

    public function isValid($token)
    {
        $sessionToken = $this->session->userdata(self::SESSION_KEY);

        return is_string($sessionToken)
            && is_string($token)
            && $token !== ''
            && hash_equals($sessionToken, $token);
    }
}
