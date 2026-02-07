DELIMITER $$

CREATE PROCEDURE sp_create_asset_raw()
BEGIN
    CREATE TABLE IF NOT EXISTS asset_raw (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        `Ticket Number` VARCHAR(50),
        `Ticket Sub Type Name` VARCHAR(100),
        `Ticket Status Name` VARCHAR(100),
        `Site ID` VARCHAR(50),
        `Site Name` VARCHAR(150),
        `Assignee Group` VARCHAR(100),
        `Assignee` VARCHAR(100),
        `Ticket Summary` VARCHAR(150),
        `Ticket Created Date` TEXT,
        `Ticket Resolved Date` TEXT,
        `Ticket Cleared Date` TEXT,
        `Barcode Number` VARCHAR(100),
        `Part Code` VARCHAR(100),
        `Part Name` VARCHAR(150),
        `Brand Name` VARCHAR(100),
        `Asset Physical Group Name` VARCHAR(150),
        `Asset PO Number` VARCHAR(100),
        `Asset Status Name` VARCHAR(100),
        `Asset Flag Name` VARCHAR(100),
        `Asset mFlag` VARCHAR(50),
        PRIMARY KEY (id)       
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$

DELIMITER ;