# An Educational Game System for Teaching Coding Skills and Programming Languages for Pateros Technological College 🎮💻
Welcome to our BSIT Capstone project at Pateros Technological College! This repo hosts "Code Gaming," a web-based system to teach coding skills with fun challenges. Check out our [latest release v0.0.2](https://github.com/Areyzxc/Game-Development/releases/tag/v0.0.2) and join our growing team! 🚀

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![GitHub issues](https://img.shields.io/github/issues/Areyzxc/Game-Development)](https://github.com/Areyzxc/Game-Development/issues)
[![GitHub stars](https://img.shields.io/github/stars/Areyzxc/Game-Development)](https://github.com/Areyzxc/Game-Development/stargazers)
[![GitHub release](https://img.shields.io/github/v/release/Areyzxc/Game-Development)](https://github.com/Areyzxc/Game-Development/releases)

## Project Overview

**Code Gaming** is an interactive web-based educational game system designed to teach coding skills and programming languages in an engaging, gamified manner. Developed as a Capstone Project for BSIT/CSS Students and IT Experts at Pateros Technological College, this platform transforms traditional coding learning into fun challenges, tutorials, topics to read, and mini-games. It targets beginners, intermediate, and expert learners, focusing on languages such as HTML, CSS, Bootstrap, JavaScript, Python, Java, and C++. The platform incorporates points, progress tracking, motivational quotes from famous coding experts, and leaderboards to boost motivation.

The system was evaluated using the ISO/IEC 25010 quality model, with an emphasis on aspects such as functional suitability, performance efficiency, compatibility, maintainability, reliability, usability, portability, and security. This project aims to address common challenges in coding education, such as low engagement, demotivation, and struggles, by making learning feel like an adventure and with comfort.

## Purpose and Objectives

This repository serves as the codebase for our Capstone Project (Academic Year 2025-2026). The primary goals are:
- To enable players to efficiently complete coding challenges within a set time limit, ensuring high performance and productivity;
- To present players with intricate and thought-provoking coding tasks that encourage problem-solving and critical thinking;
- To assess players' proficiency in programming languages and/or non-programming languages, data structures, and algorithms through immersive and interactive gameplay;
- To introduce progressively challenging levels, engaging themes, and advanced coding problems to sustain players' interest and promote continuous skill development;
- To inspire players to climb the leaderboard by excelling in challenges and earning exclusive rewards and/or badges, encouraging sustained participation and;
- To evaluate the web-based gaming platform in terms of its functional suitability, performance efficiency, compatibility, maintainability, reliability, usability, portability, and security.

## Key Features

### Core Features
- **Interactive Learning Modules**: Hands-on coding challenges with instant feedback and progressive difficulty levels
- **Multi-Language Support**: Comprehensive tutorials and exercises for HTML, CSS, Bootstrap, JavaScript, Python, Java, and C++
- **Gamified Experience**: Points, stats, badges, and achievements to motivate learning progress
- **Real-time Code Execution**: Built-in code editor with syntax highlighting and live preview
- **Comprehensive Progress Tracking**: Detailed analytics and visualizations of learning journey

### User Experience
- **Personalized Dashboard**: Customizable user profiles with progress tracking and achievement display, updating user information, and uploading profile picture
- **Responsive Design**: Fully responsive interface that works on desktop and mobile devices
- **Accessibility**: WCAG 2.1 compliant with keyboard navigation and screen reader support
- **Interactive UI/UX**: Animated transitions, tooltips, and guided tours for better engagement

### Admin & Analytics
- **Admin Dashboard**: Comprehensive user management, content moderation, and system analytics
- **Real-time Monitoring**: Track active users/visitors, recent activities/logging, and system performance
- **Content Management**: Easy addition and updating of tutorials, challenges, mini-games, and quizzes through coding
- **Security**: Role-based access control and activity logging

## Technology Stack

### Frontend
- **Core**: HTML5, CSS3, Vanilla JavaScript (ES6+)
- **UI Framework**: Bootstrap 5.3 with custom theming
- **Animation & Effects**: 
  - Three.js for 3D elements
  - Anime.js for micro-interactions
  - ScrollReveal.js for scroll-based animations
  - Typed.js for typewriter effects
- **Data Visualization**: Charts.js for progress tracking and visitor statistics
- **Icons & UI**: Boxicons and Font Awesome
- **Fonts**: RadiationHollow, Calliste, Monochrome, and more. [Downloaded from Dafont.com]

### Backend
- **Server**: PHP 8.1+
- **Database**: MySQL 8.0+ with InnoDB
- **API**: RESTful API architecture
- **Sessions & Authentication**: Custom session management with JWT support
- **Security**: Prepared statements, CSRF protection, XSS prevention

### Development Tools
- **Version Control**: Git with GitHub
- **Package Management**: Composer (PHP)
- **Local Development**: XAMPP/WAMP
- **Code Quality**: ESLint, Prettier, PHP_CodeSniffer

## Installation and Setup

1. **Prerequisites**:
   - XAMPP (for local PHP/MySQL server).
   - A modern web browser (e.g., Chrome, Brave, Firefox, MS Edge, etc).

2. **Clone the Repository**:
   ```
   git clone https://github.com/your-repo-name/CG-Game-Development.git
   cd Game-Development
   ```

3. **Set Up Database**:
   - Import the provided SQL file (e.g., `code_gaming.sql`) into MySQL via phpMyAdmin.
   - Update database credentials in `config.php` and 'database.php'.

4. **Run Locally**:
   - Start XAMPP (Apache + MySQL).
   - Open `http://localhost/CG-Game-Development` in your browser.

5. **Deployment** (Optional):
   - Use GitHub Pages for static previews or a free host like Heroku/Netlify for full deployment.
  
6. **Recommendation**:
   - Recommended Free Host: InfinityFree (reliable for PHP/MySQL, unlimited subdomains, HTTPS, no ads; alternatives: AeonFree or Byet.host if needed).

## Usage

- **As a User**: Register/login, explore tutorials/topics, play mini-games, quizzes, and challenges to earn points, and track progress on your dashboard.
- **As a Guest/Visitor**: Browse to any pages and explore more by signing up!
- **As an Admin**: Access the dashboard to monitor users, post announcements, and view analytics.
- **Testing**: Run surveys (included in `/surveys` or this Google Form link: ) to evaluate via ISO/IEC 25010—analyze results for Capstone reporting in Chapters 4 and 5.

For detailed user guides, see the `/docs` folder.

## Contributors

This project is a collaborative effort by BSIT-4F students at Pateros Technological College:

- **Belza, John Jaylyn**  
- **Constantino, Alvin Jr.**  
- **Sabangan, Ybo**  
- **Santiago, James Aries**  
- **Silvestre, Jesse Lei**  
- **Valencia, Paul Dexter**

We welcome contributions! Fork the repo, create a branch, and submit a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

Special thanks to our professor and panelists in Capstone from Pateros Technological College for guidance. Inspired by gamification studies and open-source educational tools.

For questions or collaborations, contact us via GitHub Issues and Discussions (Make sure to have a GitHub account registered) or email us at [jamesariess76@gmail.com].
