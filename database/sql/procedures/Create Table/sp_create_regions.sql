DELIMITER $$
CREATE PROCEDURE sp_create_regions()
BEGIN
    CREATE TABLE IF NOT EXISTS regions (
        region_id INT(11) NOT NULL AUTO_INCREMENT,
        region_name VARCHAR(50),
        PRIMARY KEY (region_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;