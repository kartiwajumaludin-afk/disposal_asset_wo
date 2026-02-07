DELIMITER $$
CREATE PROCEDURE sp_create_region_mapping()
BEGIN
    CREATE TABLE IF NOT EXISTS region_mapping (
        mapping_id INT(11) NOT NULL AUTO_INCREMENT,
        assignee_group VARCHAR(150) NOT NULL,
        region_id INT(11) NOT NULL,
        PRIMARY KEY (mapping_id),
        UNIQUE INDEX uk_assignee_group (assignee_group),
        INDEX idx_region_id (region_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;