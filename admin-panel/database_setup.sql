-- Configuration de la base de données pour le panel admin
-- Exécutez ce script dans votre base de données MySQL

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_parent_id (parent_id),
    INDEX idx_sort_order (sort_order),
    INDEX idx_is_active (is_active)
);

-- Table des produits (si elle n'existe pas déjà)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    media VARCHAR(500),
    description TEXT,
    category_id INT NULL,
    weight VARCHAR(50),
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category_id (category_id)
);

-- Table des prix multiples
CREATE TABLE IF NOT EXISTS product_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'EUR',
    price_type ENUM('regular', 'sale', 'wholesale', 'promotional') DEFAULT 'regular',
    valid_from DATE NULL,
    valid_until DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_currency (product_id, currency),
    INDEX idx_price_type (price_type),
    INDEX idx_valid_dates (valid_from, valid_until)
);

-- Table des utilisateurs admin
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'manager', 'editor') DEFAULT 'editor',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des sessions admin
CREATE TABLE IF NOT EXISTS admin_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id),
    INDEX idx_expires_at (expires_at)
);

-- Table des logs d'activité
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_action (action),
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_created_at (created_at)
);

-- Insertion d'un utilisateur admin par défaut (mot de passe: admin123)
-- Changez le mot de passe après la première connexion !
INSERT INTO admin_users (username, password, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 'admin')
ON DUPLICATE KEY UPDATE id=id;

-- Index pour optimiser les performances
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_products_country ON products(country);
CREATE INDEX idx_products_created_at ON products(created_at);

-- Vue pour les produits avec leurs prix
CREATE OR REPLACE VIEW products_with_prices AS
SELECT 
    p.*,
    pp.price as current_price,
    pp.currency,
    pp.price_type,
    pp.valid_from,
    pp.valid_until
FROM products p
LEFT JOIN product_prices pp ON p.id = pp.product_id 
    AND pp.price_type = 'regular' 
    AND (pp.valid_from IS NULL OR pp.valid_from <= CURDATE())
    AND (pp.valid_until IS NULL OR pp.valid_until >= CURDATE());

-- Procédure pour nettoyer les sessions expirées
DELIMITER //
CREATE PROCEDURE CleanExpiredSessions()
BEGIN
    DELETE FROM admin_sessions WHERE expires_at < NOW();
END //
DELIMITER ;

-- Événement pour nettoyer automatiquement les sessions expirées (toutes les heures)
CREATE EVENT IF NOT EXISTS clean_sessions_event
ON SCHEDULE EVERY 1 HOUR
DO CALL CleanExpiredSessions();

-- Procédure pour obtenir les prix actifs d'un produit
DELIMITER //
CREATE PROCEDURE GetProductPrices(IN product_id INT)
BEGIN
    SELECT 
        pp.*,
        p.product_name
    FROM product_prices pp
    JOIN products p ON pp.product_id = p.id
    WHERE pp.product_id = product_id
    AND (pp.valid_from IS NULL OR pp.valid_from <= CURDATE())
    AND (pp.valid_until IS NULL OR pp.valid_until >= CURDATE())
    ORDER BY pp.currency, pp.price_type;
END //
DELIMITER ;

-- Procédure pour ajouter un prix à un produit
DELIMITER //
CREATE PROCEDURE AddProductPrice(
    IN p_product_id INT,
    IN p_price DECIMAL(10,2),
    IN p_currency VARCHAR(3),
    IN p_price_type ENUM('regular', 'sale', 'wholesale', 'promotional'),
    IN p_valid_from DATE,
    IN p_valid_until DATE
)
BEGIN
    INSERT INTO product_prices (product_id, price, currency, price_type, valid_from, valid_until)
    VALUES (p_product_id, p_price, p_currency, p_price_type, p_valid_from, p_valid_until)
    ON DUPLICATE KEY UPDATE
        price = p_price,
        valid_from = p_valid_from,
        valid_until = p_valid_until,
        updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;