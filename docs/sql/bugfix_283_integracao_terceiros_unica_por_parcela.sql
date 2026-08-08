-- Impede que uma mesma parcela possua mais de um vínculo de terceiros.
-- Execute somente depois que a sincronização financeira sanitizar os dados.

SET @indice_existe := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'lancamentos_terceiros_vinculos'
      AND INDEX_NAME = 'uq_lancamento_terceiro_parcela'
);

SET @sql := IF(
    @indice_existe = 0,
    'ALTER TABLE lancamentos_terceiros_vinculos ADD UNIQUE KEY uq_lancamento_terceiro_parcela (id_lancamento_fatura_assoc)',
    'SELECT 1'
);

PREPARE statement FROM @sql;
EXECUTE statement;
DEALLOCATE PREPARE statement;

-- Verificação: a consulta deve retornar zero registros.
SELECT id_lancamento_fatura_assoc, COUNT(*) AS quantidade
FROM lancamentos_terceiros_vinculos
GROUP BY id_lancamento_fatura_assoc
HAVING COUNT(*) > 1;
