-- Adiciona a opção por cartão para vincular automaticamente compras de terceiros.
ALTER TABLE configs_faturas
ADD COLUMN auto_vinculo_terceiros TINYINT(1) NULL DEFAULT NULL AFTER auto_vinculo;
