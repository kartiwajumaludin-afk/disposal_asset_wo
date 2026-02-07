DELIMITER $$
CREATE PROCEDURE sp_create_users()
BEGIN
    CREATE TABLE IF NOT EXISTS users (
        user_id INT(11) NOT NULL AUTO_INCREMENT,
        username VARCHAR(50),
        password_hash VARCHAR(255),
        full_name VARCHAR(100),
        user_role ENUM('SUPER_ADMIN', 'REGIONAL_MANAGER', 'USER'),
        pic_team VARCHAR(100),
        region VARCHAR(100),
        is_active TINYINT(1) DEFAULT 1,
        last_login TIMESTAMP NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id),
        UNIQUE INDEX uk_username (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END$$
DELIMITER ;