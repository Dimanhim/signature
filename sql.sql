CREATE TABLE `alfa_payment_logs` (
                                     `id` INT AUTO_INCREMENT PRIMARY KEY,
                                     `appointment_id` INT NOT NULL,
                                     `patient_id` INT NOT NULL,
                                     `invoice_number` VARCHAR(50) NULL,
                                     `response_data` TEXT NULL,
                                     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                     KEY `idx_appointment` (`appointment_id`),
                                     KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;