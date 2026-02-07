DELIMITER $$
CREATE PROCEDURE sp_create_daily_activity()
BEGIN
    CREATE TABLE IF NOT EXISTS daily_activity (
        id INT(11) NOT NULL AUTO_INCREMENT,
        ticket_number VARCHAR(50) NOT NULL,
        site_id VARCHAR(50),
        site_name VARCHAR(150),
        regional VARCHAR(100),
        network_operation_and_productivity VARCHAR(150),
        teritory_operation VARCHAR(150),
        ticket_status_name VARCHAR(100),
        update_ticket_status_name VARCHAR(100),
        plan_dismantle_date DATE,
        pic_team VARCHAR(100),
        assigned_by VARCHAR(100),
        assignment_status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        task_status ENUM('planned', 'assigned', 'in_progress', 'reported', 'verified', 'completed', 'replanned') DEFAULT 'planned',
        category_issue VARCHAR(100),
        detail_issue VARCHAR(255),
        remark_dismantle TEXT,
        activity_date DATE NOT NULL,
        PRIMARY KEY (id)   
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;