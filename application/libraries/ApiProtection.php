<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ApiProtection
{
    protected $CI;
    protected $redis;

    public function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->database();
    }

    public function authorize($apiClient, $ip, $endpoint, $method, $authenticated, $scopeAllowed = true)
    {
        $endpoint = strtolower(trim($endpoint, '/'));
        $method   = strtoupper($method);

        if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $this->getMaxBodyBytes()) {
            return ['allowed' => false, 'code' => 413, 'reason' => 'payload_too_large'];
        }

        $rule = $this->getRule($endpoint, $method);
        if (!$rule) {
            return ['allowed' => false, 'code' => 503, 'reason' => 'limit_not_configured'];
        }

        $rule  = $this->normalizeRule($rule);
        $keys  = $this->getKeys($apiClient, $ip, $authenticated, $endpoint);
        $redis = $this->getRedis();

        if (!$redis) {
            $this->logSecurityEvent($apiClient, $ip, 'Redis indisponivel; fail-open', 'api_rate_limit');
            $this->CI->output->set_header('X-RateLimit-Mode: fail-open');
            return ['allowed' => true, 'code' => 200, 'remaining' => null];
        }

        $block = $this->getActiveBlock($redis, $keys);
        if ($block) {
            $this->setResponseHeaders($rule, 0, $block['ttl']);
            return [
                'allowed'     => false,
                'code'        => 429,
                'reason'      => 'temporarily_blocked',
                'retry_after' => $block['ttl'],
            ];
        }

        if (!$authenticated || !$scopeAllowed) {
            $failure = $this->registerFailure($redis, $keys, $rule);
            if ($failure['blocked']) {
                $this->logSecurityEvent($apiClient, $ip, 'Bloqueio temporario apos tentativas recusadas', 'api_rate_limit');
                $this->setResponseHeaders($rule, 0, $failure['ttl']);
                return [
                    'allowed'     => false,
                    'code'        => 429,
                    'reason'      => 'temporarily_blocked',
                    'retry_after' => $failure['ttl'],
                ];
            }

            return ['allowed' => false, 'code' => 401, 'reason' => 'unauthorized'];
        }

        $remaining = PHP_INT_MAX;
        foreach ($keys as $key) {
            $result    = $this->incrementCounter($redis, $key, $rule);
            $remaining = min($remaining, $result['remaining']);

            if (!$result['allowed']) {
                $this->logSecurityEvent($apiClient, $ip, 'Limite de requisicoes excedido', 'api_rate_limit');
                $this->setResponseHeaders($rule, 0, $result['ttl']);
                return [
                    'allowed'     => false,
                    'code'        => 429,
                    'reason'      => 'rate_limit_exceeded',
                    'retry_after' => $result['ttl'],
                ];
            }
        }

        $this->setResponseHeaders($rule, $remaining === PHP_INT_MAX ? 0 : $remaining, null);
        return [
            'allowed'   => true,
            'code'      => 200,
            'remaining' => $remaining === PHP_INT_MAX ? 0 : $remaining,
        ];
    }

    protected function getRule($endpoint, $method)
    {
        $candidates = [
            [$endpoint, $method],
            [$endpoint, '*'],
            ['*', $method],
            ['*', '*'],
        ];

        foreach ($candidates as $candidate) {
            $rule = $this->CI->db
                ->where('endpoint', $candidate[0])
                ->where('metodo', $candidate[1])
                ->where('ativo', 1)
                ->get('api_configs_regras')
                ->row();

            if ($rule) {
                return $rule;
            }
        }

        return null;
    }

    protected function normalizeRule($rule)
    {
        return [
            'limite_cliente'   => min((int) $rule->limite_cliente, $this->getEnvInt('API_RATE_HARD_MAX_CLIENT_REQUESTS', 120)),
            'limite_ip'        => min((int) $rule->limite_ip, $this->getEnvInt('API_RATE_HARD_MAX_IP_REQUESTS', 240)),
            'janela_segundos'  => min(max(1, (int) $rule->janela_segundos), $this->getEnvInt('API_RATE_HARD_MAX_WINDOW_SECONDS', 3600)),
            'falhas_bloqueio'  => max(1, (int) $rule->falhas_bloqueio),
            'bloqueio_minutos' => min(max(1, (int) $rule->bloqueio_minutos), $this->getEnvInt('API_RATE_HARD_MAX_BLOCK_MINUTES', 1440)),
        ];
    }

    protected function getKeys($apiClient, $ip, $authenticated, $endpoint)
    {
        $ip = $this->normalizeIp($ip);

        $keys = [
            ['tipo' => 'ip', 'chave' => (string) $ip, 'escopo' => 'endpoint', 'endpoint' => $endpoint, 'limite' => 'limite_ip'],
            ['tipo' => 'ip', 'chave' => (string) $ip, 'escopo' => 'global', 'endpoint' => 'global', 'limite' => 'limite_ip'],
        ];

        if ($authenticated && $apiClient && !empty($apiClient->id)) {
            array_unshift($keys, [
                'tipo'     => 'cliente',
                'chave'    => (string) $apiClient->id,
                'escopo'   => 'endpoint',
                'endpoint' => $endpoint,
                'limite'   => 'limite_cliente',
            ]);
        }

        return $keys;
    }

    protected function normalizeIp($ip)
    {
        $ip = trim((string) $ip);

        if ($ip === '::1') {
            return '127.0.0.1';
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return '[' . $ip . ']';
        }

        return $ip;
    }

    protected function getRedis()
    {
        if ($this->redis) {
            return $this->redis;
        }

        if (!class_exists('Predis\\Client')) {
            return null;
        }

        try {
            $parameters = [
                'scheme'   => 'tcp',
                'host'     => $this->getEnvString('REDIS_HOST', '127.0.0.1'),
                'port'     => $this->getEnvInt('REDIS_PORT', 6379),
                'database' => max(0, (int) $this->getEnvString('REDIS_DATABASE', '0')),
                'timeout'  => (float) $this->getEnvString('REDIS_TIMEOUT', '0.5'),
            ];

            $password = $this->getEnvString('REDIS_PASSWORD', '');
            if ($password !== '') {
                $parameters['password'] = $password;
            }

            $redis = new Predis\Client($parameters);
            $redis->connect();
            $this->redis = $redis;
            return $this->redis;
        } catch (Exception $exception) {
            return null;
        }
    }

    protected function getActiveBlock($redis, $keys)
    {
        foreach ($keys as $key) {
            $blockKey = $this->buildBlockKey($key);
            if ($redis->exists($blockKey)) {
                return ['ttl' => max(1, (int) $redis->ttl($blockKey))];
            }
        }

        return null;
    }

    protected function incrementCounter($redis, $key, $rule)
    {
        $counterKey = $this->buildCounterKey($key);
        $blockKey   = $this->buildBlockKey($key);
        $limit      = (int) $rule[$key['limite']];

        $script = <<<'LUA'
            local count = redis.call('INCR', KEYS[1])
            if count == 1 then
                redis.call('EXPIRE', KEYS[1], ARGV[1])
            end
            local ttl = redis.call('TTL', KEYS[1])
            if count > tonumber(ARGV[2]) then
                redis.call('SET', KEYS[2], '1', 'EX', ARGV[3])
                return {0, count, ttl}
            end
            return {1, count, ttl}
            LUA
        ;

        $result = $redis->eval($script, 2, $counterKey, $blockKey, (int) $rule['janela_segundos'], $limit, (int) ($rule['bloqueio_minutos'] * 60));
        $count  = (int) ($result[1] ?? 0);

        return [
            'allowed'   => (bool) ($result[0] ?? 0),
            'ttl'       => max(1, (int) ($result[2] ?? $rule['janela_segundos'])),
            'remaining' => max(0, $limit - $count),
        ];
    }

    protected function registerFailure($redis, $keys, $rule)
    {
        $script = <<<'LUA'
            local count = redis.call('INCR', KEYS[1])
            if count == 1 then
                redis.call('EXPIRE', KEYS[1], ARGV[1])
            end
            local ttl = redis.call('TTL', KEYS[1])
            if count >= tonumber(ARGV[2]) then
                local level = math.floor((count - 1) / tonumber(ARGV[2]))
                local seconds = math.min(tonumber(ARGV[3]) * (2 ^ level), tonumber(ARGV[4]))
                redis.call('SET', KEYS[2], '1', 'EX', seconds)
                return {1, ttl, seconds}
            end
            return {0, ttl, 0}
            LUA
        ;

        foreach ($keys as $key) {
            $failureKey = $this->buildFailureKey($key);
            $blockKey   = $this->buildBlockKey($key);
            $result     = $redis->eval(
                $script,
                2,
                $failureKey,
                $blockKey,
                (int) $rule['janela_segundos'],
                (int) $rule['falhas_bloqueio'],
                (int) ($rule['bloqueio_minutos'] * 60),
                (int) ($this->getEnvInt('API_RATE_HARD_MAX_BLOCK_MINUTES', 1440) * 60)
            );

            if (!empty($result[0])) {
                return ['blocked' => true, 'ttl' => max(1, (int) ($result[2] ?? 1))];
            }
        }

        return ['blocked' => false, 'ttl' => 0];
    }

    protected function buildCounterKey($key)
    {
        return 'contex:api:counter:' . $key['tipo'] . ':' . $key['chave'] . ':' . $key['endpoint'];
    }

    protected function buildFailureKey($key)
    {
        return 'contex:api:failure:' . $key['tipo'] . ':' . $key['chave'] . ':' . $key['endpoint'];
    }

    protected function buildBlockKey($key)
    {
        return 'contex:api:block:' . $key['tipo'] . ':' . $key['chave'] . ':' . $key['endpoint'];
    }

    protected function setResponseHeaders($rule, $remaining, $retryAfter)
    {
        $limit = min($rule['limite_cliente'], $rule['limite_ip']);
        $this->CI->output->set_header('X-RateLimit-Limit: ' . (int) $limit);
        $this->CI->output->set_header('X-RateLimit-Remaining: ' . (int) $remaining);
        if ($retryAfter !== null) {
            $this->CI->output->set_header('Retry-After: ' . (int) $retryAfter);
        }
    }

    protected function logSecurityEvent($apiClient, $ip, $event, $origin)
    {
        if (function_exists('gravaLog')) {
            $client = $apiClient && !empty($apiClient->username) ? $apiClient->username : null;
            gravaLog(null, $client, null, sprintf('API: %s (%s)', $event, $ip), $ip, 'api', $origin);
        }
    }

    protected function getMaxBodyBytes()
    {
        return $this->getEnvInt('API_MAX_BODY_BYTES', 1048576);
    }

    protected function getEnvInt($key, $default)
    {
        $value = function_exists('env') ? env($key, $default) : $default;
        return max(1, (int) $value);
    }

    protected function getEnvString($key, $default)
    {
        $value = function_exists('env') ? env($key, $default) : $default;
        return $value === null ? $default : (string) $value;
    }
}
