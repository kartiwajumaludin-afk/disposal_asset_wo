DELIMITER $$

DROP PROCEDURE IF EXISTS sp_insert_workinfo_raw$$

CREATE PROCEDURE sp_insert_workinfo_raw()
BEGIN
    -- LOG DUPLICATE DI STG
    INSERT INTO import_duplicate_log (
        ticket_number,
        source,
        action,
        reason,
        filter_col_1,
        filter_col_2,
        filter_col_3,
        processed_at
    )
    SELECT
        s.`Ticket Number`,
        'workinfo',
        'SKIPPED',
        'DUPLICATE_BY_3COL_FILTER',
        s.`Ticket Number`,
        s.`Work Info Status Name`,
        s.`Work Info Updated Date`,
        NOW()
    FROM workinfo_raw_stg s
    JOIN (
        SELECT `Ticket Number`, `Work Info Status Name`, `Work Info Updated Date`
        FROM workinfo_raw_stg
        GROUP BY `Ticket Number`, `Work Info Status Name`, `Work Info Updated Date`
        HAVING COUNT(*) > 1
    ) d
      ON s.`Ticket Number`          = d.`Ticket Number`
     AND s.`Work Info Status Name`  = d.`Work Info Status Name`
     AND s.`Work Info Updated Date` = d.`Work Info Updated Date`;

    -- DELETE DUPLICATE DARI STG
    DELETE s
    FROM workinfo_raw_stg s
    JOIN (
        SELECT `Ticket Number`, `Work Info Status Name`, `Work Info Updated Date`
        FROM workinfo_raw_stg
        GROUP BY `Ticket Number`, `Work Info Status Name`, `Work Info Updated Date`
        HAVING COUNT(*) > 1
    ) d
      ON s.`Ticket Number`          = d.`Ticket Number`
     AND s.`Work Info Status Name`  = d.`Work Info Status Name`
     AND s.`Work Info Updated Date` = d.`Work Info Updated Date`;

    -- INSERT VALID WORKINFO (SKIPSERT)
    INSERT INTO workinfo_raw (
        `Ticket Number`,
        `Ticket Sub Type Name`,
        `Regional`,
        `Network Operation and Productivity`,
        `Teritory Operation`,
        `Site ID`,
        `Site Name`,
        `Work Info Updated Date`,
        `Work Info Status Name`,
        `Work Info Note`,
        `Work Info User Updater`,
        `Work Info Role Updater`
    )
    SELECT
        s.`Ticket Number`,
        s.`Ticket Sub Type Name`,
        s.`Regional`,
        s.`Network Operation and Productivity`,
        s.`Teritory Operation`,
        s.`Site ID`,
        s.`Site Name`,
        s.`Work Info Updated Date`,
        s.`Work Info Status Name`,
        s.`Work Info Note`,
        s.`Work Info User Updater`,
        s.`Work Info Role Updater`
    FROM workinfo_raw_stg s
    WHERE NOT EXISTS (
        SELECT 1
        FROM workinfo_raw r
        WHERE r.`Ticket Number`          = s.`Ticket Number`
          AND r.`Work Info Status Name`  = s.`Work Info Status Name`
          AND r.`Work Info Updated Date` = s.`Work Info Updated Date`
    );

    -- TRUNCATE STG
    TRUNCATE TABLE workinfo_raw_stg;

    -- RESULT
    SELECT 'SUCCESS' AS status, 'workinfo_raw_stg → workinfo_raw OK' AS message;
END$$

DELIMITER ;
