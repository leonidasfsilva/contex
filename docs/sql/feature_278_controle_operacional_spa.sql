INSERT INTO configs_opcoes (id, descricao, setor, ativo, status)
VALUES (100, 'ACESSO DO CONTEX SPA A API', 'SISTEMA', 1, 1)
ON DUPLICATE KEY UPDATE
    descricao = VALUES(descricao),
    setor = VALUES(setor),
    status = 1;
