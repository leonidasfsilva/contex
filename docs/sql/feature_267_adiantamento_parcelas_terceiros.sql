CREATE TABLE lancamentos_terceiros_pagamentos (
    id INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_lancamento INT NOT NULL,
    id_lancamento_terceiros_vinculo INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_pagamento DATE NULL DEFAULT NULL,
    forma_pgto INT NOT NULL DEFAULT 2,
    tipo_pgto TINYINT(1) NOT NULL COMMENT '1=parcela, 2=compra',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL DEFAULT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY idx_lancamento_terceiro_pagamento_usuario (id_usuario),
    KEY idx_lancamento_terceiro_pagamento_lancamento (id_lancamento),
    KEY idx_lancamento_terceiro_pagamento_vinculo (id_lancamento_terceiros_vinculo),
    KEY idx_lancamento_terceiro_pagamento_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE lancamentos_terceiros_vinculos
ADD nome_terceiro VARCHAR(255) NULL DEFAULT NULL AFTER id_fatura,
ADD id_cartao INT NULL DEFAULT NULL AFTER nome_terceiro,
ADD mes_vencimento VARCHAR(2) NULL DEFAULT NULL AFTER id_cartao,
ADD ano_vencimento VARCHAR(4) NULL DEFAULT NULL AFTER mes_vencimento,
ADD KEY idx_lancamento_terceiro_nome (nome_terceiro),
ADD KEY idx_lancamento_terceiro_cartao (id_cartao),
ADD KEY idx_lancamento_terceiro_vencimento (mes_vencimento, ano_vencimento);

UPDATE lancamentos_terceiros_vinculos ltv
INNER JOIN lancamentos_faturas_assoc lfa
ON lfa.id_assoc = ltv.id_lancamento_fatura_assoc
INNER JOIN lancamentos_faturas lf
ON lf.id_lancamento = lfa.id_lancamento
INNER JOIN faturas f
ON f.id_fatura = lfa.id_fatura
SET ltv.nome_terceiro = lf.nome_cliente,
    ltv.id_cartao = f.id_cartao,
    ltv.mes_vencimento = LPAD(MONTH(f.vencimento), 2, '0'),
    ltv.ano_vencimento = YEAR(f.vencimento);