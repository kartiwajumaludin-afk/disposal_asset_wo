DELIMITER $$

DROP PROCEDURE IF EXISTS sp_tracker_business_logic_opt$$

CREATE PROCEDURE sp_tracker_business_logic_opt()
BEGIN
    DECLARE asset_count INT DEFAULT 0;
    DECLARE workinfo_count INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'ERROR' AS status, 'Business logic failed' AS message;
    END;

    START TRANSACTION;

    /* =====================================================
       PRECHECK (INFO ONLY)
    ===================================================== */
    SELECT COUNT(*) INTO asset_count FROM asset_clean;
    SELECT COUNT(*) INTO workinfo_count FROM workinfo_clean;

    /* =====================================================
       A. GENERAL STATUS
    ===================================================== */
    UPDATE tracker
    SET general_status =
        CASE
            WHEN ticket_status_name = 'Cancelled'
                THEN 'Cancelled'
            WHEN ticket_status_name = 'Closed'
                THEN 'Closed'
            WHEN ticket_status_name = 'Waiting PCAA Approval'
                THEN 'Pending PCAA Approved'
            WHEN ticket_status_name = 'Waiting NOP Dismantle Approval'
                THEN 'Pending NOP Approval'
            WHEN ticket_status_name = 'Waiting TO Review'
                THEN 'Pending TO Approval'
            ELSE 'Pending Dismantle'
        END;

    /* =====================================================
       B. WORKABLE STATUS
    ===================================================== */
    UPDATE tracker
    SET workable_status =
        CASE
            WHEN ticket_status_name = 'Cancelled'
                THEN 'Ticket Cancelled'
            WHEN ticket_status_name = 'Waiting TO Review'
                THEN 'Waiting TO Review'
            WHEN ticket_status_name IN (
                'Closed',
                'Waiting PCAA Approval',
                'Waiting NOP Dismantle Approval'
            )
                THEN 'Done Dismantle'
            WHEN site_status = 'Non Workable'
                THEN 'Non Workable'
            WHEN site_status = 'Workable'
                THEN 'Workable'
            ELSE NULL
        END;

    /* =====================================================
       C. PLAN ASSET DISMANTLE (DETAIL)
    ===================================================== */
    UPDATE tracker tr
    JOIN (
        SELECT
            ticket_number,
            CONCAT(
                'PLAN : ',
                GROUP_CONCAT(
                    CONCAT(asset_physical_group_name, ' : ', jumlah)
                    ORDER BY asset_physical_group_name
                    SEPARATOR ', '
                )
            ) AS plan_summary
        FROM (
            SELECT
                ticket_number,
                asset_physical_group_name,
                COUNT(*) AS jumlah
            FROM asset_clean
            GROUP BY ticket_number, asset_physical_group_name
        ) x
        GROUP BY ticket_number
    ) a ON tr.ticket_number = a.ticket_number
    SET tr.plan_asset_dismantle = a.plan_summary;

    /* =====================================================
       D. ACTUAL ASSET DISMANTLE (DETAIL – DISPOSED)
    ===================================================== */
    UPDATE tracker tr
    JOIN (
        SELECT
            ticket_number,
            CONCAT(
                'ACTUAL : ',
                GROUP_CONCAT(
                    CONCAT(asset_physical_group_name, ' : ', jumlah)
                    ORDER BY asset_physical_group_name
                    SEPARATOR ', '
                )
            ) AS actual_summary
        FROM (
            SELECT
                ticket_number,
                asset_physical_group_name,
                COUNT(*) AS jumlah
            FROM asset_clean
            WHERE asset_mflag LIKE '%Disposed%'
            GROUP BY ticket_number, asset_physical_group_name
        ) x
        GROUP BY ticket_number
    ) a ON tr.ticket_number = a.ticket_number
    SET tr.actual_asset_dismantle = a.actual_summary;

    /* =====================================================
       E. ASSET SUMMARY (INIT | ADD | ACC | ACT | %INIT | %ACC)
       TARGET : tracker.percentage_asset_actual
    ===================================================== */
    UPDATE tracker tr
    JOIN (
        SELECT
            ticket_number,
            acc_cnt,
            add_cnt,
            (acc_cnt - add_cnt) AS init_cnt,
            act_cnt,
            CASE
                WHEN (acc_cnt - add_cnt) <= 0 THEN 0
                ELSE FLOOR((act_cnt / (acc_cnt - add_cnt)) * 100)
            END AS pct_init,
            CASE
                WHEN acc_cnt <= 0 THEN 0
                ELSE FLOOR((act_cnt / acc_cnt) * 100)
            END AS pct_acc
        FROM (
            SELECT
                ticket_number,
                COUNT(*) AS acc_cnt,
                SUM(asset_status_name = 'Propose to Write Off') AS add_cnt,
                SUM(asset_mflag LIKE '%Disposed%') AS act_cnt
            FROM asset_clean
            GROUP BY ticket_number
        ) t
    ) x ON tr.ticket_number = x.ticket_number
    SET tr.percentage_asset_actual = CONCAT(
        'INIT : ', x.init_cnt,
        ' | ADD : ', x.add_cnt,
        ' | ACC : ', x.acc_cnt,
        ' | ACT : ', x.act_cnt,
        ' | %INIT : ', x.pct_init, '%',
        ' | %ACC : ', x.pct_acc, '%'
    );

    /* =====================================================
       F. JUMLAH & KATEGORI ASSET
    ===================================================== */
    UPDATE tracker tr
    JOIN (
        SELECT ticket_number, COUNT(*) AS jumlah_asset
        FROM asset_clean
        GROUP BY ticket_number
    ) a ON tr.ticket_number = a.ticket_number
    SET tr.jumlah_asset = a.jumlah_asset;

    UPDATE tracker
    SET cat_asset =
        CASE
            WHEN jumlah_asset <= 5 THEN '<5 Asset'
            WHEN jumlah_asset <= 10 THEN '<10 Asset'
            WHEN jumlah_asset <= 15 THEN '<15 Asset'
            WHEN jumlah_asset <= 20 THEN '<20 Asset'
            ELSE '>20 Asset'
        END
    WHERE jumlah_asset IS NOT NULL;

    /* =====================================================
       H. AGING CALCULATION (SAFE COLUMNS ONLY)
    ===================================================== */
    UPDATE tracker
