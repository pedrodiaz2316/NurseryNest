# NurseryNest — Project Requirements

**Status:** Draft
**Last updated:** 2026-10-22

## 1. Overview

NurseryNest is a web platform that hosts and manages more than 40 plant nurseries across Florida. Each nursery gets its own account, where it manages its inventory, prices and other business data. The site also has a public marketing website that presents NurseryNest as the best nursery platform in Florida.

## 2. Technology Stack

| Layer      | Technology                    |
|------------|-------------------------------|
| Back end   | PHP                           |
| Front end  | JavaScript, HTML, CSS         |
| Database   | MySQL                         |

- **TECH-1** The application is built mainly in PHP, JavaScript and MySQL.
- **TECH-2** MySQL is the database for the platform. There is no plan to move to another database, so the code does not need to stay database-independent.
- **TECH-3** The project may use MySQL stored procedures for database operations. A good first candidate is placing an order, where stock is checked and reduced in a single transaction (ORD-4).

## 3. Users and Roles

| Role                | Description                                                       |
|---------------------|-------------------------------------------------------------------|
| Visitor             | Anyone browsing the public site who is not logged in.             |
| Nursery user        | A person who logs in to a nursery's account. A nursery can have many users, each with one role: Nursery Manager, Office Administrator, Inventory Coordinator, or Sales and Service Representative (AUTH-5). |
| Nursery staff       | Staff members of a nursery whose records the platform stores.     |
| Client              | A customer with their own NurseryNest account. One account works across every nursery on the platform: clients can browse any nursery, order from any of them and see their past orders. |
| Platform admin      | NurseryNest developers and administrators only. They manage the whole platform and every nursery on it. |

## 4. Functional Requirements

### 4.1 Nursery Management

- **NUR-1** The system stores 40 or more Florida nurseries, and adding more must not require code changes.
- **NUR-2** Each nursery has a profile: name, address/location in Florida, contact details, hours and description.
- **NUR-3** Each nursery's data is kept separate, so a nursery can only see and change its own records.

### 4.2 Inventory and Pricing

- **INV-1** Each nursery manages its own inventory (plants, products and supplies).
- **INV-2** Each inventory item stores at least: name, category, description, quantity in stock and price.
- **INV-3** Nurseries can add, edit and remove inventory items and update prices.
- **INV-4** For each plant, the system records the date it arrived at the nursery, so it can show how long the plant has been there.
- **INV-5** More data fields can be added later without changing the database structure. For example, a nursery can define its own extra fields for an item, such as size, care needs or supplier.
- **INV-6** Each plant records its botanical name (genus and species), so shipping restriction rules can be checked against it (ORD-29).

### 4.3 Authentication and Accounts

- **AUTH-1** Every nursery has its own account, and each account can have multiple users, each with their own login.
- **AUTH-2** Passwords are stored hashed, using PHP `password_hash()` / `password_verify()`.
- **AUTH-3** Users can log out, and sessions expire after a period of inactivity.
- **AUTH-4** Users can reset a forgotten password.
- **AUTH-5** Each user has one role within their nursery, and the role decides what they can see and do. The nursery roles are:
  - **Nursery Manager:** runs the nursery account and has full access to it.
  - **Office Administrator:** handles user accounts, staff records and nursery settings.
  - **Inventory Coordinator:** manages plants, stock and inventory data.
  - **Sales and Service Representative:** handles orders and clients.
