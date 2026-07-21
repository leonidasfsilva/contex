DROP TABLE IF EXISTS api_controles;
DROP TABLE IF EXISTS api_limites;

CREATE TABLE IF NOT EXISTS api_configs_regras (
    id INT NOT NULL AUTO_INCREMENT,
    endpoint VARCHAR(150) NOT NULL,
    metodo VARCHAR(10) NOT NULL,
    limite_cliente INT NOT NULL,
    limite_ip INT NOT NULL,
    janela_segundos INT NOT NULL,
    falhas_bloqueio INT NOT NULL,
    bloqueio_minutos INT NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_api_configs_regras_endpoint_metodo (endpoint, metodo),
    KEY idx_api_configs_regras_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO api_configs_regras
    (endpoint, metodo, limite_cliente, limite_ip, janela_segundos, falhas_bloqueio, bloqueio_minutos)
VALUES
    ('*', '*', 60, 120, 60, 5, 5),
    ('api/v1/mikrotik/sendemail', 'POST', 10, 30, 60, 5, 15),
    ('api/v1/cron/rotinas/financeiro', 'POST', 2, 5, 300, 3, 30)
ON DUPLICATE KEY UPDATE
    limite_cliente = VALUES(limite_cliente),
    limite_ip = VALUES(limite_ip),
    janela_segundos = VALUES(janela_segundos),
    falhas_bloqueio = VALUES(falhas_bloqueio),
    bloqueio_minutos = VALUES(bloqueio_minutos),
    ativo = VALUES(ativo);

ALTER TABLE logs
ADD COLUMN modulo VARCHAR(50) NULL DEFAULT NULL AFTER descricao,
ADD COLUMN origem VARCHAR(150) NULL DEFAULT NULL AFTER modulo;