SET aging_pending_approval =
    CASE
        WHEN ticket_status_name = 'Waiting PCAA Approval'
             AND approve_before IS NOT NULL
            THEN DATEDIFF(CURDATE(), approve_before)

        WHEN ticket_status_name = 'Waiting NOP Approval'
             AND submit_before IS NOT NULL
            THEN DATEDIFF(CURDATE(), submit_before)

        WHEN ticket_status_name = 'Waiting TO Review'
             AND ticket_created_date IS NOT NULL
            THEN DATEDIFF(CURDATE(), ticket_created_date)

        ELSE NULL
    END;


    /* =====================================================
       I. KATEGORI AGING
    ===================================================== */
    UPDATE tracker
    SET cat_pending_approval =
        CASE
            WHEN aging_pending_approval IS NULL THEN NULL
            WHEN aging_pending_approval <= 1 THEN '<=1 Day'
            WHEN aging_pending_approval <= 2 THEN '<=2 Days'
            WHEN aging_pending_approval <= 3 THEN '<=3 Days'
            WHEN aging_pending_approval <= 4 THEN '<=4 Days'
            WHEN aging_pending_approval <= 5 THEN '<=5 Days'
            WHEN aging_pending_approval <= 6 THEN '<=6 Days'
            WHEN aging_pending_approval <= 7 THEN '<=7 Days'
            WHEN aging_pending_approval <= 14 THEN '>1 Week'
            WHEN aging_pending_approval <= 21 THEN '>2 Weeks'
            WHEN aging_pending_approval <= 30 THEN '>3 Weeks'
            ELSE '>1 Month'
        END;

    COMMIT;

    SELECT
        'SUCCESS' AS status,
        CONCAT(
            'Tracker business logic executed. ',
            'Asset rows: ', asset_count,
            ', Workinfo rows: ', workinfo_count
        ) AS message;
END$$

DELIMITER ;
