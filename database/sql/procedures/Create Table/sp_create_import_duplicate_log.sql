DELIMITER $$
CREATE PROCEDURE sp_create_import_duplicate_log()
BEGIN
    CREATE TABLE IF NOT EXISTS import_duplicate_log (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        ticket_number VARCHAR(50) NOT NULL,
        source VARCHAR(50) NOT NULL,
        action VARCHAR(50) NOT NULL,
        reason VARCHAR(100) NOT NULL,
        filter_col_1 VARCHAR(255),
        filter_col_2 VARCHAR(255),
        filter_col_3 VARCHAR(255),
        processed_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        INDEX idx_ticket_number (ticket_number),
        INDEX idx_source (source),
        INDEX idx_action (action),
        INDEX idx_processed_at (processed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;
