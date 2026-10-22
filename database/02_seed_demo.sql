-- NurseryNest demo data for local development. Do not load in production.
-- Every demo login uses the password: DemoPass!2026
-- Tax rates and shipping rules here are SAMPLES for testing; verify them before real use.

USE nurserynest;

SET NAMES utf8mb4;

-- Theme library (BRAND-5)
INSERT INTO themes (id, name, layout, default_settings) VALUES
(1, 'Fern',       'classic', JSON_OBJECT('primary', '#2f6b3b', 'secondary', '#d9ead3', 'font', 'serif')),
(2, 'Terracotta', 'classic', JSON_OBJECT('primary', '#b5522e', 'secondary', '#f6e3d6', 'font', 'serif')),
(3, 'Coastal',    'classic', JSON_OBJECT('primary', '#1f6f8b', 'secondary', '#dcf0f4', 'font', 'sans'));

-- Nurseries (fictional)
INSERT INTO nurseries (id, slug, name, address, city, county, zip, phone, email, hours, description) VALUES
(1, 'sunshine-palms', 'Sunshine Palms Nursery', '1200 Orange Blossom Trail', 'Orlando', 'Orange', '32805',
    '(407) 555-0142', 'hello@sunshinepalms.test',
    JSON_OBJECT('Mon-Fri', '8:00-17:00', 'Sat', '8:00-15:00', 'Sun', 'Closed'),
    'Family-run since 1987. Palms, tropicals and hardy Central Florida shrubs.'),
(2, 'gulf-coast-gardens', 'Gulf Coast Gardens', '845 Bayshore Road', 'Tampa', 'Hillsborough', '33606',
    '(813) 555-0187', 'info@gulfcoastgardens.test',
    JSON_OBJECT('Mon-Sat', '9:00-18:00', 'Sun', '10:00-16:00'),
    'Coastal landscaping plants, salt-tolerant natives and container gardens.'),
(3, 'everglades-natives', 'Everglades Native Plants', '30 Krome Avenue', 'Homestead', 'Miami-Dade', '33030',
    '(305) 555-0119', 'grow@evergladesnatives.test',
    JSON_OBJECT('Tue-Sat', '8:00-16:00', 'Sun-Mon', 'Closed'),
    'South Florida natives that feed pollinators and need little water.');

INSERT INTO nursery_themes (nursery_id, theme_id, logo_path, primary_color, secondary_color, banner_path) VALUES
(1, 1, NULL, '#2f6b3b', '#e4f0df', NULL),
(2, 3, NULL, '#1f6f8b', '#dcedf3', NULL),
(3, 2, NULL, '#a5492a', '#f6e6da', NULL);

-- Staff and nursery users (AUTH-5)
INSERT INTO staff (id, nursery_id, name, position, email, phone, hire_date) VALUES
(1, 1, 'Maria Lopez',  'General Manager', 'manager@sunshinepalms.test',  '(407) 555-0150', '2015-03-02'),
(2, 1, 'James Carter', 'Inventory Lead',  'inventory@sunshinepalms.test', '(407) 555-0151', '2019-06-17'),
(3, 1, 'Ana Ruiz',     'Sales Associate', 'sales@sunshinepalms.test',    '(407) 555-0152', '2022-01-10'),
(4, 2, 'Dev Patel',    'Owner',           'manager@gulfcoastgardens.test', '(813) 555-0190', '2012-09-01'),
(5, 3, 'Grace Kim',    'Owner',           'manager@evergladesnatives.test', '(305) 555-0120', '2018-02-14');

INSERT INTO users (nursery_id, staff_id, email, password_hash, role) VALUES
(1, 1, 'manager@sunshinepalms.test',     '$2y$10$c/0rqm7TaML7UzqG0tHp3OrWEy.RWD/sEhZeRUKymNyVBKxj5rfO6', 'nursery_manager'),
(1, NULL, 'office@sunshinepalms.test',   '$2y$10$c/0rqm7TaML7UzqG0tHp3OrWEy.RWD/sEhZeRUKymNyVBKxj5rfO6', 'office_admin'),
(1, 2, 'inventory@sunshinepalms.test',   '$2y$10$c/0rqm7TaML7UzqG0tHp3OrWEy.RWD/sEhZeRUKymNyVBKxj5rfO6', 'inventory_coordinator'),
(1, 3, 'sales@sunshinepalms.test',       '$2y$10$c/0rqm7TaML7UzqG0tHp3OrWEy.RWD/sEhZeRUKymNyVBKxj5rfO6', 'sales_rep'),
(2, 4, 'manager@gulfcoastgardens.test',  '$2y$10$c/0rqm7TaML7UzqG0tHp3OrWEy.RWD/sEhZeRUKymNyVBKxj5rfO6', 'nursery_manager'),
(3, 5, 'manager@evergladesnatives.test', '$2y$10$c/0rqm7TaML7UzqG0tHp3OrWEy.RWD/sEhZeRUKymNyVBKxj5rfO6', 'nursery_manager');

-- A demo client account (CLI-1)
INSERT INTO clients (id, name, email, password_hash, phone) VALUES
(1, 'Sam Rivera', 'client@example.test', '$2y$10$c/0rqm7TaML7UzqG0tHp3OrWEy.RWD/sEhZeRUKymNyVBKxj5rfO6', '(407) 555-0177');

