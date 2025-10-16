-- =================================================================
-- Filename: about_page_enhancements.sql
-- Description: Additional database tables for enhanced About page features
-- Features: Team members, timeline events, playlists, and enhanced feedback
-- Last Updated: September 28, 2025
-- =================================================================

USE coding_game;

-- Team Members Table
-- Stores detailed information about team members for the About page
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100) NOT NULL,
    age INT,
    email VARCHAR(100),
    code VARCHAR(20) UNIQUE,
    photo VARCHAR(255) DEFAULT 'assets/images/background.png',
    bio TEXT,
    fun_fact TEXT,
    mission_statement TEXT,
    facebook_url VARCHAR(255),
    instagram_url VARCHAR(255),
    github_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Timeline Events Table
-- Stores project development milestones for the timeline section
CREATE TABLE IF NOT EXISTS timeline_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    image_url VARCHAR(255),
    icon VARCHAR(50) DEFAULT 'fas fa-calendar',
    category ENUM('milestone', 'development', 'testing', 'launch', 'update') DEFAULT 'milestone',
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Coding Playlist Table
-- Stores music tracks for the coding playlist section
CREATE TABLE IF NOT EXISTS coding_playlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255),
    file_path VARCHAR(255),
    external_url VARCHAR(255),
    duration INT, -- in seconds
    genre VARCHAR(50),
    is_featured BOOLEAN DEFAULT FALSE,
    play_count INT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- FAQ Items Table
-- Stores frequently asked questions with search functionality
CREATE TABLE IF NOT EXISTS faq_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category ENUM('project', 'technology', 'team', 'general') DEFAULT 'general',
    tags VARCHAR(255), -- comma-separated for search
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Enhanced Feedback Likes Table
-- Tracks individual user likes on feedback messages
CREATE TABLE IF NOT EXISTS feedback_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feedback_id INT NOT NULL,
    user_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    liked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feedback_id) REFERENCES feedback_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_feedback_like (feedback_id, ip_address)
) ENGINE=InnoDB;

-- Project Statistics Table
-- Stores key project metrics for the impact section
CREATE TABLE IF NOT EXISTS project_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_name VARCHAR(100) NOT NULL UNIQUE,
    stat_value INT NOT NULL,
    stat_label VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-chart-line',
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =================================================================
-- Insert Sample Data
-- =================================================================

-- Insert team members (To be added)
INSERT INTO team_members (name, role, age, email, code, photo, bio, fun_fact, mission_statement, facebook_url, instagram_url, github_url, display_order) VALUES
('Belza, John Jaylyn I.', 'Frontend Developer', 23, 'jibelza@paterostechnologicalcollege.edu.ph', 'CG-001', 
 'assets/images/BELZA.jpg',
 'Passionate full-stack developer with expertise in PHP, JavaScript, and modern web technologies. Leads the technical architecture and ensures code quality across all projects.',
 'Loves debugging at 3AM and retro games. Can solve complex algorithms while listening to rap music.',
 'Always pushing the boundaries of code to create innovative solutions that make learning programming fun and accessible.',
 'https://www.facebook.com/johnjaylyn.ilovino', 'https://www.instagram.com/jaylynbelza/', 'https://github.com/johnjaylynilovino', 1),

('Constantino, Alvin Jr. B.', 'UI/UX Designer', 22, 'ajbconstantino@paterostechnologicalcollege.edu.ph', 'CG-002',
 'assets/images/Constantino.jpg',
 'Creative designer focused on user experience and interface design. Specializes in creating intuitive and visually appealing educational interfaces.',
 'Sketches wireframes on napkins during coffee breaks and has an eye for pixel-perfect designs.',
 'Designs with both fun and function in mind, ensuring every user interaction is delightful and meaningful.',
 'https://www.facebook.com/alvin.constantino.267801', '#', 'https://github.com/Konshiro20', 2),

('Sabangan, Ybo T.', 'Backend Specialist', 22, 'ytsabangan@paterostechnologicalcollege.edu.ph', 'CG-003',
 'assets/images/Sabangan.jpg',
 'Backend developer and database architect responsible for server-side logic, API development, and database optimization.',
 'API wizard and database whisperer who can optimize queries in his sleep.',
 'Keeps the engine running smoothly, ensuring scalable and secure backend infrastructure.',
 'https://www.facebook.com/ybo.sabangan', 'https://www.instagram.com/sabanganybo/', 'https://github.com/whybooo', 3),

('Santiago, James Aries G.', 'Lead Developer', 22, 'jgsantiago@paterostechnologicalcollege.edu.ph', 'CG-004',
 'assets/images/Santiago.PNG',
 'Passionate full-stack developer with expertise in PHP, JavaScript, and modern web technologies. Leads the technical architecture and ensures code quality across all projects.',
 'Can code and meme at the same time. Loves listening to j-pop music and playing video games.',
 'Lezzgoooo, the show must go on! Creating engaging frontend experiences that make learning code feel like playing a game.',
 'https://www.facebook.com/Areyszxc', 'https://www.instagram.com/areys27_tiago.san/?hl=en', 'https://github.com/Areyzxc', 4),

('Silvestre, Jesse Lei C.', 'QA & Testing Specialist', 22, 'jcsilvestre@paterostechnologicalcollege.edu.ph', 'CG-005',
 'assets/images/Silvestre.jpg',
 'Quality assurance specialist ensuring bug-free user experiences through comprehensive testing and quality control processes.',
 'Finds bugs even in dreams and has an uncanny ability to break things in creative ways.',
 'Ensures every feature is pixel-perfect and every user interaction is smooth and reliable.',
 'https://www.facebook.com/SalimShadyyyyy', 'https://www.instagram.com/_jissili/', 'https://github.com/not-leizy', 5),

