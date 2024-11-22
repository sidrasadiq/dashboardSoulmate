-- Create Database
CREATE DATABASE themillionaireso_sm_panel

-- Create Roles Table
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT
);

-- Create Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create Cast Table
CREATE TABLE user_cast (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cast_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create Qualifications Table
CREATE TABLE qualifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    qualification_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create religion Table
CREATE TABLE religion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    religion_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create Nationality Table
CREATE TABLE nationality (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nationality_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create Countries Table
CREATE TABLE countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL UNIQUE,
    country_code VARCHAR(3) UNIQUE,  -- ISO country code (e.g., "PK" for Pakistan)
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create Cities Table
CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(100) NOT NULL,
    country_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create Profiles Table
CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    gender ENUM('male', 'female', 'other') NOT NULL,
    date_of_birth DATE,
    bio TEXT,
    profile_picture VARCHAR(255),
    
    -- Foreign keys for city and country
    country_id INT,
    city_id INT,
    
    -- New contact and social fields
    contact_number VARCHAR(15),
    whatsapp_contact VARCHAR(15),
    cnic VARCHAR(15) UNIQUE,
    social_links JSON,  -- Storing social links as a JSON object for flexibility

    -- Foreign keys for additional profile attributes
    cast_id INT,  -- References user_cast now
    nationality_id INT,
    religion_id INT,
    qualification_id INT,

    looking_for ENUM('male', 'female', 'other'),
    interests TEXT,
    preferences JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,

    -- Foreign key constraints
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE SET NULL,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE SET NULL,
    FOREIGN KEY (cast_id) REFERENCES user_cast(id) ON DELETE SET NULL,
    FOREIGN KEY (nationality_id) REFERENCES nationality(id) ON DELETE SET NULL,
    FOREIGN KEY (religion_id) REFERENCES religion(id) ON DELETE SET NULL,
    FOREIGN KEY (qualification_id) REFERENCES qualifications(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Dummy data for roles table. 
INSERT INTO `roles`(`role_name`, `description`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES ('admin','Admin, who can manage every thing.',NOW() ,NOW(), 1, 1)

-- Dummy data for users table. 
INSERT INTO users(`username`, `email`, `password`, `role_id`, `is_active`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
('admin', 'admin@soulmate.com.pk', '$2y$10$8UxCHvWu4yZrfec4833eNeyk4KjKgzjYS004fs7iP3knp8eULtQda', 1, TRUE, NOW(), NOW(), 1, 1);

-- Adding foreign key to roles table.
ALTER TABLE roles
ADD CONSTRAINT fk_roles_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_roles_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL;

-- Added some more fields in Profile table
ALTER TABLE profiles
ADD COLUMN prefer_age_from TINYINT UNSIGNED,
ADD COLUMN prefer_age_to TINYINT UNSIGNED,
ADD COLUMN relationship_looking SET('marriage', 'friendship'),
ADD COLUMN ethnicity ENUM('arab (middle eastern)', 'asian', 'black', 'caucasian (white)', 'hispanic/latino', 'indian', 'pacific islander', 'other', 'mixed', 'prefer not to say') NOT NULL,
ADD COLUMN beliefs ENUM('islam-sunni', 'islam-shiite', 'islam-sufism', 'islam-ahmadiyya', 'islam-other', 'willing-to-revert', 'other', 'prefer not to say') NOT NULL,
ADD COLUMN drink_alcohol ENUM('do drink', 'occasionally drink', 'don\'t drink', 'prefer not to say') NOT NULL,
ADD COLUMN smoking ENUM('do smoke', 'occasionally smoke', 'don\'t smoke', 'prefer not to say') NOT NULL,
ADD COLUMN children ENUM('yes', 'not sure', 'no') NOT NULL,
ADD COLUMN marital_status ENUM('single', 'separated', 'widowed', 'divorced', 'other', 'prefer not to say') NOT NULL,
ADD COLUMN my_appearance ENUM('below average', 'average', 'attractive', 'very attractive') NOT NULL,
ADD COLUMN body_type ENUM('petite', 'slim', 'average', 'few extra pounds', 'full figured', 'large and lovely') NOT NULL;

-- Steates table
CREATE TABLE states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    state_name VARCHAR(100) NOT NULL,
    state_abbreviation VARCHAR(2) NOT NULL,
      country_id INT,
    is_active 	tinyint(1) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- added columns for verification
ALTER TABLE users 
ADD COLUMN is_verified TINYINT(1) DEFAULT 0,
ADD COLUMN verification_token VARCHAR(255) NULL;

ALTER TABLE users
ADD COLUMN otp VARCHAR(6) NULL;
