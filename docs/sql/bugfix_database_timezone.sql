-- Execute um backup do banco antes desta migracao.

SET time_zone = '+00:00';

DROP PROCEDURE IF EXISTS contex_migrar_timestamps_locais;

DELIMITER //

CREATE PROCEDURE contex_migrar_timestamps_locais()
BEGIN
    DECLARE v_done TINYINT DEFAULT 0;
    DECLARE v_table VARCHAR(64);
    DECLARE v_column VARCHAR(64);
    DECLARE v_type VARCHAR(32);
    DECLARE v_nullable VARCHAR(3);
    DECLARE v_default VARCHAR(64);
    DECLARE v_extra VARCHAR(255);
    DECLARE v_sql TEXT;

    DECLARE v_cursor CURSOR FOR
        SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND DATA_TYPE = 'timestamp'
          AND (
              COLUMN_DEFAULT LIKE 'CURRENT_TIMESTAMP%'
              OR LOWER(EXTRA) LIKE '%on update current_timestamp%'
          );

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

    OPEN v_cursor;

    migrar: LOOP
        FETCH v_cursor INTO v_table, v_column, v_type, v_nullable, v_default, v_extra;

        IF v_done = 1 THEN
            LEAVE migrar;
        END IF;

        -- Materializa o historico em horario de Sao Paulo antes de remover
        -- a conversao automatica do tipo TIMESTAMP.
        SET v_sql = CONCAT(
            'UPDATE `', REPLACE(v_table, '`', '``'), '` ',
            'SET `', REPLACE(v_column, '`', '``'), '` = DATE_SUB(`',
            REPLACE(v_column, '`', '``'), '`, INTERVAL 3 HOUR) ',
            'WHERE `', REPLACE(v_column, '`', '``'), '` IS NOT NULL'
        );
        SET @contex_sql = v_sql;
        PREPARE contex_stmt FROM @contex_sql;
        EXECUTE contex_stmt;
        DEALLOCATE PREPARE contex_stmt;

        SET v_sql = CONCAT(
            'ALTER TABLE `', REPLACE(v_table, '`', '``'), '` ',
            'MODIFY COLUMN `', REPLACE(v_column, '`', '``'), '` ',
            REPLACE(v_type, 'timestamp', 'datetime')
        );

        IF v_nullable = 'YES' THEN
            SET v_sql = CONCAT(v_sql, ' NULL');
        ELSE
            SET v_sql = CONCAT(v_sql, ' NOT NULL');
        END IF;

        IF v_default IS NOT NULL THEN
            SET v_sql = CONCAT(v_sql, ' DEFAULT ', v_default);
        ELSEIF v_nullable = 'YES' THEN
            SET v_sql = CONCAT(v_sql, ' DEFAULT NULL');
        END IF;

        IF LOWER(v_extra) LIKE '%on update current_timestamp%' THEN
            SET v_sql = CONCAT(v_sql, ' ON UPDATE CURRENT_TIMESTAMP');
        END IF;

        SET @contex_sql = v_sql;
        PREPARE contex_stmt FROM @contex_sql;
        EXECUTE contex_stmt;
        DEALLOCATE PREPARE contex_stmt;
    END LOOP;

    CLOSE v_cursor;
END//

DELIMITER ;

CALL contex_migrar_timestamps_locais();
DROP PROCEDURE contex_migrar_timestamps_locais;

-- Novos valores automaticos serao locais nas conexoes da aplicacao,
-- pois o hook DatabaseTimezone configura a sessao para UTC-03:00.
