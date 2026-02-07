DELIMITER $$

DROP PROCEDURE IF EXISTS sp_upsert_tracker_base $$
CREATE PROCEDURE sp_upsert_tracker_base()
BEGIN
    DECLARE v_now DATETIME DEFAULT NOW();
    DECLARE v_rows INT DEFAULT 0;

    SELECT COUNT(*) INTO v_rows FROM ticket_clean;

    INSERT INTO tracker (
        ticket_number,
        site_id,
        site_name,
        regional,
        network_operation_and_productivity,
        teritory_operation,
        ticket_status_name,
        ticket_summary,
        ticket_sub_type_name,
        ticket_created_date,
        working_permit_start_date,
        working_permit_end_date,
        working_permit_status_name,
        assignee_group,
        created_at,
        updated_at
    )
    SELECT 
        tc.ticket_number,
        tc.site_id,
        tc.site_name,
        tc.regional,
        tc.network_operation_and_productivity,
        tc.teritory_operation,
        tc.ticket_status_name,
        tc.ticket_summary,
        tc.ticket_sub_type_name,
        tc.ticket_created_date,
        tc.working_permit_start_date,
        tc.working_permit_end_date,
        tc.working_permit_status_name,
        tc.assignee_group,
        v_now,
        v_now
    FROM ticket_clean tc
    ON DUPLICATE KEY UPDATE
        site_id = VALUES(site_id),
        site_name = VALUES(site_name),
        regional = VALUES(regional),
        network_operation_and_productivity = VALUES(network_operation_and_productivity),
        teritory_operation = VALUES(teritory_operation),
        ticket_status_name = VALUES(ticket_status_name),
        ticket_summary = VALUES(ticket_summary),
        ticket_sub_type_name = VALUES(ticket_sub_type_name),
        ticket_created_date = VALUES(ticket_created_date),
        working_permit_start_date = VALUES(working_permit_start_date),
        working_permit_end_date = VALUES(working_permit_end_date),
        working_permit_status_name = VALUES(working_permit_status_name),
        assignee_group = VALUES(assignee_group),
        updated_at = v_now;

    SELECT
        'SUCCESS' AS status,
        v_rows AS affected_rows;
END$$

DELIMITER ;