- **AUTH-6** The Nursery Manager and the Office Administrator can add, edit, deactivate and remove users and assign their roles. Only a Nursery Manager can create or remove another Nursery Manager.
- **AUTH-7** A user belongs to one nursery and can only access that nursery's data (NUR-3).
- **AUTH-8** Client logins are separate from nursery user logins. AUTH-2 to AUTH-4 apply to client logins as well.
- **AUTH-9** Permissions per role. This is a draft and still needs confirmation:

  | Action                                         | Nursery Manager | Office Administrator | Inventory Coordinator | Sales & Service Rep |
  |------------------------------------------------|:---:|:---:|:---:|:---:|
  | View inventory                                 | ✓ | ✓ | ✓ | ✓ |
  | Add, edit or remove items; update stock        | ✓ |   | ✓ |   |
  | Change prices                                  | ✓ |   |   |   |
  | View orders                                    | ✓ | ✓ | ✓ | ✓ |
  | Update order status                            | ✓ |   | ✓ | ✓ |
  | Cancel orders                                  | ✓ |   |   | ✓ |
  | Issue refunds                                  | ✓ |   |   |   |
  | View clients and edit the nursery's client notes | ✓ | ✓ |   | ✓ |
  | Manage staff records                           | ✓ | ✓ |   |   |
  | Manage nursery users and roles                 | ✓ | ✓ |   |   |
  | Edit nursery profile, pickup/delivery/shipping and payment settings | ✓ | ✓ |   |   |

### 4.4 Per-Nursery Branding

- **BRAND-1** After logging in, each nursery sees its own distinct design.
- **BRAND-2** Branding settings are stored per nursery: logo, color palette and banner/header image at minimum.
- **BRAND-3** Branding is applied by the theme/config system from stored settings, not by separate code for each nursery.
- **BRAND-4** Themes are chosen and set up by NurseryNest developers or platform admins (ADM-1), not by the nurseries. Nursery users cannot change their nursery's theme.
- **BRAND-5** Developers build a library of themes and assign one to each nursery, adjusting its logo, colors and banner for that nursery.

### 4.5 Client and Staff Data

- **DATA-1** The system stores client records in the MySQL database. Client accounts belong to the platform, not to one nursery (see section 4.8).
- **DATA-2** The system stores staff (personnel) records in the MySQL database.
- **DATA-3** Staff records belong to a nursery and follow the isolation rule in NUR-3.
- **DATA-4** A nursery can see only the client details it needs for its own orders: name, contact details and delivery address. It can also keep its own private notes on a client. A nursery never sees a client's orders with other nurseries.

### 4.6 Public Marketing Website

- **WEB-1** **Home page:** a standard landing page explaining why NurseryNest is the best nursery platform in Florida.
- **WEB-2** **About Us:** a section about the company and its mission.
- **WEB-3** **Our Specialty:** a section on what NurseryNest specializes in and what sets it apart.
- **WEB-4** More sections explaining NurseryNest in detail (for example features, partner nurseries and contact).
- **WEB-5** The public site links to the nursery login and to client sign-up and login.
- **WEB-6** Each nursery has a public page that shows its profile and inventory. Anyone can view it, even without logging in.
- **WEB-7** Seeing all nurseries together, for example browsing or searching inventory across every nursery at once, requires a client login (CLI-3). Visitors who aren't logged in are asked to sign up or log in.

### 4.7 Online Ordering and Payment

