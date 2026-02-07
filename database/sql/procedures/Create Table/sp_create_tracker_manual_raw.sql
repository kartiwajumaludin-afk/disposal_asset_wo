DELIMITER $$
CREATE PROCEDURE sp_create_tracker_manual_raw()
BEGIN
    CREATE TABLE IF NOT EXISTS tracker_manual_raw (
        id INT(11) NOT NULL AUTO_INCREMENT,
        ticket_number VARCHAR(50) NOT NULL,
        tp_company VARCHAR(100),
        latitude DECIMAL(10,6),
        longitude DECIMAL(10,6),
        caf_status VARCHAR(50),
        general_status VARCHAR(100),
        start_permit_tp_date_raw VARCHAR(20),
        end_permit_tp_date_raw VARCHAR(20),
        status_permit_tp VARCHAR(50),
        ticket_batch VARCHAR(50),
        site_status VARCHAR(50),
        site_issue VARCHAR(50),
        category_issue VARCHAR(100),
        detail_issue VARCHAR(255),
        remark_dismantle TEXT,
        mom TEXT,
        partner_company VARCHAR(100),
        plan_dismantle_date_raw VARCHAR(20),
        pic_team VARCHAR(100),
        no_handphone VARCHAR(14),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE INDEX uk_ticket_number (ticket_number),
        INDEX idx_ticket_batch (ticket_batch)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;
