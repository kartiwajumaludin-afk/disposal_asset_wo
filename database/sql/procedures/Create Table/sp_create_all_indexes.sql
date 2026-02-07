DELIMITER $$

CREATE PROCEDURE sp_create_all_indexes()
BEGIN
CREATE INDEX idx_ticket_raw_ticket ON ticket_raw(`Ticket Number`);

CREATE INDEX idx_asset_raw_ticket ON asset_raw(`Ticket Number`);
CREATE INDEX idx_asset_raw_barcode ON asset_raw(`Barcode Number`);

CREATE INDEX idx_workinfo_raw_ticket ON workinfo_raw(`Ticket Number`);
CREATE INDEX idx_workinfo_raw_status ON workinfo_raw(`Work Info Status Name`);
CREATE INDEX idx_workinfo_raw_date   ON workinfo_raw(`Work Info Updated Date`);
CREATE UNIQUE INDEX uq_workinfo_guard
ON workinfo_raw(`Ticket Number`,`Work Info Status Name`,`Work Info Updated Date`);

CREATE INDEX idx_ticket_clean_ticket ON ticket_clean(ticket_number);
CREATE INDEX idx_ticket_clean_region   ON ticket_clean(regional);
CREATE INDEX idx_ticket_clean_site     ON ticket_clean(site_id);
CREATE INDEX idx_ticket_clean_summary  ON ticket_clean(ticket_summary);
CREATE INDEX idx_ticket_clean_created  ON ticket_clean(ticket_created_date);


CREATE INDEX idx_asset_clean_ticket ON asset_clean(ticket_number);
CREATE INDEX idx_asset_clean_barcode ON asset_clean(barcode_number);
CREATE INDEX idx_asset_clean_created   ON asset_clean(ticket_created_date);
CREATE INDEX idx_asset_clean_group     ON asset_clean(asset_physical_group_name);
CREATE INDEX idx_asset_clean_mflag     ON asset_clean(asset_mflag);


CREATE INDEX idx_workinfo_clean_ticket ON workinfo_clean(ticket_number);
CREATE INDEX idx_workinfo_clean_status ON workinfo_clean(work_info_status_name);
CREATE INDEX idx_workinfo_clean_date   ON workinfo_clean(work_info_updated_date);
CREATE INDEX idx_workinfo_clean_updated ON workinfo_clean(work_info_updated_date);


CREATE UNIQUE INDEX uq_tracker_ticket ON tracker(ticket_number);
CREATE INDEX idx_tracker_site ON tracker(site_id);
CREATE INDEX idx_tracker_status_raw ON tracker(ticket_status_name);
CREATE INDEX idx_tracker_permit ON tracker(working_permit_status_name);
CREATE INDEX idx_tracker_pending ON tracker(cat_pending_approval);
CREATE INDEX idx_tracker_plan_week ON tracker(plan_dismantle_week);




CREATE INDEX idx_daily_activity_ticket ON daily_activity(ticket_number);
CREATE INDEX idx_daily_activity_plan_date ON daily_activity(plan_dismantle_date);

END$$
DELIMITER ;