- **ORD-1** Clients can browse a nursery's available items and add them to a cart.
- **ORD-2** Clients can place an order with a nursery and pay online.
- **ORD-3** Each order belongs to one nursery and follows the isolation rule in NUR-3.
- **ORD-4** Stock goes down automatically when an order is placed, and a client cannot order more than is in stock.
- **ORD-5** Nurseries can view and manage their orders and update each order's status (for example pending, paid, ready for pickup, shipped, completed, cancelled).
- **ORD-6** Clients receive an order confirmation, for example by email.
- **ORD-7** Payments go through third-party payment providers, and the platform supports Stripe, PayPal and Square. NurseryNest never stores card numbers.
- **ORD-8** Nurseries can issue refunds and cancel orders.
- **ORD-9** Each nursery chooses which fulfillment methods it offers: pickup, local delivery, shipping, or any mix of these.
- **ORD-10** At checkout, clients can only pick from the methods their nursery offers.
- **ORD-11** Each nursery sets its own rules for the methods it offers, such as delivery area, delivery or shipping fees, and pickup hours.
- **ORD-12** Each nursery chooses which payment providers to enable (Stripe, PayPal, Square, or any mix) and connects its own provider accounts. Clients see only the providers their nursery has enabled.
- **ORD-13** All providers sit behind one common payment interface in the code, so the rest of the system works the same way whichever provider is used, and new providers can be added later.
- **ORD-14** Sales tax is calculated at checkout, after the client chooses a fulfillment method.
- **ORD-15** **Delivery or shipping:** tax is calculated from the client's shipping address. Inside Florida, it is the state rate plus that county's surtax. Outside Florida, the destination state's tax rules and rates apply, including local rates, but only when the nursery is registered to collect tax there (TAX-4).
- **ORD-16** **Pickup:** tax is calculated on the final order price, using the rate for the nursery's own location.
- **ORD-17** The client sees the tax amount before paying, and it is stored on the order (`orders.tax`).
- **ORD-18** Tax rates are kept in our own MySQL tables, not hard-coded: `state_tax_rates` for each state's base rate, and `local_tax_rates` for county, city and district taxes. Rates are dated, so when a state or local rate changes, a new row is added instead of overwriting the old one. Past orders keep the rate that applied when they were placed.
- **ORD-19** Nurseries can ship to addresses outside Florida.
- **ORD-20** A client's payment goes straight into the nursery's own provider account. NurseryNest does not hold or pass on any of the money, and takes no fee at checkout. Refunds (ORD-8) are also paid from the nursery's own account.
- **ORD-21** A logged-in client's cart can hold items from more than one nursery at the same time.
- **ORD-22** At checkout, a cart with several nurseries is split into one order per nursery (ORD-3). For each order, the client picks one of that nursery's fulfillment methods, sees that order's tax, and pays through that nursery's own provider account (ORD-20). The checkout page shows each order's total and the overall total.
- **ORD-23** A visitor who is not logged in can only have items from one nursery in their cart. Adding an item from a second nursery asks them to log in or sign up. This matches WEB-7.
- **ORD-24** A logged-in client's cart is saved to their account, so it stays the same on every device and after logging out.
- **ORD-25** Clients can check out as guests, without an account, but only with items from one nursery (ORD-23).
- **ORD-26** At checkout, a guest gives their name, email, phone and, for delivery or shipping, their address. The order confirmation (ORD-6) goes to that email, with a link the guest can use to check the order's status.
- **ORD-27** After a guest checkout, the client is offered the option to create an account. If they do, the guest order is added to their order history (CLI-5).
- **ORD-28** All plants can be shipped to addresses inside Florida.
- **ORD-29** Plants shipped outside Florida are checked against the destination state's rules and USDA rules. The system blocks any plant that can't legally be shipped there, for example citrus.
- **ORD-30** If a cart has a plant that can't be shipped to the chosen address, checkout says which item it is and why. The client can remove the item, or switch that nursery's order to pickup if the nursery offers it. A restricted plant is never shipped.
- **ORD-31** Shipping restriction rules are kept as data, not hard-coded. Platform admins maintain them, since state and USDA rules change.
- **ORD-32** A nursery can mark any item as "Florida only", even when no rule requires it.
- **ORD-33** In a checkout with several nurseries, each payment is first only authorized, meaning the money is held but not taken. No order is placed and no money is taken until every payment has been authorized, or until the client decides what to do (ORD-35). Stock for the items is reserved during this time.
- **ORD-34** If any payment fails, the whole checkout stops and the client is not charged for any order. The failed nursery's order is shown in red with the reason, for example "card declined". The other orders are shown as ready to go through.
- **ORD-35** The client then chooses one of these:
  - **Try again:** retry the failed order, for example with a different payment method or provider.
  - **Remove the failed order and continue:** the page tells the client that if they remove the failed order, the rest of the checkout will go through. The remaining orders are then placed and paid.
  - **Cancel the whole checkout:** all held payments are released, no orders are placed, and the items stay in the cart.
- **ORD-36** If the client leaves without choosing, the held payments and reserved stock are released after a set time, for example 15 minutes, and the items stay in the cart.

### 4.8 Client Accounts

