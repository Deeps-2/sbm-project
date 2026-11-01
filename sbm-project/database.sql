-- =============================================================================
-- SWACHH BHARAT MISSION (CLEAN INDIA) - MYSQL DATABASE SCHEMA & INITIAL DATA
-- Database Name: sbm_db
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `sbm_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sbm_db`;

-- 1. Table: users
DROP TABLE IF EXISTS `complaints`;
DROP TABLE IF EXISTS `areas`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `activities`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('citizen', 'worker', 'admin') NOT NULL DEFAULT 'citizen',
  `phone` VARCHAR(20) DEFAULT NULL,
  `ward_id` INT DEFAULT NULL,
  `points` INT DEFAULT 0,
  `badge` VARCHAR(100) DEFAULT 'Swachhta Mitra',
  `avatar` VARCHAR(255) DEFAULT '/assets/images/avatar_citizen1.svg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table: areas (Wards)
CREATE TABLE `areas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL,
  `ward_number` VARCHAR(50) NOT NULL,
  `zone` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) DEFAULT 'New Delhi',
  `latitude` DECIMAL(10, 6) NOT NULL,
  `longitude` DECIMAL(10, 6) NOT NULL,
  `cleanliness_score` INT DEFAULT 75,
  `status` ENUM('Clean', 'Moderate', 'Not Clean') DEFAULT 'Clean',
  `last_inspection_date` DATE DEFAULT NULL,
  `inspection_rating` INT DEFAULT 80,
  `total_bins` INT DEFAULT 20,
  `bins_functional` INT DEFAULT 18,
  `population_density` VARCHAR(50) DEFAULT 'Medium',
  `in_charge_officer` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table: complaints
CREATE TABLE `complaints` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tracking_code` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `area_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `location_address` VARCHAR(255) NOT NULL,
  `latitude` DECIMAL(10, 6) DEFAULT NULL,
  `longitude` DECIMAL(10, 6) DEFAULT NULL,
  `citizen_photo` VARCHAR(255) DEFAULT NULL,
  `priority` ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
  `status` ENUM('Pending', 'Assigned', 'In Progress', 'Verification Required', 'Resolved', 'Rejected') DEFAULT 'Pending',
  `assigned_worker_id` INT DEFAULT NULL,
  `worker_before_photo` VARCHAR(255) DEFAULT NULL,
  `worker_after_photo` VARCHAR(255) DEFAULT NULL,
  `worker_remarks` TEXT DEFAULT NULL,
  `worker_completed_at` DATETIME DEFAULT NULL,
  `admin_notes` TEXT DEFAULT NULL,
  `points_awarded` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`area_id`) REFERENCES `areas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_worker_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table: activities (Live feed & audit log)
