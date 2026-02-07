DELIMITER $$
CREATE PROCEDURE sp_create_ticket_raw()
BEGIN
    CREATE TABLE IF NOT EXISTS ticket_raw (
        `Ticket Number` VARCHAR(50) NOT NULL,
        `Ticket Sub Type Name` VARCHAR(100),
        `Ticket Status Name` VARCHAR(100),
        `Regional` VARCHAR(100),
        `Network Operation and Productivity` VARCHAR(150),
        `Teritory Operation` VARCHAR(150),
        `Site ID` VARCHAR(50),
        `Site Name` VARCHAR(150),
        `Assignee Group` VARCHAR(100),
        `Assignee` VARCHAR(100),
        `Ticket Summary` VARCHAR(150),
        `Ticket Created Date` TEXT,
        `Ticket Resolved Date` TEXT,
        `Ticket Cleared Date` TEXT,
        `Working Permit Number` VARCHAR(50),
        `Working Permit Status Name` VARCHAR(100),
        `Working Permit Status Text` TEXT,
        `Working Permit Activity Name` VARCHAR(150),
        `Working Permit Activity Description` TEXT,
        `Working Permit Activity Category` VARCHAR(100),
        `Site Owner` VARCHAR(100),
        `Working Permit Start Date` TEXT,
        `Working Permit End Date` TEXT,
        `Working Permit Updated Date` TEXT,
        `SIK Number` VARCHAR(50),
        `SIK Status Name` VARCHAR(100)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;