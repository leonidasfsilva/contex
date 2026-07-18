-- Execute primeiro um backup da tabela logs.

-- Garante que a conversao do TIMESTAMP historico seja interpretada em UTC.
SET time_zone = '+00:00';

-- Remove a conversao dependente do fuso da sessao para consultas futuras.
ALTER TABLE logs
MODIFY COLUMN data_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Os valores foram materializados em UTC na conversao acima.
UPDATE logs
SET data_registro = DATE_SUB(data_registro, INTERVAL 3 HOUR);

-- A partir daqui, gravaLog() informa explicitamente o horario de Sao Paulo.
