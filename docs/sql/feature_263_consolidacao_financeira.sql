CREATE TABLE consolidacoes_financeiras (
    id INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    rotina VARCHAR(100) NOT NULL,
    origem VARCHAR(30) NOT NULL,
    status VARCHAR(20) NOT NULL,
    iniciado_em DATETIME NOT NULL,
    finalizado_em DATETIME NULL DEFAULT NULL,
    msg_erro TEXT NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_consolidacao_financeira_usuario_rotina (id_usuario, rotina),
    KEY idx_consolidacao_financeira_iniciado_em (iniciado_em),
    KEY idx_consolidacao_financeira_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
