# Clipboard - Collaborative Clipboard Application

A secure, feature-rich web-based clipboard application built with PHP and MySQL. Create boards, share snippets, and collaborate with your team.

## Features

### User Management
- User registration and authentication
- Secure password hashing with `password_hash()`
- Session-based authentication

### Board Management
- Create unlimited boards with custom names
- Automatic URL-friendly suburl generation
- Three public access levels:
  - **Private**: No public access
  - **Public View**: Anyone can view clips
  - **Public Add**: Anyone can view and add clips
- Optional password protection for boards
- Toggle clip editability (read-only mode)

### Access Control
- **Owner**: Full admin access to their boards
- **Collaborators**: Three permission levels
  - **View**: Can only view clips
  - **Edit**: Can view, add, edit, and delete clips
  - **Admin**: Full access including board settings
- Smart permission system: checks user-specific roles first, then falls back to public access
- Temporary password access for protected boards

### Clip Management
- Add, edit, and delete text-based clips
- Automatic timestamp tracking
- User attribution for clips
- Respects board editability settings
- Clean, readable presentation

## Technology Stack

- **Backend**: PHP 8.1+
- **Database**: MySQL with PDO (prepared statements)
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5 (responsive design)
- **Routing**: Front-controller pattern with `.htaccess`

## File Structure

```
CLIPBOARD/
├── config.php              # Configuration and database credentials
├── index.php               # Main router (front controller)
├── .htaccess               # URL rewriting rules
├── src/
│   ├── db.php              # PDO database connection
│   └── functions.php       # Core business logic
├── templates/
│   ├── header.php          # HTML header and navigation
│   ├── footer.php          # HTML footer
│   ├── home.php            # Homepage
│   ├── login.php           # Login form
│   ├── register.php        # Registration form
│   ├── board.php           # Board view with clips
│   ├── board_settings.php  # Board settings and collaborators
│   └── password_prompt.php # Password entry for protected boards
└── assets/
    └── style.css           # Custom CSS styles
```

## Installation

### Prerequisites
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- XAMPP, WAMP, or similar local development environment

### Setup Steps

1. **Clone or Download** this repository to your web server directory 

2. **Create Database**
   - Open phpMyAdmin or MySQL command line
   - Create a new database: `clipboard_db`
   - Import the database schema from `database.sql`

3. **Configure Database Connection**
   - Open `config.php`
   - Update the database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'clipboard_db');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

4. **Update Site URL**
   - In `config.php`, update the `SITE_URL` constant to match your setup:
     ```php
     define('SITE_URL', 'http://localhost/CLIPBOARD');
     ```

5. **Verify .htaccess**
   - Ensure `RewriteBase` in `.htaccess` matches your directory:
     ```apache
     RewriteBase /CLIPBOARD/
     ```

6. **Set Permissions** (Linux/Mac)
   ```bash
   chmod 755 CLIPBOARD/
   chmod 644 CLIPBOARD/*.php
   ```

7. **Enable mod_rewrite** (Apache)
   - Make sure `mod_rewrite` is enabled in Apache
   - For XAMPP, it's usually enabled by default

8. **Access the Application**
   - Open your browser and navigate to: `http://localhost/CLIPBOARD`

## Usage Guide

### Getting Started

1. **Register an Account**
   - Click "Register" in the navigation
   - Enter username, email, and password (min 6 characters)
   - You'll be redirected to login

2. **Create a Board**
   - After logging in, use the "Create New Board" form
   - Set a name, choose access level, and optionally add a password
   - Click "Create Board"

3. **Share Your Board**
   - Copy the board URL (e.g., `http://localhost/CLIPBOARD/b/my-board`)
   - Share with collaborators

### Managing Boards

#### Board Settings
- Click "Board Settings" button on your board
- Update name, access level, editability, or password
- Add/remove collaborators with specific permissions
- Delete the board (danger zone)

#### Access Levels Explained

- **Private**: Only you and invited collaborators can access
- **Public View**: Anyone with the link can view clips
- **Public Add**: Anyone with the link can view and add clips

#### Password Protection

- **Set a Password**: In board settings, enter a password to protect your board
- **Change Password**: Enter a new password to update the existing one
- **Remove Password**: Check the "Remove password protection" checkbox to disable password protection entirely
- **How it Works**: Visitors must enter the password to gain temporary view access
- **Use Case**: Great for sharing with non-registered users securely

### Working with Clips

#### Adding Clips
- If you have edit permission, use the "Add New Clip" form
- Enter your text content
- Click "Add Clip"

#### Editing Clips
- Click "Edit" button on any clip (if you have permission)
- Modify the content
- Click "Save"

#### Deleting Clips
- Click "Delete" button on any clip (if you have permission)
- Confirm the deletion

### Collaborating
1. **As Board Owner/Admin**:
   - Go to Board Settings
   - Select a user from the dropdown
   - Choose permission level (View, Edit, or Admin)
   - Click "Add"

2. **Permission Levels**:
   - **View**: Can see all clips but cannot add/edit/delete
   - **Edit**: Can add new clips and edit/delete existing ones (respects board's editable flag)
   - **Admin**: Can do everything including manage board settings

## Security Features

- **SQL Injection Protection**: All queries use PDO prepared statements
- **XSS Prevention**: All output is escaped with `htmlspecialchars()`
- **Password Security**: Passwords hashed with `password_hash()` (bcrypt)
- **Session Security**: HTTP-only cookies, secure session handling
- **CSRF Protection**: Consider adding CSRF tokens for production use
- **Permission Checks**: Every action validates user permissions

## Production Deployment

Before deploying to production:

1. **Update Configuration**
   - Set proper database credentials
   - Update `SITE_URL` to your domain
   - Enable HTTPS and set `session.cookie_secure` to 1

2. **Disable Error Display**
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

3. **Enable Error Logging**
   - Configure PHP to log errors to a file
   - Monitor logs regularly

4. **Add CSRF Protection**
   - Implement CSRF tokens for all forms
   - Validate tokens on POST requests

5. **Set Up Backups**
   - Regular database backups
   - File backups

6. **Use HTTPS**
   - Install SSL certificate
   - Force HTTPS in `.htaccess`

7. **Security Headers**
   - Add security headers in Apache config or PHP
   - Content-Security-Policy, X-Frame-Options, etc.
