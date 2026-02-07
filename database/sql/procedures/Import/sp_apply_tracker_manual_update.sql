DELIMITER $$

DROP PROCEDURE IF EXISTS sp_apply_tracker_manual_update$$

CREATE PROCEDURE sp_apply_tracker_manual_update()
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'ERROR' AS status, 'Apply tracker manual update failed' AS message;
    END;

    START TRANSACTION;

    /* =====================================================
       APPLY MANUAL UPDATE (tracker_manual_update → tracker)
       ===================================================== */
    UPDATE tracker t
    JOIN tracker_manual_update m
        ON m.ticket_number = t.ticket_number
    SET
        t.tp_company            = COALESCE(m.tp_company, t.tp_company),
        t.latitude              = COALESCE(m.latitude, t.latitude),
        t.longitude             = COALESCE(m.longitude, t.longitude),
        t.caf_status            = COALESCE(m.caf_status, t.caf_status),
        t.general_status        = COALESCE(m.general_status, t.general_status),
        t.start_permit_tp_date  = COALESCE(m.start_permit_tp_date, t.start_permit_tp_date),
        t.end_permit_tp_date    = COALESCE(m.end_permit_tp_date, t.end_permit_tp_date),
        t.status_permit_tp      = COALESCE(m.status_permit_tp, t.status_permit_tp),
        t.ticket_batch          = COALESCE(m.ticket_batch, t.ticket_batch),
        t.site_status           = COALESCE(m.site_status, t.site_status),
        t.site_issue            = COALESCE(m.site_issue, t.site_issue),
        t.category_issue        = COALESCE(m.category_issue, t.category_issue),
        t.detail_issue          = COALESCE(m.detail_issue, t.detail_issue),
        t.remark_dismantle      = COALESCE(m.remark_dismantle, t.remark_dismantle),
        t.mom                   = COALESCE(m.mom, t.mom),
        t.partner_company       = COALESCE(m.partner_company, t.partner_company),
        t.plan_dismantle_date   = COALESCE(m.plan_dismantle_date, t.plan_dismantle_date),
        t.pic_team              = COALESCE(m.pic_team, t.pic_team),
        t.no_handphone          = COALESCE(m.no_handphone, t.no_handphone),
        t.updated_at            = CURRENT_TIMESTAMP;

    /* =====================================================
       DERIVED FIELD: PLAN DISMANTLE WEEK (ISO WEEK)
       Format: YYYY-WXX (e.g., 2024-W15)
       ===================================================== */
    UPDATE tracker t
    JOIN tracker_manual_update m
        ON m.ticket_number = t.ticket_number
    SET
        t.plan_dismantle_week =
            CONCAT(
                YEAR(t.plan_dismantle_date),
                '-W',
                LPAD(WEEK(t.plan_dismantle_date, 3), 2, '0')
            )
    WHERE t.plan_dismantle_date IS NOT NULL;

    COMMIT;

    SELECT 'SUCCESS' AS status, 'Manual update applied to tracker successfully' AS message;
END$$

DELIMITER ;