- **CLI-1** Clients can create their own NurseryNest account and log in.
- **CLI-2** One client account works across every nursery on the platform.
- **CLI-3** Logged-in clients can browse and search all nurseries and their items together in one combined view. Anyone can still see a single nursery's public page (WEB-6).
- **CLI-4** Clients can order from any nursery using the same account.
- **CLI-5** Clients can see their full order history across every nursery, including each order's status and details.
- **CLI-6** Clients can manage their profile: name, contact details and saved delivery addresses.

### 4.9 Sales Tax Tracking

- **TAX-1** The system tracks each nursery's sales into every state: total sales amount and number of orders, by calendar year, based on the shipping address.
- **TAX-2** Each state's threshold for when an out-of-state seller must start collecting its tax is stored as data: a sales amount, a number of orders, or both, plus the period it covers. This is the "economic nexus" threshold.
- **TAX-3** The Nursery Manager, Office Administrator and platform admins are warned when a nursery gets close to a state's threshold, for example at 80%, and again when it passes the threshold.
- **TAX-4** Each nursery records the states where it is registered to collect sales tax. Tax for an out-of-state address (ORD-15) is charged only when the nursery is registered in that state. Florida tax is always charged.
- **TAX-5** Nurseries can run tax reports showing tax collected by state, county and period, to help with filing returns.

### 4.10 Platform Administration

- **ADM-1** There is a platform-admin role, and only NurseryNest developers and administrators can hold it. Nursery users and clients can never be given it.
- **ADM-2** Platform admins can manage all nurseries: add new nurseries, edit them, and deactivate or remove them.
- **ADM-3** Platform admins can view and manage data across all nurseries for support and maintenance. This is the only exception to the isolation rule in NUR-3.
- **ADM-4** Platform admins can manage nursery user accounts and client accounts, for example to unlock an account or reset access.
- **ADM-5** Platform admins log in through a separate admin area, with its own login and stronger security such as two-factor authentication.
- **ADM-6** Every action a platform admin takes is recorded in an audit log.

## 5. Non-Functional Requirements

- **NFR-1 Security:** use prepared statements (PDO/MySQLi) for every query, escape all output, add CSRF protection on forms and serve the site over HTTPS only.
- **NFR-2 Privacy:** client and staff personal data is protected and never exposed across nurseries.
- **NFR-3 Responsive design:** the public site and nursery dashboards work on desktop, tablet and mobile.
- **NFR-4 Scalability:** the platform handles growth well beyond 40 nurseries without a redesign.
- **NFR-5 Maintainability:** shared code serves all nurseries, and differences between nurseries come from configuration and data.
- **NFR-6 Backups:** the database is backed up regularly.
- **NFR-7 Payment security:** card data is handled only by the payment provider (hosted checkout or tokenization), which keeps PCI DSS compliance as simple as possible.
- **NFR-8 Supported versions and patching:** the platform runs only on PHP and MySQL versions that still receive security fixes (PHP 8.3 or newer at launch). Critical security patches are applied within 48 hours of release.
- **NFR-9 Server setup:** PHP runs through PHP-FPM behind Nginx or Apache, not as an Apache module (mod_php). A web application firewall sits in front of the site so known attacks can be blocked even before a patch is applied.
- **NFR-10 Least privilege:** the PHP process runs as a low-privilege system user. The application's MySQL account has only the rights it needs and is never the root account. Payment provider credentials are kept encrypted or outside the database (see `nursery_payment_providers`).
- **NFR-11 Dependency monitoring:** PHP, MySQL and all third-party packages (for example Composer packages) are watched for security advisories, for example with `composer audit` or automated alerts.
- **NFR-12 Incident recovery:** backups (NFR-6) are test-restored regularly. A written incident plan covers what to do if the server is compromised, including how to restore service and how to notify affected nurseries and clients.

## 6. High-Level Data Model (Initial)

