-- Add indexes to improve query performance

-- Users table indexes
ALTER TABLE users ADD INDEX idx_users_email (email);
ALTER TABLE users ADD INDEX idx_users_username (username);
ALTER TABLE users ADD INDEX idx_users_role (role);
ALTER TABLE users ADD INDEX idx_users_status (status);

-- Products table indexes
ALTER TABLE products ADD INDEX idx_products_category_id (category_id);
ALTER TABLE products ADD INDEX idx_products_status (status);
ALTER TABLE products ADD INDEX idx_products_created_at (created_at);

-- Orders table indexes
ALTER TABLE orders ADD INDEX idx_orders_user_id (user_id);
ALTER TABLE orders ADD INDEX idx_orders_status (status);
ALTER TABLE orders ADD INDEX idx_orders_created_at (created_at);

-- Order items table indexes
ALTER TABLE order_items ADD INDEX idx_order_items_order_id (order_id);
ALTER TABLE order_items ADD INDEX idx_order_items_product_id (product_id);

-- Cart items table indexes
ALTER TABLE cart_items ADD INDEX idx_cart_items_user_id (user_id);
ALTER TABLE cart_items ADD INDEX idx_cart_items_product_id (product_id);

-- Categories table indexes
ALTER TABLE categories ADD INDEX idx_categories_parent_id (parent_id);
ALTER TABLE categories ADD INDEX idx_categories_status (status);
