-- USERS
INSERT INTO users (name, email, username, password, is_admin)
VALUES
('John Rollins', 'johnrollins789@gmail.com', 'johnr789', '$2y$10$Aqp2lMEfKQgI7zM0hXZ/OuVqp4LzYxOjQ6WYoF6n6.Ty4CuZySdVm', TRUE); 
-- NOTE: This hash is for "testpassword" (change if you want).

-- CATEGORIES
INSERT INTO categories (name) VALUES
('Hand Tools'), ('Watering'), ('Plant Care'), ('Power Tools'), ('Soil & Composting'), ('Accessories'), ('Compost & Soil'), ('Garden Carts');

-- PRODUCTS (example)
INSERT INTO products (name, description, price, image, category_id) VALUES
('Wheelbarrow', 'Heavy-duty wheelbarrow for hauling large loads.', 89.99, '/assets/images/wheelbarrow_1753457884.png', 8),
('Watering Wand', 'Gentle watering for plants.', 28.99, '/assets/images/watering_wand_1753457853.png', 2),
('Watering Can', 'Classic metal watering can.', 19.99, '/assets/images/watering_can_1753454595.png', 2);

