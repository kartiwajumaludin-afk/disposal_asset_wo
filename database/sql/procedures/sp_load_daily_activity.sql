DELIMITER $$

DROP PROCEDURE IF EXISTS sp_load_daily_activity$$

CREATE PROCEDURE sp_load_daily_activity()
BEGIN
    -- 1. INSERT MUNG DATA ANU DI-PLAN DINTEN IEU (Filter ku CURDATE)
    INSERT INTO daily_activity (
        ticket_number, site_id, site_name, regional, 
        network_operation_and_productivity, teritory_operation, 
        ticket_status_name, update_ticket_status_name, 
        plan_dismantle_date, pic_team, activity_date,
        assignment_status, task_status
    )
    SELECT 
        tr.ticket_number, tr.site_id, tr.site_name, tr.regional, 
        tr.network_operation_and_productivity, tr.teritory_operation, 
        tr.ticket_status_name, tr.ticket_status_name, 
        tr.plan_dismantle_date, tr.pic_team, CURDATE(),
        'pending', 'planned'
    FROM tracker tr
    LEFT JOIN daily_activity da 
        ON da.ticket_number = tr.ticket_number 
        AND da.activity_date = CURDATE()
    WHERE da.id IS NULL 
    AND tr.plan_dismantle_date = CURDATE(); -- <== IEU FILTERNANA KANG

    -- 2. UPDATE STATUS UPAMI AYA PERUBAHAN DI TRACKER DINTEN IEU
    UPDATE daily_activity da
    JOIN tracker tr ON da.ticket_number = tr.ticket_number
    SET da.update_ticket_status_name = tr.ticket_status_name,
        da.updated_at = NOW()
    WHERE da.activity_date = CURDATE();
END$$

DELIMITER ;