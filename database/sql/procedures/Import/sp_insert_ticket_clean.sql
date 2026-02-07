DELIMITER $$

DROP PROCEDURE IF EXISTS sp_insert_ticket_clean $$

CREATE PROCEDURE sp_insert_ticket_clean()
BEGIN
    INSERT INTO ticket_clean (
        ticket_number,
        ticket_sub_type_name,
        ticket_status_name,
        regional,
        network_operation_and_productivity,
        teritory_operation,
        site_id,
        site_name,
        assignee_group,
        assignee,
        ticket_summary,
        ticket_created_date,
        ticket_resolved_date,
        ticket_cleared_date,
        working_permit_number,
        working_permit_status_name,
        working_permit_status_text,
        working_permit_activity_name,
        working_permit_activity_description,
        working_permit_activity_category,
        site_owner,
        working_permit_start_date,
        working_permit_end_date,
        working_permit_updated_date,
        sik_number,
        sik_status_name
    )
    SELECT 
        r.`Ticket Number`,
        r.`Ticket Sub Type Name`,
        r.`Ticket Status Name`,
        r.`Regional`,
        r.`Network Operation and Productivity`,
        r.`Teritory Operation`,
        r.`Site ID`,
        r.`Site Name`,
        r.`Assignee Group`,
        r.`Assignee`,
        r.`Ticket Summary`,
        STR_TO_DATE(r.`Ticket Created Date`, '%d-%m-%y %H:%i'),
        STR_TO_DATE(r.`Ticket Resolved Date`, '%d-%m-%y %H:%i'),
        STR_TO_DATE(r.`Ticket Cleared Date`, '%d-%m-%y %H:%i'),
        r.`Working Permit Number`,
        r.`Working Permit Status Name`,
        r.`Working Permit Status Text`,
        r.`Working Permit Activity Name`,
        r.`Working Permit Activity Description`,
        r.`Working Permit Activity Category`,
        r.`Site Owner`,
        STR_TO_DATE(r.`Working Permit Start Date`, '%d-%m-%y %H:%i'),
        STR_TO_DATE(r.`Working Permit End Date`, '%d-%m-%y %H:%i'),
        STR_TO_DATE(r.`Working Permit Updated Date`, '%d-%m-%y %H:%i'),
        r.`SIK Number`,
        r.`SIK Status Name`
    FROM ticket_raw r
    WHERE NOT EXISTS (
        SELECT 1 FROM ticket_clean c
        WHERE c.ticket_number = r.`Ticket Number`
    );
END $$

DELIMITER ;
