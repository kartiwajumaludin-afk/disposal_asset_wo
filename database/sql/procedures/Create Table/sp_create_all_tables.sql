DELIMITER $$

-- 1. SP UTAMA: CREATE ALL TABLES
CREATE PROCEDURE sp_create_all_tables()
BEGIN
    -- Asset Tables
    CALL sp_create_asset_clean();
    CALL sp_create_asset_raw();
    CALL sp_create_asset_raw_stg();
    
    -- Ticket Tables
    CALL sp_create_ticket_clean();
    CALL sp_create_ticket_raw();
    CALL sp_create_ticket_raw_stg();
    
    -- Work Info Tables
    CALL sp_create_workinfo_clean();
    CALL sp_create_workinfo_raw();
    CALL sp_create_workinfo_raw_stg();
    
    -- Daily Activity
    CALL sp_create_daily_activity();
    
    -- Tracker Tables
    CALL sp_create_tracker();
    CALL sp_create_tracker_manual_raw();
    CALL sp_create_tracker_manual_update();
    
    -- Master Tables
    CALL sp_create_regions();
    CALL sp_create_region_mapping();
    CALL sp_create_roles();
    CALL sp_create_users();
    
    -- Audit Tables
    CALL sp_create_import_audit();
    CALL sp_create_import_duplicate_log();
    
    SELECT 'All tables created successfully!' AS message;
END$$
DELIMITER ;