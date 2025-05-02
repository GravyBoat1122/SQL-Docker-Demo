# SQL-Docker-Demo
My SQL Database &amp; Docker, proof of completion and record keeping for Wolkwerker.

# PHP Parent-Child User Management App

This is a simple PHP web application for managing users and their relationships. Specifically, it lets you:

- Add users (with name and surname).
- Mark a user as a parent.
- Assign children to a parent.
- View a table of all users, showing which users are parents and who their parents are.

## Technologies Used

- PHP
- MySQL (via PDO)
- HTML & CSS (inline)
- JavaScript (inline for dynamic interaction)
- Docker (recommended for MySQL container)
  
## Files Overview

### `main.php`
The main user interface:
- Form to add users.
- If "Is Parent" is checked, shows a searchable list of non-parent users to assign as children.
- Displays a table of all users and their relationships.

### `main_h.php`
Handles:
- Database connection (`devdb` on host `mysql`).
- Table creation (auto-creates `users` and `user_parents` tables if they don't exist).
- POST submission: inserts users and parent-child relations.
- Loads user data for the frontend.

Also includes embedded:
- CSS styles for layout and appearance.
- JavaScript for dynamic child selection UI.

## Database Schema

### `users` Table
| Column      | Type         | Description            |
|-------------|--------------|------------------------|
| id          | INT (PK)     | Auto-increment ID      |
| name        | VARCHAR(50)  | User's name            |
| surname     | VARCHAR(100) | User's surname         |
| is_parent   | BOOLEAN      | Whether the user is a parent |
| created_at  | TIMESTAMP    | Auto-filled on insert  |

### `user_parents` Table
| Column     | Type     | Description                      |
|------------|----------|----------------------------------|
| child_id   | INT (FK) | References a user (the child)    |
| parent_id  | INT (FK) | References a user (the parent)   |
*Primary key is a combination of `child_id` and `parent_id`.

## Setup Instructions

### 1. Clone the Repository
```bash
git clone https://github.com/GravyBoat1122/SQL-Docker_Demo.git
