# School Management System

A web-based school management system built with PHP and MySQL that allows students to register for courses and view their academic information through a dashboard.

## Features

- **Student Authentication**: Secure login system with password hashing
- **Course Registration**: Interactive course selection and registration
- **Dashboard**: Student information overview and registered courses display
- **Semester Filtering**: Filter courses by semester
- **Responsive Design**: Mobile-friendly interface

## Project Structure

```
Agile/
├── config/
│   └── db.php                 # Database connection configuration
├── Dashboard/
│   └── index.php             # Student dashboard
├── Database/
│   └── school_system.sql     # Database schema and sample data
├── Login/
│   ├── hash.php              # Password hashing utility
│   ├── index.php             # Login page
│   └── process_login.php     # Login authentication handler
└── Registration/
    ├── index.php             # Course registration interface
    └── regpage.php           # Registration processing
```

## Database Schema

The system uses a MySQL database with the following tables:

- **student**: Student information and credentials
- **course**: Course details and capacity
- **semester**: Academic semesters
- **registration**: Student course registrations
- **regdata**: Registration metadata

## Installation

1. **Set up the database**:
   - Create a MySQL database named `school_system`
   - Import the SQL file: [`Database/school_system.sql`](Database/school_system.sql)

2. **Configure database connection**:
   - Update [`config/db.php`](config/db.php) with your database credentials:
   ```php
   $servername = "localhost";
   $username_db = "your_username";
   $password_db = "your_password";
   $dbname = "school_system";
   ```

3. **Deploy to web server**:
   - Copy all files to your web server directory
   - Ensure PHP and MySQL are properly configured

## Usage

### Login
- Navigate to [`Login/index.php`](Login/index.php)
- Use the demo credentials:
  - **Email**: `hamadaezzo@school.edu`
  - **Password**: `password`

### Course Registration
1. After login, access the registration page via [`Registration/index.php`](Registration/index.php)
2. Browse available courses
3. Filter by semester if needed
4. Select courses by clicking on them
5. Submit registration

### Dashboard
- View student information at [`Dashboard/index.php`](Dashboard/index.php)
- See registered courses and academic overview
- Access course registration from the dashboard

## Key Components

### Authentication System
- Password hashing using PHP's `password_hash()` function
- Session management for user state
- Protected routes requiring login

### Course Registration Logic
The registration system in [`Registration/regpage.php`](Registration/regpage.php):
- Validates course selections
- Uses database transactions for data integrity
- Links students to semesters rather than individual courses

### Database Connection
Centralized database configuration in [`config/db.php`](config/db.php) with:
- MySQLi connection with error handling
- UTF-8 character set support

## Security Features

- **Password Hashing**: Secure password storage using PHP's password hashing functions
- **SQL Injection Prevention**: Prepared statements throughout the application
- **Session Management**: Proper session handling for user authentication
- **Input Validation**: Server-side validation for form submissions

## Sample Data

The system includes sample data:
- One student account (Hamada Ezzo)
- Two courses (Mathematics 1, Intro to BioInformatics)
- Four semesters (Fall 2024 through Spring 2026)

## Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB)
- **Web Server**: Apache/Nginx
- **Browser**: Modern web browser with JavaScript enabled

## Future Enhancements

- Grade management system
- Course capacity enforcement
- Email notifications
- Admin panel for course management
- Student profile editing
- Password reset functionality

## Troubleshooting

### Common Issues

1. **Database Connection Failed**:
   - Verify database credentials in [`config/db.php`](config/db.php)
   - Ensure MySQL service is running

2. **Login Issues**:
   - Check if the sample user exists in the database
   - Verify password hash using [`Login/hash.php`](Login/hash.php)

3. **Registration Problems**:
   - Ensure courses exist in the database
   - Check foreign key constraints are properly set

## License

This project is for educational purposes. Feel free to modify and use as needed.