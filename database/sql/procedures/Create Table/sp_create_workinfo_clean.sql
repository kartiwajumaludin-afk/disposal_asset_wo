DELIMITER $$
CREATE PROCEDURE sp_create_workinfo_clean()
BEGIN
    CREATE TABLE IF NOT EXISTS workinfo_clean (
        id INT(11) NOT NULL AUTO_INCREMENT,
        ticket_number VARCHAR(50),
        site_id VARCHAR(50),
        site_name VARCHAR(150),
        ticket_sub_type_name VARCHAR(100),
        regional VARCHAR(100),
        network_operation_and_productivity VARCHAR(150),
        teritory_operation VARCHAR(150),
        work_info_user_updater VARCHAR(100),
        work_info_role_updater VARCHAR(100),
        work_info_status_name VARCHAR(100),
        work_info_note TEXT,
        work_info_updated_date DATETIME,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;