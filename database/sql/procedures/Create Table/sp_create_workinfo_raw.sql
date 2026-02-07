DELIMITER $$
CREATE PROCEDURE sp_create_workinfo_raw()
BEGIN
    CREATE TABLE IF NOT EXISTS workinfo_raw (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        `Ticket Number` VARCHAR(50),
        `Ticket Sub Type Name` VARCHAR(100),
        `Regional` VARCHAR(100),
        `Network Operation and Productivity` VARCHAR(150),
        `Teritory Operation` VARCHAR(150),
        `Site ID` VARCHAR(50),
        `Site Name` VARCHAR(150),
        `Work Info Updated Date` TEXT,
        `Work Info Status Name` VARCHAR(100),
        `Work Info Note` TEXT,
        `Work Info User Updater` VARCHAR(100),
        `Work Info Role Updater` VARCHAR(100),
        PRIMARY KEY (id)       
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;