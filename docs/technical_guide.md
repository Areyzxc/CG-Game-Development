# Technical Guide for Code Gaming

## System Architecture
- **Frontend**: Built with HTML/CSS/JS and libraries like Bootstrap for responsive design, Three.js for animations, and Chart.js for dashboards.
- **Backend**: PHP handles logic, MySQL stores data (e.g., users table with progress fields).
- **Data Flow**: AJAX for real-time updates (e.g., quiz scoring); see UML diagrams in Capstone docs.

## Development Setup
Follow the README installation steps. Key files:
- `index.php`: Entry point (Anchor Page).
- `game.php`: Handles mini-games and scoring.
- `admin/dashboard.php`: Admin tools with SQL queries for stats.

## Maintenance Tips
- **Debugging**: Use browser console for JS errors; check PHP logs in XAMPP.
- **Updates**: Add new modes by extending `game_modes.js`—test for portability.
- **Security**: Ensure prepared statements in PHP to prevent SQL injection.

Contribute by forking and PR-ing changes. See LICENSE for usage rights.
