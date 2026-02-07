DELIMITER $$

DROP PROCEDURE IF EXISTS sp_asset_raw_to_clean $$

CREATE PROCEDURE sp_asset_raw_to_clean()
BEGIN
    INSERT INTO asset_clean (
        ticket_number,
        site_id,
        site_name,
        ticket_sub_type_name,
        ticket_status_name,
        assignee_group,
        assignee,
        ticket_summary,
        ticket_created_date,
        ticket_resolved_date,
        ticket_cleared_date,
        barcode_number,
        part_code,
        part_name,
        brand_name,
        asset_physical_group_name,
        asset_po_number,
        asset_status_name,
        asset_flag_name,
        asset_mflag
    )
    SELECT 
        r.`Ticket Number`,
        r.`Site ID`,
        r.`Site Name`,
        r.`Ticket Sub Type Name`,
        r.`Ticket Status Name`,
        r.`Assignee Group`,
        r.`Assignee`,
        r.`Ticket Summary`,
        STR_TO_DATE(r.`Ticket Created Date`, '%d-%m-%y %H:%i'),
        STR_TO_DATE(r.`Ticket Resolved Date`, '%d-%m-%y %H:%i'),
        STR_TO_DATE(r.`Ticket Cleared Date`, '%d-%m-%y %H:%i'),
        r.`Barcode Number`,
        r.`Part Code`,
        r.`Part Name`,
        r.`Brand Name`,
        r.`Asset Physical Group Name`,
        r.`Asset PO Number`,
        r.`Asset Status Name`,
        r.`Asset Flag Name`,
        r.`Asset mFlag`
    FROM asset_raw r
    WHERE NOT EXISTS (
        SELECT 1 FROM asset_clean c
        WHERE c.barcode_number = r.`Barcode Number`
          AND c.ticket_number = r.`Ticket Number`
    );
END $$

DELIMITER ;
