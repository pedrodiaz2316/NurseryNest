-- NurseryNest schema (MySQL 8.0+, InnoDB, utf8mb4).
-- Matches "MySQL schema" in the design doc. Run as an administrative user after 00_create_database.sql.

USE nurserynest;

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- Tenancy and identity
-- ---------------------------------------------------------------------------

CREATE TABLE nurseries (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug          VARCHAR(80)  NOT NULL,
    name          VARCHAR(150) NOT NULL,
    address       VARCHAR(255) NOT NULL,
    city          VARCHAR(100) NOT NULL,
    county        VARCHAR(60)  NOT NULL,
    zip           VARCHAR(10)  NOT NULL,
    phone         VARCHAR(30)  NULL,
    email         VARCHAR(255) NULL,
    hours         JSON         NULL,
    description   TEXT         NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nurseries_slug (slug)
) ENGINE=InnoDB;

CREATE TABLE themes (
    id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name              VARCHAR(100) NOT NULL,
    layout            VARCHAR(100) NOT NULL DEFAULT 'classic',
    default_settings  JSON NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_themes_name (name)
) ENGINE=InnoDB;

CREATE TABLE nursery_themes (
    nursery_id       BIGINT UNSIGNED NOT NULL,
    theme_id         BIGINT UNSIGNED NOT NULL,
    logo_path        VARCHAR(255) NULL,
    primary_color    CHAR(7) NOT NULL,
    secondary_color  CHAR(7) NOT NULL,
    banner_path      VARCHAR(255) NULL,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (nursery_id),
    CONSTRAINT fk_nursery_themes_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id),
    CONSTRAINT fk_nursery_themes_theme   FOREIGN KEY (theme_id)   REFERENCES themes (id)
) ENGINE=InnoDB;

CREATE TABLE staff (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nursery_id  BIGINT UNSIGNED NOT NULL,
    name        VARCHAR(150) NOT NULL,
    position    VARCHAR(100) NULL,
    email       VARCHAR(255) NULL,
    phone       VARCHAR(30)  NULL,
    hire_date   DATE NULL,
    PRIMARY KEY (id),
    KEY ix_staff_nursery_name (nursery_id, name),
    CONSTRAINT fk_staff_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id)
) ENGINE=InnoDB;