```
nurseries        (id, name, address, city, phone, email, hours, description, created_at)
themes           (id, name, layout, default_settings)   -- theme library built by developers (BRAND-5)
nursery_themes   (nursery_id, theme_id, logo_path, primary_color, secondary_color, banner_path)   -- set by developers/admins only (BRAND-4)
users            (id, nursery_id, staff_id, email, password_hash, role, is_active, last_login)   -- many users per nursery (AUTH-1); staff_id is optional; role: nursery_manager | office_admin | inventory_coordinator | sales_rep (AUTH-5)
inventory_items  (id, nursery_id, name, category, genus, species, description, quantity, price, date_received, florida_only, updated_at)
item_attributes  (id, item_id, attribute_name, attribute_value)   -- extra fields added later (INV-5)
plant_shipping_restrictions (id, state_code, genus, species, rule, source, effective_from, effective_to)   -- maintained by platform admins (ORD-31)
clients          (id, name, email, password_hash, phone, created_at, last_login)   -- one account across the platform (CLI-2)
client_addresses (id, client_id, label, address, city, zip)
nursery_clients  (nursery_id, client_id, notes)   -- each nursery's private notes on a client (DATA-4)
platform_admins  (id, name, email, password_hash, two_factor_secret, is_active, last_login)   -- developers/admins only (ADM-1)
admin_audit_log  (id, admin_id, action, target_type, target_id, details, created_at)   -- ADM-6
staff            (id, nursery_id, name, position, email, phone, hire_date)
nursery_fulfillment (id, nursery_id, method, is_enabled, fee, rules)   -- method: pickup | delivery | shipping (ORD-9)
orders           (id, nursery_id, client_id, checkout_id, guest_name, guest_email, guest_phone, shipping_address, shipping_state, shipping_county, fulfillment_method, status, subtotal, fulfillment_fee, tax, total, guest_access_token, created_at)   -- client_id is NULL for guest orders (ORD-25)
order_items      (id, order_id, item_id, quantity, unit_price)
carts            (id, client_id, session_id, updated_at)   -- client_id for logged-in clients (ORD-24), session_id for visitors
cart_items       (id, cart_id, item_id, nursery_id, quantity, added_at)   -- may span nurseries when logged in (ORD-21)
checkouts        (id, client_id, created_at)   -- groups the orders created from one multi-nursery checkout (ORD-22)
nursery_payment_providers (id, nursery_id, provider, is_enabled, account_ref)   -- provider: stripe | paypal | square (ORD-12); credentials kept encrypted or outside the DB
state_tax_rates  (id, state_code, state_name, rate, has_local_taxes, effective_from, effective_to, notes)   -- ORD-18
local_tax_rates  (id, state_code, jurisdiction, name, zip_code, rate, effective_from, effective_to, notes)   -- county/city/district surtaxes
state_nexus_thresholds (id, state_code, sales_threshold, transaction_threshold, threshold_logic, period, effective_from, effective_to, notes)   -- TAX-2
nursery_tax_registrations (id, nursery_id, state_code, registration_number, registered_on, is_active)   -- TAX-4
nursery_state_sales (view over orders: nursery_id, state_code, year, total_sales, order_count, tax_collected)   -- TAX-1, TAX-5
payments         (id, order_id, provider, provider_transaction_id, amount, status, created_at)
```

## 7. Open Questions

