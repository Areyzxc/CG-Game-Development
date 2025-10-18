# An Educational Game System for Teaching Coding Skills and Programming Languages for Pateros Technological College

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

- **Anchor Page**: The main landing point upon visiting this code gaming system, with some detailed description about this platform is managed.
- **User Profiles**: Customizable profiles with editable usernames, email, bios, location, banners, avatars, and progress tracking.
- **Interactive Tutorials**: Step-by-step guides with dynamic pop-ups, pagination, and progress indicators for learning coding concepts.
- **Game Modes**: Mini-games like "Guess the Output" and "Speed Typing," quizzes, and challenges with scoring, timers, and feedback.
- **Engagement Elements**: points, leaderboards, floating icons, background music, and a feedback container located at the About Us page for a personalized experience.
- **Admin Dashboard**: Tools for user monitoring, announcements, and system stats, powered by charts and tables.
- **Visitor Tracking**: Non-registered users' activities are logged to encourage sign-ups.

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5, Three.js (for 3D effects on Anchor page), Chart.js (for progress visuals), ScrollReveal.js (for animations), Boxicons, Popper.js, Typed.js, Rellax.js, and Anime.js.
- **Backend**: PHP, MySQL (via XAMPP for local development), AJAX for seamless interactions.
- **Database**: MySQL tables for users, visitors, questions, admins, logs, progress-tracking data, feedback-likes, team-members, and much, much more.
- **Deployment**: Hosted via GitHub Student Pack (e.g., Namecheap domains, Digital Ocean/Azure for free tier)/InfinityFree
- **Development Methodology**: Agile with weekly/monthly meetings and iterative sprints.

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

Special thanks to our professors and panelists from Capstone and Pateros Technological College for guidance. Inspired by gamification studies and open-source educational tools.

For questions or collaborations, contact us via GitHub Issues and Discussions (Make sure to have a GitHub account registered) or email us at [jamesariess76@gmail.com].