INSERT INTO client_addresses (client_id, label, address, city, state, county, zip) VALUES
(1, 'Home', '88 Lakeview Drive', 'Winter Park', 'FL', 'Orange', '32789');

-- Inventory (INV-2, INV-4, INV-6)
INSERT INTO inventory_items (nursery_id, name, category, genus, species, description, quantity, price, date_received, florida_only) VALUES
(1, 'Foxtail Palm (3 gal)',        'Palms',      'Wodyetia',     'bifurcata',    'Fast-growing palm with plume-like fronds.',               24,  39.99, '2026-08-14', 0),
(1, 'Pygmy Date Palm (7 gal)',     'Palms',      'Phoenix',      'roebelenii',   'Compact palm for patios and entryways.',                  12,  74.50, '2026-07-02', 0),
(1, 'Bird of Paradise (3 gal)',    'Tropicals',  'Strelitzia',   'reginae',      'Orange and blue flowers, full sun.',                      18,  29.99, '2026-09-20', 0),
(1, 'Meyer Lemon Tree (5 gal)',    'Fruit Trees','Citrus',       'meyeri',       'Sweet lemons on a compact tree. Ships within Florida only.', 9, 59.00, '2026-06-11', 1),
(1, 'Croton Petra (1 gal)',        'Shrubs',     'Codiaeum',     'variegatum',   'Bold red, orange and yellow foliage.',                    40,   9.99, '2026-10-01', 0),
(1, 'Organic Potting Mix (1 cu ft)','Supplies',  NULL,           NULL,           'Peat-free mix for containers.',                           60,  12.49, NULL,         0),
(2, 'Seagrape (3 gal)',            'Natives',    'Coccoloba',    'uvifera',      'Salt-tolerant coastal shrub with round leaves.',          30,  27.00, '2026-09-05', 0),
(2, 'Muhly Grass (1 gal)',         'Grasses',    'Muhlenbergia', 'capillaris',   'Pink fall plumes, drought tolerant.',                     55,   8.50, '2026-08-28', 0),
(2, 'Key Lime Tree (3 gal)',       'Fruit Trees','Citrus',       'aurantiifolia','Classic Florida lime.',                                   10,  45.00, '2026-07-19', 1),
(2, 'Ceramic Planter, 14 in',      'Supplies',   NULL,           NULL,           'Glazed planter with drainage hole.',                      16,  34.99, NULL,         0),
(3, 'Firebush (3 gal)',            'Natives',    'Hamelia',      'patens',       'Red tubular flowers loved by hummingbirds.',              35,  19.99, '2026-09-30', 0),
(3, 'Coontie (1 gal)',             'Natives',    'Zamia',        'integrifolia', 'Native cycad and host plant for the Atala butterfly.',    42,  14.99, '2026-08-08', 0),
(3, 'Beautyberry (3 gal)',         'Natives',    'Callicarpa',   'americana',    'Purple berry clusters in fall.',                          20,  22.00, '2026-10-12', 0);

INSERT INTO item_attributes (item_id, attribute_name, attribute_value) VALUES
(1, 'Mature height', '20-30 ft'),
(1, 'Light', 'Full sun'),
(3, 'Light', 'Full sun to part shade'),
(11, 'Attracts', 'Hummingbirds, butterflies');

-- Fulfillment options (ORD-9, ORD-11)
INSERT INTO nursery_fulfillment (nursery_id, method, is_enabled, fee, rules) VALUES
(1, 'pickup',   1,  0.00, JSON_OBJECT('hours', 'Mon-Sat 8:00-15:00')),
(1, 'delivery', 1, 25.00, JSON_OBJECT('zips', JSON_ARRAY('32801','32803','32804','32805','32789'))),
(1, 'shipping', 1, 14.95, NULL),
(2, 'pickup',   1,  0.00, NULL),
(2, 'delivery', 1, 30.00, JSON_OBJECT('radius_miles', 20)),
(3, 'pickup',   1,  0.00, NULL);

-- Sample tax data (ORD-18). Verify before real use.
INSERT INTO state_tax_rates (state_code, state_name, rate, has_local_taxes, effective_from, notes) VALUES
('FL', 'Florida', 0.06000, 1, '2000-01-01', 'Sample row');

INSERT INTO local_tax_rates (state_code, jurisdiction, name, rate, effective_from, notes) VALUES
('FL', 'county', 'Orange',       0.00500, '2000-01-01', 'Sample row'),
('FL', 'county', 'Hillsborough', 0.01500, '2000-01-01', 'Sample row'),
('FL', 'county', 'Miami-Dade',   0.01000, '2000-01-01', 'Sample row');

-- Sample shipping rule (ORD-29, ORD-31). Verify before real use.
INSERT INTO plant_shipping_restrictions (state_code, genus, species, rule, source, effective_from) VALUES
(NULL, 'Citrus', NULL, 'Citrus plants cannot be shipped out of Florida (USDA citrus quarantine).', 'USDA APHIS', '2000-01-01');

-- Open question 16: fees are not taxed until confirmed.
INSERT INTO app_settings (setting_key, setting_value) VALUES
('tax_fulfillment_fees', '0');
