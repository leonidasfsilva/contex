ALTER TABLE clientes_api
ADD scopes VARCHAR(255) NULL DEFAULT NULL AFTER token;

UPDATE clientes_api
SET scopes = 'mikrotik'
WHERE username = 'mikrotik_mxcode'
AND status = 1;