CREATE TABLE `activities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `category` VARCHAR(50) DEFAULT 'General',
  `icon` VARCHAR(50) DEFAULT 'activity',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SEED DATA
-- =============================================================================

-- Seed Users
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `ward_id`, `points`, `badge`, `avatar`) VALUES
(1, 'Dr. Rajesh Verma', 'admin@sbm.gov.in', 'admin123', 'admin', '9876543210', 1, 0, 'Municipal Commissioner', '/assets/images/avatar_admin.svg'),
(2, 'Ramesh Kumar', 'worker@sbm.gov.in', 'worker123', 'worker', '9811223344', 1, 120, 'Lead Sanitation Worker', '/assets/images/avatar_worker1.svg'),
(3, 'Suresh Yadav', 'suresh@sbm.gov.in', 'worker123', 'worker', '9822334455', 2, 95, 'Sanitation Specialist', '/assets/images/avatar_worker2.svg'),
(4, 'Anita Devi', 'anita@sbm.gov.in', 'worker123', 'worker', '9833445566', 3, 140, 'Senior Field Cleaner', '/assets/images/avatar_worker3.svg'),
(5, 'Arun Patel', 'arun@sbm.gov.in', 'citizen123', 'citizen', '9844556677', 2, 680, 'National Swachh Icon', '/assets/images/avatar_citizen1.svg'),
(6, 'Pooja Sharma', 'citizen@sbm.gov.in', 'citizen123', 'citizen', '9855667788', 1, 420, 'Green Warrior', '/assets/images/avatar_citizen2.svg'),
(7, 'Neha Gupta', 'neha@sbm.gov.in', 'citizen123', 'citizen', '9866778899', 3, 290, 'Cleanliness Champion', '/assets/images/avatar_citizen3.svg'),
(8, 'Vikram Singh', 'vikram@sbm.gov.in', 'citizen123', 'citizen', '9877889900', 4, 150, 'Cleanliness Champion', '/assets/images/avatar_citizen4.svg'),
(9, 'Meera Reddy', 'meera@sbm.gov.in', 'citizen123', 'citizen', '9888990011', 5, 80, 'Swachhta Mitra', '/assets/images/avatar_citizen5.svg');

-- Seed Areas
INSERT INTO `areas` (`id`, `name`, `ward_number`, `zone`, `city`, `latitude`, `longitude`, `cleanliness_score`, `status`, `last_inspection_date`, `inspection_rating`, `total_bins`, `bins_functional`, `population_density`, `in_charge_officer`) VALUES
(1, 'Connaught Place & Inner Circle', 'Ward 12', 'Central Zone', 'New Delhi', 28.631500, 77.216700, 92, 'Clean', '2026-10-30', 94, 25, 24, 'High Commercial', 'Er. S. Sengupta'),
(2, 'Karol Bagh Market & Residential', 'Ward 04', 'Central Zone', 'New Delhi', 28.651700, 77.190600, 78, 'Moderate', '2026-10-28', 80, 30, 26, 'Dense Commercial', 'Insp. Anil Saxena'),
(3, 'Chandni Chowk Heritage Sector', 'Ward 08', 'North Zone', 'New Delhi', 28.656200, 77.230000, 54, 'Not Clean', '2026-10-26', 60, 35, 20, 'Very Dense', 'Insp. Mehmood Khan'),
(4, 'Lajpat Nagar Central Market', 'Ward 15', 'South Zone', 'New Delhi', 28.567700, 77.243300, 86, 'Clean', '2026-10-31', 88, 22, 21, 'High Footfall', 'Er. Sunita Rao'),
(5, 'Hauz Khas Village & Environs', 'Ward 22', 'South Zone', 'New Delhi', 28.549400, 77.200100, 89, 'Clean', '2026-10-29', 90, 18, 18, 'Medium Tourist', 'Insp. K. Raman'),
(6, 'Rohini Sector 14 Urban Hub', 'Ward 31', 'North-West Zone', 'New Delhi', 28.715900, 77.119800, 64, 'Moderate', '2026-10-27', 68, 28, 22, 'High Residential', 'Er. P. K. Mishra'),
(7, 'Mayur Vihar Phase 1 Enclave', 'Ward 19', 'East Zone', 'New Delhi', 28.608300, 77.296500, 83, 'Clean', '2026-10-30', 85, 20, 19, 'Medium Residential', 'Insp. Rekha Das'),
(8, 'Sadar Bazaar Wholesale Market', 'Ward 27', 'Central Zone', 'New Delhi', 28.658000, 77.214000, 48, 'Not Clean', '2026-10-25', 52, 40, 24, 'Intense Commercial', 'Er. Gaurav Mathur');

-- Seed Complaints
INSERT INTO `complaints` (`id`, `tracking_code`, `user_id`, `area_id`, `title`, `category`, `description`, `location_address`, `latitude`, `longitude`, `citizen_photo`, `priority`, `status`, `assigned_worker_id`, `worker_before_photo`, `worker_after_photo`, `worker_remarks`, `worker_completed_at`, `admin_notes`, `points_awarded`) VALUES
(1, 'SBM-2026-1042', 5, 2, 'Overflowing Trash and Plastic on Ajmal Khan Road', 'Plastic Waste', 'Severe accumulation of discarded single-use plastic, cardboard packaging, and beverage cups around municipal bin.', 'Ajmal Khan Road, near Metro Gate 2, Karol Bagh', 28.652100, 77.191200, '/assets/uploads/sample_before.svg', 'High', 'Verification Required', 2, '/assets/uploads/sample_before.svg', '/assets/uploads/sample_after.svg', 'Cleared 140kg dry plastic waste, swept 300m street sidewalk, washed curb, and installed segregated wet/dry bins with sanitizer spray.', '2026-11-01 09:30:00', 'Worker uploaded proof. Awaiting supervisor comparison.', 0),
(2, 'SBM-2026-1041', 6, 3, 'Unattended Garbage Dump blocking pedestrian walkway', 'Garbage Dump', 'Massive uncollected garbage accumulation near historical gate causing foul smell and stray animal issues.', 'Opposite Town Hall, Main Road, Chandni Chowk', 28.656500, 77.230500, '/assets/uploads/sample_before.svg', 'Urgent', 'Pending', NULL, NULL, NULL, NULL, NULL, 'Immediate mechanical sweeping and dump truck required.', 0),
(3, 'SBM-2026-1043', 7, 8, 'Debris dumped along market lane after night unloading', 'Garbage Dump', 'Mixed waste, rotting vegetable packaging and packing thermocol left scattered along the wholesale lane.', 'Gali Qasim Jan, Sadar Bazaar Main Lane', 28.658500, 77.214500, '/assets/uploads/sample_before.svg', 'High', 'In Progress', 3, '/assets/uploads/sample_before.svg', NULL, 'Dispatched team with compactor vehicle. Sweeping in progress.', NULL, 'Team deployed on site.', 0),
(4, 'SBM-2026-1044', 8, 1, 'Public park pathway littered with food boxes', 'Plastic Waste', 'Litter around the radial park fountain after evening food stall hours.', 'Block B Inner Circle, Connaught Place', 28.632000, 77.217000, '/assets/uploads/sample_park_before.svg', 'Medium', 'Resolved', 2, '/assets/uploads/sample_park_before.svg', '/assets/uploads/sample_park_after.svg', 'Cleaned thoroughly, washed pavers, added two high-capacity litter bins.', '2026-10-31 16:00:00', 'Verified by Admin. Excellent before/after contrast. 75 points awarded.', 75),
(5, 'SBM-2026-1045', 9, 4, 'Spilled garbage bin near bus stand', 'Overflowing Dustbin', 'Stray cattle overturned open bin causing debris spill onto road.', 'Near Central Market Bus Stop, Lajpat Nagar', 28.568000, 77.244000, '/assets/uploads/sample_before.svg', 'Medium', 'Assigned', 4, NULL, NULL, NULL, NULL, 'Assigned to Morning Sanitation Batch.', 0);

-- Seed Activities
INSERT INTO `activities` (`title`, `description`, `category`, `icon`) VALUES
('Task Verified & Approved', 'Admin verified Before/After photos for SBM-2026-1044 at Connaught Place.', 'Approval', 'check-circle'),
('New Photo Verification Submitted', 'Worker Ramesh Kumar submitted After-Cleaning photos for SBM-2026-1042.', 'Task', 'camera'),
('Task Assigned to Field Worker', 'SBM-2026-1043 assigned to Suresh Yadav with compactor vehicle.', 'Assignment', 'user-check'),
('Urgent Citizen Complaint Logged', 'Pooja Sharma reported garbage dump in Chandni Chowk (SBM-2026-1041).', 'Complaint', 'alert-triangle'),
('Cleanliness Score Updated', 'Ward 12 Connaught Place achieved 92/100 Swachh Adarsh benchmark.', 'Score', 'trending-up');