1. ~~What counts as "other important nursery data" beyond inventory and prices?~~ **Answered:** the plants in each nursery, their prices and how long each plant has been at the nursery, with more fields to be added later. See INV-4 and INV-5.
2. ~~Do clients log in themselves (for example to browse or order), or are they only records the nursery keeps?~~ **Answered:** clients log in with one platform-wide account. They can see their past orders, browse other nurseries and order from them. See section 4.8.
3. ~~Will there be a platform-admin role at NurseryNest that manages all nurseries?~~ **Answered:** yes, but only for NurseryNest developers and admins. See section 4.10.
4. ~~Does each nursery have one login, or several users (owner, staff) per nursery?~~ **Answered:** several users per nursery. See AUTH-1 and AUTH-5 to AUTH-7.
5. ~~Should the public site show nursery listings or inventory, or is inventory private to each nursery?~~ **Answered:** each nursery's inventory is public on its own page, but seeing all nurseries together requires a login. See WEB-6, WEB-7 and CLI-3.
6. ~~How much can each nursery customize: preset themes, or full custom colors, logos and layouts?~~ **Answered:** developers choose and set up the themes, and nurseries do not customize them. See BRAND-4 and BRAND-5.
7. ~~Is online ordering or payment in scope?~~ **Answered:** yes. See section 4.7.
8. ~~Which payment provider should we use: Stripe, PayPal, Square or another?~~ **Answered:** all three: Stripe, PayPal and Square. See ORD-7, ORD-12 and ORD-13.
9. ~~How do clients get their orders: pickup, delivery, shipping, or a mix chosen by each nursery?~~ **Answered:** a mix chosen by each nursery. See ORD-9 to ORD-11.
10. ~~How should Florida sales tax be calculated?~~ **Answered:** for delivery or shipping, from the client's address; for pickup, on the final price at the nursery's rate. See ORD-14 to ORD-18.
11. ~~What are the exact roles and permissions inside a nursery?~~ **Answered:** the roles are Nursery Manager, Office Administrator, Inventory Coordinator, and Sales and Service Representative. See AUTH-5 and AUTH-6. *Still to confirm:* the draft permissions in AUTH-9.
12. ~~Does payment go straight to each nursery's own provider account, or through NurseryNest first (for example, with NurseryNest taking a fee)?~~ **Answered:** straight to the nursery's account. See ORD-20.
13. ~~Can a client's cart hold items from several nurseries at once, which would be split into separate orders and payments at checkout? Or is there one cart per nursery?~~ **Answered:** yes for logged-in clients. Visitors who aren't logged in are limited to one nursery. See ORD-21 to ORD-24.
14. ~~Can clients check out as guests without an account, or must they sign up first?~~ **Answered:** guests can check out, but only for one nursery. See ORD-25 to ORD-27.
15. ~~Do nurseries ship outside Florida? If so, how is tax handled for out-of-state addresses?~~ **Answered:** probably yes. Tax follows the destination state's rules. See ORD-15 and ORD-19.
16. Are delivery and shipping fees taxed too, or only the items? Confirm with an accountant. *Deferred:* to be decided later in the project. Until then, the tax calculation should have a setting to include or exclude fees, so the decision doesn't need code changes.
17. ~~Should we keep our own table of Florida county tax rates, or use a tax service such as Stripe Tax, TaxJar or Avalara?~~ **Answered:** our own SQL tables with every state's tax rate. See ORD-18. *Still to do:* fill in `local_tax_rates`, starting with Florida's 67 county surtaxes, and decide who keeps the rates up to date.
18. ~~A nursery may only have to collect another state's tax once its sales into that state pass that state's limit. Should we track sales per nursery per state, or let a tax service handle it?~~ **Answered:** the system tracks sales per nursery per state itself. See section 4.9 (TAX-1 to TAX-5). *Still to do:* fill in each state's threshold in `state_nexus_thresholds`, checked by an accountant.
19. ~~Some states and USDA rules restrict which live plants can be shipped in from Florida, for example citrus. Should the system block plants that can't legally be shipped to the client's state?~~ **Answered:** yes. All plants can ship within Florida, and shipping outside Florida is restricted by the rules. See ORD-28 to ORD-32 and INV-6. *Still to do:* collect the actual restriction rules from USDA APHIS and each state's agriculture department.
20. ~~In a checkout with several nurseries, what happens if one payment fails, for example if a nursery's provider declines the card? Do the other orders still go through, or does the whole checkout stop?~~ **Answered:** the whole checkout stops, and the failed order is shown in red. The client can retry it, remove it so the rest goes through, or cancel everything. See ORD-33 to ORD-36.
21. Who is responsible for applying security patches within the NFR-8 time limit, and who owns the incident plan in NFR-12? This depends on the hosting choice (managed hosting or our own server).
