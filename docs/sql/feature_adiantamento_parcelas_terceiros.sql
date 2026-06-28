ALTER TABLE lancamentos_faturas_assoc
ADD parcela_terceiro_pago TINYINT(1) NULL DEFAULT NULL;

CREATE TABLE lancamentos_terceiros_vinculos (
    id INT NOT NULL AUTO_INCREMENT,
    id_lancamento INT NOT NULL,
    id_lancamento_fatura INT NOT NULL,
    id_fatura INT NOT NULL,
    id_lancamento_fatura_assoc INT NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_lancamento_terceiro_assoc (id_lancamento, id_lancamento_fatura_assoc),
    KEY idx_lancamento_terceiro_lancamento (id_lancamento),
    KEY idx_lancamento_terceiro_lancamento_fatura (id_lancamento_fatura),
    KEY idx_lancamento_terceiro_fatura (id_fatura),
    KEY idx_lancamento_terceiro_assoc (id_lancamento_fatura_assoc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Em desenvolvimento, prefira dropar/recriar a tabela lancamentos_terceiros_vinculos.
-- Rode este bloco apenas quando precisar preservar vínculos já existentes.
ALTER TABLE lancamentos_terceiros_vinculos
ADD id_lancamento_fatura INT NULL AFTER id_lancamento,
ADD id_fatura INT NULL AFTER id_lancamento_fatura;

UPDATE lancamentos_terceiros_vinculos ltv
INNER JOIN lancamentos_faturas_assoc lfa
ON lfa.id_assoc = ltv.id_lancamento_fatura_assoc
SET ltv.id_lancamento_fatura = lfa.id_lancamento,
    ltv.id_fatura = lfa.id_fatura;

ALTER TABLE lancamentos_terceiros_vinculos
MODIFY id_lancamento_fatura INT NOT NULL,
MODIFY id_fatura INT NOT NULL,
ADD KEY idx_lancamento_terceiro_lancamento_fatura (id_lancamento_fatura),
ADD KEY idx_lancamento_terceiro_fatura (id_fatura);
