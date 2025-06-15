CREATE TABLE drivers (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Unique identifier for each driver
    driver_name VARCHAR(255) NOT NULL, -- Driver's full name
    phone_number VARCHAR(15) NOT NULL, -- Driver's phone number
    id_number VARCHAR(20) NOT NULL,   -- Driver's identification number
    dl_number VARCHAR(20) NOT NULL,   -- Driver's license number
    sacco VARCHAR(100),               -- SACCO the driver belongs to
    years_of_experience INT CHECK (years_of_experience >= 0), -- Years of driving experience
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Record creation time
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Record update time
);
-- Indexes for faster lookups
CREATE INDEX idx_driver_name ON drivers(driver_name);