('Valencia, Paul Dexter', 'Documentation & Support', 22, 'psvalencia@paterostechnologicalcollege.edu.ph', 'CG-006',
 'assets/images/Valencia.jpg',
 'Technical writer and user support specialist, creating comprehensive documentation and providing user assistance.',
 'Writes docs with style and substance, making complex technical concepts accessible to everyone.',
 'Bridges users and developers with clear guides, ensuring everyone can succeed with our platform.',
 'https://www.facebook.com/dextersanchez.valencia', 'https://www.instagram.com/pol_dxx?igsh=MXF4eHg4d294amxncA==', '#https://github.com/psvalencia-dev ', 6);

-- Insert timeline events (done)
INSERT INTO timeline_events (title, description, event_date, category, is_featured, display_order) VALUES
('Project Conception', 'Initial brainstorming and concept development for the Code Gaming platform', '2025-05-15', 'milestone', TRUE, 1),
('Team Formation', 'Assembly of the core development team and role assignments', '2025-05-20', 'milestone', TRUE, 2),
('Technical Planning', 'Architecture design and technology stack selection', '2025-05-23', 'development', FALSE, 3),
('UI/UX Design Phase', 'Creation of wireframes, mockups, and user experience design', '2025-06-30', 'development', TRUE, 4),
('Database Design', 'Database schema creation and optimization', '2025-07-14', 'development', FALSE, 5),
('Core Development', 'Implementation of core features and functionality', '2025-06-17', 'development', TRUE, 6),
('Alpha Testing', 'Internal testing and bug fixing phase', '2025-09-15', 'testing', FALSE, 7),
('Beta Release', 'Limited beta release for user feedback', '2025-10-20', 'testing', TRUE, 8),
('Feature Enhancement', 'Addition of advanced features based on user feedback', '2025-10-27', 'development', FALSE, 9),
('Public Launch', 'Official launch of the Code Gaming platform', '2025-11-10', 'launch', TRUE, 10);

-- Insert coding playlist (to be added)
INSERT INTO coding_playlist (title, artist, file_path, genre, is_featured, display_order) VALUES
('Andromeda Sunsets', 'Starjunk 95', 'audio/Andromeda_Sunsets.mp3', 'Synthwave, EDM', TRUE, 1),
('Apricot', 'Lo-Fi Producer', 'audio/apricot.mp3', 'Lo-Fi', TRUE, 2),
('Binary Dreams', 'Chiptune Master', 'audio/binary.mp3', 'Chiptune', TRUE, 3),
('Sleeper MK Ultra 4', 'Breakcore Artist', 'audio/SleeperMKUltra4.mp3', 'Ambient', TRUE, 4),
('Virtuality', 'Electronic Producer', 'audio/Virtuality.mp3', 'Electronic', TRUE, 5);

-- Insert FAQ items (done) (can be added more)
INSERT INTO faq_items (question, answer, category, tags, is_featured, display_order) VALUES
('What is Code Gaming?', 'Code Gaming is an innovative educational platform that teaches programming through interactive games, quizzes, and challenges. We make learning to code fun and engaging for students of all levels.', 'project', 'about,platform,education', TRUE, 1),
('What technologies do you use?', 'Our platform is built using modern web technologies including PHP, MySQL, JavaScript, HTML5, CSS3, Bootstrap 5, and various libraries like Three.js and ScrollReveal.js for enhanced user experience.', 'technology', 'tech,stack,php,javascript,mysql', TRUE, 2),
('Who can use this platform?', 'Code Gaming is designed for students, educators, and anyone interested in learning programming. Whether you are a complete beginner or looking to enhance your coding skills, our platform adapts to your learning pace.', 'general', 'users,students,beginners', TRUE, 3),
('How does the gamification work?', 'We use points, achievements, leaderboards, and interactive challenges to make learning programming feel like playing a game. Users earn rewards for completing tutorials, solving challenges, and participating in quizzes.', 'project', 'gamification,points,achievements', FALSE, 4),
('Is the platform free to use?', 'Yes! Code Gaming is completely free to use. We believe in making quality programming education accessible to everyone.', 'general', 'free,cost,pricing', TRUE, 5),
('What programming languages are supported?', 'Currently, we focus on web development technologies including HTML, CSS, JavaScript, Bootstrap, AJAX, and PHP. We plan to expand to other languages based on user feedback and demand.', 'technology', 'languages,html,css,javascript,php', FALSE, 6);

-- Insert project statistics (done)
INSERT INTO project_statistics (stat_name, stat_value, stat_label, icon, description, display_order) VALUES
('total_users', 500, 'Active Users', 'fas fa-users', 'Total number of registered and active users on the platform', 1),
('challenges_completed', 1250, 'Challenges Solved', 'fas fa-trophy', 'Total number of coding challenges completed by all users', 2),
('lines_of_code', 15000, 'Lines of Code', 'fas fa-code', 'Total lines of code written for the platform', 3),
('quiz_attempts', 3500, 'Quiz Attempts', 'fas fa-question-circle', 'Total number of quiz questions attempted by users', 4),
('feedback_received', 89, 'Feedback Messages', 'fas fa-comments', 'Total feedback messages received from users', 5),
('uptime_percentage', 99, 'Platform Uptime', 'fas fa-server', 'Platform availability and uptime percentage', 6);
