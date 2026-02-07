DELIMITER $$

CREATE PROCEDURE sp_create_asset_clean()
BEGIN
    CREATE TABLE IF NOT EXISTS asset_clean (
        id INT(11) NOT NULL AUTO_INCREMENT,
        ticket_number VARCHAR(50),
        site_id VARCHAR(50),
        site_name VARCHAR(150),
        ticket_sub_type_name VARCHAR(100),
        ticket_status_name VARCHAR(100),
        assignee_group VARCHAR(100),
        assignee VARCHAR(100),
        ticket_summary VARCHAR(150),
        ticket_created_date DATETIME,
        ticket_resolved_date DATETIME,
        ticket_cleared_date DATETIME,
        barcode_number VARCHAR(100) NOT NULL,
        part_code VARCHAR(100),
        part_name VARCHAR(150),
        brand_name VARCHAR(100),
        asset_physical_group_name VARCHAR(150),
        asset_po_number VARCHAR(100),
        asset_status_name VARCHAR(100),
        asset_flag_name VARCHAR(100),
        asset_mflag VARCHAR(50),
        PRIMARY KEY (id)        
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$

DELIMITER ;