CREATE TABLE users (
    id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nursery_id     BIGINT UNSIGNED NOT NULL,
    staff_id       BIGINT UNSIGNED NULL,
    email          VARCHAR(255) NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    role           ENUM('nursery_manager','office_admin','inventory_coordinator','sales_rep') NOT NULL,
    is_active      TINYINT(1) NOT NULL DEFAULT 1,
    last_login     DATETIME NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY ix_users_nursery_role (nursery_id, role),
    CONSTRAINT fk_users_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id),
    CONSTRAINT fk_users_staff   FOREIGN KEY (staff_id)   REFERENCES staff (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE clients (
    id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name           VARCHAR(150) NOT NULL,
    email          VARCHAR(255) NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    phone          VARCHAR(30)  NULL,
    is_active      TINYINT(1) NOT NULL DEFAULT 1,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login     DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_clients_email (email)
) ENGINE=InnoDB;

CREATE TABLE client_addresses (
    id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    client_id  BIGINT UNSIGNED NOT NULL,
    label      VARCHAR(50)  NULL,
    address    VARCHAR(255) NOT NULL,
    city       VARCHAR(100) NOT NULL,
    state      CHAR(2)      NOT NULL,
    county     VARCHAR(60)  NULL,
    zip        VARCHAR(10)  NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_client_addresses_client FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE nursery_clients (
    nursery_id  BIGINT UNSIGNED NOT NULL,
    client_id   BIGINT UNSIGNED NOT NULL,
    notes       TEXT NULL,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (nursery_id, client_id),
    CONSTRAINT fk_nursery_clients_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id),
    CONSTRAINT fk_nursery_clients_client  FOREIGN KEY (client_id)  REFERENCES clients (id)
) ENGINE=InnoDB;

CREATE TABLE platform_admins (
    id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name               VARCHAR(150) NOT NULL,
    email              VARCHAR(255) NOT NULL,
    password_hash      VARCHAR(255) NOT NULL,
    two_factor_secret  VARBINARY(255) NULL,
    is_active          TINYINT(1) NOT NULL DEFAULT 1,
    last_login         DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_platform_admins_email (email)
) ENGINE=InnoDB;

CREATE TABLE admin_audit_log (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    admin_id     BIGINT UNSIGNED NOT NULL,
    action       VARCHAR(100) NOT NULL,
    target_type  VARCHAR(50)  NULL,
    target_id    BIGINT UNSIGNED NULL,
    details      JSON NULL,
    ip_address   VARBINARY(16) NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_audit_admin_created (admin_id, created_at),
    KEY ix_audit_target (target_type, target_id),
    CONSTRAINT fk_audit_admin FOREIGN KEY (admin_id) REFERENCES platform_admins (id)
) ENGINE=InnoDB;

CREATE TABLE password_resets (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    account_type  ENUM('user','client','admin') NOT NULL,
    account_id    BIGINT UNSIGNED NOT NULL,
    token_hash    CHAR(64) NOT NULL,
    expires_at    DATETIME NOT NULL,
    used_at       DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_password_resets_token (token_hash),
    KEY ix_password_resets_account (account_type, account_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Catalogue
-- ---------------------------------------------------------------------------

CREATE TABLE inventory_items (
    id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nursery_id     BIGINT UNSIGNED NOT NULL,
    name           VARCHAR(150) NOT NULL,
    category       VARCHAR(80)  NOT NULL,
    genus          VARCHAR(80)  NULL,
    species        VARCHAR(80)  NULL,
    description    TEXT NULL,
    quantity       INT UNSIGNED NOT NULL DEFAULT 0,
    price          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    date_received  DATE NULL,
    florida_only   TINYINT(1) NOT NULL DEFAULT 0,
    is_active      TINYINT(1) NOT NULL DEFAULT 1,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_items_nursery_category (nursery_id, category),
    KEY ix_items_botanical (genus, species),
    FULLTEXT KEY ft_items_search (name, description),
    CONSTRAINT fk_items_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id)
) ENGINE=InnoDB;

CREATE TABLE item_attributes (
    id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    item_id          BIGINT UNSIGNED NOT NULL,
    attribute_name   VARCHAR(80)  NOT NULL,
    attribute_value  VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_item_attributes (item_id, attribute_name),
    CONSTRAINT fk_item_attributes_item FOREIGN KEY (item_id) REFERENCES inventory_items (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Cart, checkout, orders, payments
-- ---------------------------------------------------------------------------

CREATE TABLE carts (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    client_id   BIGINT UNSIGNED NULL,
    session_id  VARCHAR(128) NULL,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_carts_client (client_id),
    UNIQUE KEY uq_carts_session (session_id),
    CONSTRAINT fk_carts_client FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE,
    CONSTRAINT ck_carts_owner CHECK ((client_id IS NULL) <> (session_id IS NULL))
) ENGINE=InnoDB;

CREATE TABLE cart_items (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cart_id     BIGINT UNSIGNED NOT NULL,
    item_id     BIGINT UNSIGNED NOT NULL,
    nursery_id  BIGINT UNSIGNED NOT NULL,
    quantity    INT UNSIGNED NOT NULL,
    added_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cart_items (cart_id, item_id),
    CONSTRAINT fk_cart_items_cart    FOREIGN KEY (cart_id)    REFERENCES carts (id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_items_item    FOREIGN KEY (item_id)    REFERENCES inventory_items (id),
    CONSTRAINT fk_cart_items_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id)
) ENGINE=InnoDB;

CREATE TABLE checkouts (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    client_id   BIGINT UNSIGNED NULL,
    status      ENUM('open','authorizing','completed','cancelled','expired') NOT NULL DEFAULT 'open',
    expires_at  DATETIME NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_checkouts_status_expires (status, expires_at),
    CONSTRAINT fk_checkouts_client FOREIGN KEY (client_id) REFERENCES clients (id)
) ENGINE=InnoDB;

CREATE TABLE stock_reservations (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    checkout_id  BIGINT UNSIGNED NOT NULL,
    item_id      BIGINT UNSIGNED NOT NULL,
    quantity     INT UNSIGNED NOT NULL,
    expires_at   DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY ix_stock_reservations_expires (expires_at),
    CONSTRAINT fk_reservations_checkout FOREIGN KEY (checkout_id) REFERENCES checkouts (id) ON DELETE CASCADE,
    CONSTRAINT fk_reservations_item     FOREIGN KEY (item_id)     REFERENCES inventory_items (id)
) ENGINE=InnoDB;

CREATE TABLE orders (
    id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nursery_id          BIGINT UNSIGNED NOT NULL,
    client_id           BIGINT UNSIGNED NULL,
    checkout_id         BIGINT UNSIGNED NULL,
    guest_name          VARCHAR(150) NULL,
    guest_email         VARCHAR(255) NULL,
    guest_phone         VARCHAR(30)  NULL,
    shipping_address    VARCHAR(255) NULL,
    shipping_city       VARCHAR(100) NULL,
    shipping_zip        VARCHAR(10)  NULL,
    shipping_state      CHAR(2)      NULL,
    shipping_county     VARCHAR(60)  NULL,
    fulfillment_method  ENUM('pickup','delivery','shipping') NOT NULL,
    status              ENUM('pending','paid','ready_for_pickup','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
    subtotal            DECIMAL(10,2) NOT NULL,
    fulfillment_fee     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax                 DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax_rate            DECIMAL(7,5)  NOT NULL DEFAULT 0.00000,
    total               DECIMAL(10,2) NOT NULL,
    guest_access_token  CHAR(64) NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_orders_guest_token (guest_access_token),
    KEY ix_orders_nursery_status (nursery_id, status, created_at),
    KEY ix_orders_client (client_id, created_at),
    KEY ix_orders_nursery_state (nursery_id, shipping_state, created_at),
    CONSTRAINT fk_orders_nursery  FOREIGN KEY (nursery_id)  REFERENCES nurseries (id),
    CONSTRAINT fk_orders_client   FOREIGN KEY (client_id)   REFERENCES clients (id),
    CONSTRAINT fk_orders_checkout FOREIGN KEY (checkout_id) REFERENCES checkouts (id)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id    BIGINT UNSIGNED NOT NULL,
    item_id     BIGINT UNSIGNED NOT NULL,
    item_name   VARCHAR(150) NOT NULL,
    quantity    INT UNSIGNED NOT NULL,
    unit_price  DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_item  FOREIGN KEY (item_id)  REFERENCES inventory_items (id)
) ENGINE=InnoDB;

CREATE TABLE payments (
    id                       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id                 BIGINT UNSIGNED NOT NULL,
    provider                 ENUM('stripe','paypal','square') NOT NULL,
    provider_transaction_id  VARCHAR(255) NOT NULL,
    type                     ENUM('authorization','capture','void','refund') NOT NULL,
    amount                   DECIMAL(10,2) NOT NULL,
    status                   ENUM('pending','succeeded','failed') NOT NULL,
    failure_reason           VARCHAR(255) NULL,
    created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_payments_provider_txn (provider, provider_transaction_id),
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders (id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Nursery settings
-- ---------------------------------------------------------------------------

CREATE TABLE nursery_fulfillment (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nursery_id  BIGINT UNSIGNED NOT NULL,
    method      ENUM('pickup','delivery','shipping') NOT NULL,
    is_enabled  TINYINT(1) NOT NULL DEFAULT 0,
    fee         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    rules       JSON NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nursery_fulfillment (nursery_id, method),
    CONSTRAINT fk_fulfillment_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id)
) ENGINE=InnoDB;

CREATE TABLE nursery_payment_providers (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nursery_id   BIGINT UNSIGNED NOT NULL,
    provider     ENUM('stripe','paypal','square') NOT NULL,
    is_enabled   TINYINT(1) NOT NULL DEFAULT 0,
    account_ref  VARCHAR(255) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nursery_provider (nursery_id, provider),
    CONSTRAINT fk_payment_providers_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id)
) ENGINE=InnoDB;

CREATE TABLE nursery_tax_registrations (
    id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nursery_id           BIGINT UNSIGNED NOT NULL,
    state_code           CHAR(2) NOT NULL,
    registration_number  VARCHAR(50) NULL,
    registered_on        DATE NULL,
    is_active            TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_tax_registration (nursery_id, state_code),
    CONSTRAINT fk_tax_registrations_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Rules (dated rows; no tenant foreign key)
-- ---------------------------------------------------------------------------

CREATE TABLE state_tax_rates (
    id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    state_code       CHAR(2) NOT NULL,
    state_name       VARCHAR(50) NOT NULL,
    rate             DECIMAL(7,5) NOT NULL,
    has_local_taxes  TINYINT(1) NOT NULL DEFAULT 0,
    effective_from   DATE NOT NULL,
    effective_to     DATE NULL,
    notes            VARCHAR(255) NULL,
    PRIMARY KEY (id),
    KEY ix_state_tax_rates (state_code, effective_from)
) ENGINE=InnoDB;

CREATE TABLE local_tax_rates (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    state_code      CHAR(2) NOT NULL,
    jurisdiction    ENUM('county','city','district') NOT NULL,
    name            VARCHAR(100) NOT NULL,
    zip_code        VARCHAR(10) NULL,
    rate            DECIMAL(7,5) NOT NULL,
    effective_from  DATE NOT NULL,
    effective_to    DATE NULL,
    notes           VARCHAR(255) NULL,
    PRIMARY KEY (id),
    KEY ix_local_tax_rates (state_code, jurisdiction, name, effective_from),
    KEY ix_local_tax_rates_zip (zip_code)
) ENGINE=InnoDB;

CREATE TABLE state_nexus_thresholds (
    id                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    state_code             CHAR(2) NOT NULL,
    sales_threshold        DECIMAL(12,2) NULL,
    transaction_threshold  INT UNSIGNED NULL,
    threshold_logic        ENUM('sales','transactions','either','both') NOT NULL,
    period                 ENUM('calendar_year','prior_or_current_year','rolling_12_months') NOT NULL,
    effective_from         DATE NOT NULL,
    effective_to           DATE NULL,
    notes                  VARCHAR(255) NULL,
    PRIMARY KEY (id),
    KEY ix_nexus_thresholds (state_code, effective_from)
) ENGINE=InnoDB;

CREATE TABLE nexus_alerts (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nursery_id  BIGINT UNSIGNED NOT NULL,
    state_code  CHAR(2) NOT NULL,
    year        SMALLINT NOT NULL,
    level       ENUM('80','100') NOT NULL,
    sent_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nexus_alerts (nursery_id, state_code, year, level),
    CONSTRAINT fk_nexus_alerts_nursery FOREIGN KEY (nursery_id) REFERENCES nurseries (id)
) ENGINE=InnoDB;

CREATE TABLE plant_shipping_restrictions (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    state_code      CHAR(2) NULL COMMENT 'NULL = USDA rule for every destination outside Florida',
    genus           VARCHAR(80) NOT NULL,
    species         VARCHAR(80) NULL COMMENT 'NULL = whole genus',
    rule            VARCHAR(255) NOT NULL,
    source          VARCHAR(255) NULL,
    effective_from  DATE NOT NULL,
    effective_to    DATE NULL,
    PRIMARY KEY (id),
    KEY ix_shipping_restrictions (genus, species, state_code)
) ENGINE=InnoDB;

CREATE TABLE app_settings (
    setting_key    VARCHAR(80)  NOT NULL,
    setting_value  VARCHAR(255) NOT NULL,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (setting_key)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Audit log is append-only (ADM-6): block UPDATE and DELETE for every account.
-- ---------------------------------------------------------------------------

DELIMITER $$

CREATE TRIGGER trg_admin_audit_log_no_update
BEFORE UPDATE ON admin_audit_log
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'admin_audit_log is append-only';
END$$

CREATE TRIGGER trg_admin_audit_log_no_delete
BEFORE DELETE ON admin_audit_log
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'admin_audit_log is append-only';
END$$

DELIMITER ;

-- ---------------------------------------------------------------------------
-- Sales by nursery, destination state and year (TAX-1, TAX-5). Pickup counts as FL.
-- ---------------------------------------------------------------------------

CREATE VIEW nursery_state_sales AS
SELECT
    o.nursery_id,
    CASE WHEN o.fulfillment_method = 'pickup' THEN 'FL' ELSE o.shipping_state END AS state_code,
    YEAR(o.created_at)        AS year,
    SUM(o.total - o.tax)      AS total_sales,
    COUNT(*)                  AS order_count,
    SUM(o.tax)                AS tax_collected
FROM orders o
WHERE o.status IN ('paid','ready_for_pickup','shipped','completed')
GROUP BY o.nursery_id, state_code, YEAR(o.created_at);

-- ---------------------------------------------------------------------------
-- Place an order: lock stock rows, check and decrement in one transaction (TECH-3, ORD-4).
-- ---------------------------------------------------------------------------

DELIMITER $$

CREATE PROCEDURE sp_place_order(IN p_order_id BIGINT UNSIGNED)
BEGIN
    DECLARE v_short_item VARCHAR(150) DEFAULT NULL;
    DECLARE v_message VARCHAR(255);
    DECLARE v_locked INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Lock the ordered item rows so no other order can take the same stock.
    SELECT COUNT(*) INTO v_locked
    FROM inventory_items i
    JOIN order_items oi ON oi.item_id = i.id
    WHERE oi.order_id = p_order_id
    FOR UPDATE;

    SELECT oi.item_name INTO v_short_item
    FROM order_items oi
    JOIN inventory_items i ON i.id = oi.item_id
    WHERE oi.order_id = p_order_id
      AND i.quantity < oi.quantity
    LIMIT 1;

    IF v_short_item IS NOT NULL THEN
        SET v_message = CONCAT('Out of stock: ', v_short_item);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_message;
    END IF;

    UPDATE inventory_items i
    JOIN order_items oi ON oi.item_id = i.id
    SET i.quantity = i.quantity - oi.quantity
    WHERE oi.order_id = p_order_id;

    DELETE sr
    FROM stock_reservations sr
    JOIN orders o ON o.checkout_id = sr.checkout_id
    JOIN order_items oi ON oi.order_id = o.id AND oi.item_id = sr.item_id
    WHERE o.id = p_order_id;

    COMMIT;
END$$

DELIMITER ;
