-- Esquema base del proyecto (agregado payments)
CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  table_number INT NOT NULL,
  method ENUM('modo','qr','cash','transfer') NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('pendiente','pagado','fallido','cancelado') DEFAULT 'pendiente',
  date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  notes VARCHAR(255) DEFAULT NULL
);