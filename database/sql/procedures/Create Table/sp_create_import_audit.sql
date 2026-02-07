DELIMITER $$

CREATE PROCEDURE sp_create_import_audit()
BEGIN
    CREATE TABLE IF NOT EXISTS import_audit (
        id INT(11) NOT NULL AUTO_INCREMENT,
        file_type VARCHAR(50) NOT NULL,
        status ENUM('DONE', 'PENDING') NOT NULL,
        row_count INT(11) DEFAULT 0,
        uploaded_at DATETIME,
        PRIMARY KEY (id),
        UNIQUE INDEX uk_file_type (file_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$

DELIMITER ;