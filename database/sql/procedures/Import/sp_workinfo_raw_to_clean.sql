DELIMITER $$

DROP PROCEDURE IF EXISTS sp_workinfo_raw_to_clean $$

CREATE PROCEDURE sp_workinfo_raw_to_clean()
BEGIN
    INSERT INTO workinfo_clean (
        ticket_number,
        site_id,
        site_name,
        ticket_sub_type_name,
        regional,
        network_operation_and_productivity,
        teritory_operation,
        work_info_user_updater,
        work_info_role_updater,
        work_info_status_name,
        work_info_note,
        work_info_updated_date
    )
    SELECT 
        r.`Ticket Number`,
        r.`Site ID`,
        r.`Site Name`,
        r.`Ticket Sub Type Name`,
        r.`Regional`,
        r.`Network Operation and Productivity`,
        r.`Teritory Operation`,
        r.`Work Info User Updater`,
        r.`Work Info Role Updater`,
        r.`Work Info Status Name`,
        r.`Work Info Note`,
        STR_TO_DATE(r.`Work Info Updated Date`, '%d-%m-%y %H:%i')
    FROM workinfo_raw r
    WHERE NOT EXISTS (
        SELECT 1 FROM workinfo_clean c
        WHERE c.ticket_number = r.`Ticket Number`
          AND c.work_info_status_name = r.`Work Info Status Name`
          AND c.work_info_updated_date = STR_TO_DATE(r.`Work Info Updated Date`, '%d-%m-%y %H:%i')
    );
END $$

DELIMITER ;
