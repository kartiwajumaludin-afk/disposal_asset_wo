DELIMITER $$

DROP PROCEDURE IF EXISTS sp_tracker_manual_raw_to_update$$

CREATE PROCEDURE sp_tracker_manual_raw_to_update()
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'ERROR' AS status, 'tracker_manual_raw_to_update failed' AS message;
    END;
    
    START TRANSACTION;
    
    INSERT INTO tracker_manual_update (
        ticket_number,
        tp_company,
        latitude,
        longitude,
        caf_status,
        general_status,
        start_permit_tp_date,
        end_permit_tp_date,
        status_permit_tp,
        ticket_batch,
        site_status,
        site_issue,
        category_issue,
        detail_issue,
        remark_dismantle,
        mom,
        partner_company,
        plan_dismantle_date,
        pic_team,
        no_handphone,
        plan_dismantle_date_raw,
        created_at,
        updated_at
    )
    SELECT
        r.ticket_number,
        r.tp_company,
        r.latitude,
        r.longitude,
        r.caf_status,
        r.general_status,
        /* start_permit_tp_date */
        CASE
            WHEN r.start_permit_tp_date_raw REGEXP '^[0-9]{1,2}-[A-Za-z]{3}-[0-9]{2,4}$'
                THEN STR_TO_DATE(r.start_permit_tp_date_raw, '%d-%b-%Y')
            WHEN r.start_permit_tp_date_raw REGEXP '^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$'
                THEN STR_TO_DATE(r.start_permit_tp_date_raw, '%d/%m/%Y')
            WHEN r.start_permit_tp_date_raw REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{2,4}$'
                THEN STR_TO_DATE(r.start_permit_tp_date_raw, '%d-%m-%Y')
            ELSE NULL
        END,
        /* end_permit_tp_date */
        CASE
            WHEN r.end_permit_tp_date_raw REGEXP '^[0-9]{1,2}-[A-Za-z]{3}-[0-9]{2,4}$'
                THEN STR_TO_DATE(r.end_permit_tp_date_raw, '%d-%b-%Y')
            WHEN r.end_permit_tp_date_raw REGEXP '^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$'
                THEN STR_TO_DATE(r.end_permit_tp_date_raw, '%d/%m/%Y')
            WHEN r.end_permit_tp_date_raw REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{2,4}$'
                THEN STR_TO_DATE(r.end_permit_tp_date_raw, '%d-%m-%Y')
            ELSE NULL
        END,
        r.status_permit_tp,
        r.ticket_batch,
        r.site_status,
        r.site_issue,
        r.category_issue,
        r.detail_issue,
        r.remark_dismantle,
        r.mom,
        r.partner_company,
        /* plan_dismantle_date */
        CASE
            WHEN r.plan_dismantle_date_raw REGEXP '^[0-9]{1,2}-[A-Za-z]{3}-[0-9]{2,4}$'
                THEN STR_TO_DATE(r.plan_dismantle_date_raw, '%d-%b-%Y')
            WHEN r.plan_dismantle_date_raw REGEXP '^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$'
                THEN STR_TO_DATE(r.plan_dismantle_date_raw, '%d/%m/%Y')
            WHEN r.plan_dismantle_date_raw REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{2,4}$'
                THEN STR_TO_DATE(r.plan_dismantle_date_raw, '%d-%m-%Y')
            ELSE NULL
        END,
        r.pic_team,
        r.no_handphone,
        r.plan_dismantle_date_raw,
        NOW(),
        NOW()
    FROM tracker_manual_raw r
    ON DUPLICATE KEY UPDATE
        tp_company              = VALUES(tp_company),
        latitude                = VALUES(latitude),
        longitude               = VALUES(longitude),
        caf_status              = VALUES(caf_status),
        general_status          = VALUES(general_status),
        start_permit_tp_date    = VALUES(start_permit_tp_date),
        end_permit_tp_date      = VALUES(end_permit_tp_date),
        status_permit_tp        = VALUES(status_permit_tp),
        ticket_batch            = VALUES(ticket_batch),
        site_status             = VALUES(site_status),
        site_issue              = VALUES(site_issue),
        category_issue          = VALUES(category_issue),
        detail_issue            = VALUES(detail_issue),
        remark_dismantle        = VALUES(remark_dismantle),
        mom                     = VALUES(mom),
        partner_company         = VALUES(partner_company),
        plan_dismantle_date     = VALUES(plan_dismantle_date),
        pic_team                = VALUES(pic_team),
        no_handphone            = VALUES(no_handphone),
        plan_dismantle_date_raw = VALUES(plan_dismantle_date_raw),
        updated_at              = NOW();
    
    TRUNCATE TABLE tracker_manual_raw;
    
    COMMIT;
    
    SELECT 'SUCCESS' AS status, 'tracker_manual_raw → tracker_manual_update OK' AS message;
END$$

DELIMITER ;
