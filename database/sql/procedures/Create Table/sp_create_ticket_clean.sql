DELIMITER $$
CREATE PROCEDURE sp_create_ticket_clean()
BEGIN
    CREATE TABLE IF NOT EXISTS ticket_clean (
        id INT(11) NOT NULL AUTO_INCREMENT,
        ticket_number VARCHAR(50),
        ticket_sub_type_name VARCHAR(100),
        ticket_status_name VARCHAR(100),
        regional VARCHAR(100),
        network_operation_and_productivity VARCHAR(150),
        teritory_operation VARCHAR(150),
        site_id VARCHAR(50),
        site_name VARCHAR(150),
        assignee_group VARCHAR(100),
        assignee VARCHAR(100),
        ticket_summary VARCHAR(150),
        ticket_created_date DATETIME,
        ticket_resolved_date DATETIME,
        ticket_cleared_date DATETIME,
        working_permit_number VARCHAR(50),
        working_permit_status_name VARCHAR(100),
        working_permit_status_text TEXT,
        working_permit_activity_name VARCHAR(150),
        working_permit_activity_description TEXT,
        working_permit_activity_category VARCHAR(100),
        site_owner VARCHAR(100),
        working_permit_start_date DATETIME,
        working_permit_end_date DATETIME,
        working_permit_updated_date DATETIME,
        sik_number VARCHAR(50),
        sik_status_name VARCHAR(100),
        PRIMARY KEY (id)       
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;
