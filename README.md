# login-app-php

## Introduction

The login-app-php project is a web application that allows users to register, login, and manage their profiles. It involves a signup page for user registration, a login page for authentication, and a profile page where users can view and update their additional details.

## Flow

The project follows the following user flow:

1. **Register**: Users can register by providing necessary details, such as username, email, and password. 

2. **Login**: Registered users can log in with their credentials to access their profiles.

3. **Profile**: Upon successful login, users are redirected to the profile page, where they can view and update additional information,including username, including age, date of birth (DOB), and contact details.


## Tech Stack

The project leverages the following technologies:

- HTML
- CSS (Bootstrap for responsiveness)
- JavaScript
- PHP
- Redis (for session management)
- MongoDB (for storing user profiles)
- MySQL (for storing registered user data)

### Project Configuration

Before you can run this project, you need to set up some environment variables. Create an `.env` file in the project root and add the following variables:

- `MONGO_URL`: MongoDB connection string (e.g., `mongodb+srv://your-connection-string`)
- `DB_HOST`: Database host 
- `DB_USERNAME`: Database username 
- `DB_PASSWORD`: Database password 
- `DB_NAME`: Database name

## Usage

To use the login-app-php application:

1. Run `git clone https://github.com/bobbyrepo/login-app-php` to clone the repository to your local machine.

2. Set up a web server for your project. You can use a local development server like XAMPP (for PHP and MySQL) or MongoDB's server. Make sure your web server is running.

3. Install project dependencies using `composer install` in the project directory.

4. Start the development server.

5. Open your browser and visit `http://127.0.0.1:5500/index.html` to access the application.

Follow the points mentioned in the assignment instructions to ensure proper implementation.

