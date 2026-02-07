DELIMITER $$

CREATE PROCEDURE sp_create_roles()
BEGIN
    CREATE TABLE IF NOT EXISTS roles (
        role_id INT(11) NOT NULL AUTO_INCREMENT,
        role_name VARCHAR(30) NOT NULL,
        PRIMARY KEY (role_id),
        UNIQUE INDEX uk_role_name (role_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$

DELIMITER ;