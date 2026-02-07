DELIMITER $$

DROP PROCEDURE IF EXISTS sp_upsert_asset_raw $$

CREATE PROCEDURE sp_upsert_asset_raw()
BEGIN
    INSERT INTO asset_raw (
        `Ticket Number`,
        `Ticket Sub Type Name`,
        `Ticket Status Name`,
        `Site ID`,
        `Site Name`,
        `Assignee Group`,
        `Assignee`,
        `Ticket Summary`,
        `Ticket Created Date`,
        `Ticket Resolved Date`,
        `Ticket Cleared Date`,
        `Barcode Number`,
        `Part Code`,
        `Part Name`,
        `Brand Name`,
        `Asset Physical Group Name`,
        `Asset PO Number`,
        `Asset Status Name`,
        `Asset Flag Name`,
        `Asset mFlag`
    )
    SELECT
        s.`Ticket Number`,
        s.`Ticket Sub Type Name`,
        s.`Ticket Status Name`,
        s.`Site ID`,
        s.`Site Name`,
        s.`Assignee Group`,
        s.`Assignee`,
        s.`Ticket Summary`,
        s.`Ticket Created Date`,
        s.`Ticket Resolved Date`,
        s.`Ticket Cleared Date`,
        s.`Barcode Number`,
        s.`Part Code`,
        s.`Part Name`,
        s.`Brand Name`,
        s.`Asset Physical Group Name`,
        s.`Asset PO Number`,
        s.`Asset Status Name`,
        s.`Asset Flag Name`,
        s.`Asset mFlag`
    FROM asset_raw_stg s
    ON DUPLICATE KEY UPDATE
        `Ticket Sub Type Name`        = VALUES(`Ticket Sub Type Name`),
        `Ticket Status Name`          = VALUES(`Ticket Status Name`),
        `Site ID`                     = VALUES(`Site ID`),
        `Site Name`                   = VALUES(`Site Name`),
        `Assignee Group`              = VALUES(`Assignee Group`),
        `Assignee`                    = VALUES(`Assignee`),
        `Ticket Summary`              = VALUES(`Ticket Summary`),
        `Ticket Created Date`         = VALUES(`Ticket Created Date`),
        `Ticket Resolved Date`        = VALUES(`Ticket Resolved Date`),
        `Ticket Cleared Date`         = VALUES(`Ticket Cleared Date`),
        `Barcode Number`              = VALUES(`Barcode Number`),
        `Part Code`                   = VALUES(`Part Code`),
        `Part Name`                   = VALUES(`Part Name`),
        `Brand Name`                  = VALUES(`Brand Name`),
        `Asset Physical Group Name`   = VALUES(`Asset Physical Group Name`),
        `Asset PO Number`             = VALUES(`Asset PO Number`),
        `Asset Status Name`           = VALUES(`Asset Status Name`),
        `Asset Flag Name`             = VALUES(`Asset Flag Name`),
        `Asset mFlag`                 = VALUES(`Asset mFlag`);
END$$

DELIMITER ;














