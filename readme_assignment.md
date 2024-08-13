*******Task Management System*******

Overview

A task management system similar to Trello where users can sign up, log in, create task boards, and manage tasks within those boards. Built using Laravel for the backend APIs.

Features

1. User authentication (signup and login)
2. Each user can create and manage multiple task boards
3. Each task board can contain multiple tasks
4. Full CRUD (Create, Read, Update, Delete) operations for boards and tasks
5. Data persisted in a database
6. Rate limiting (throttling) for API requests

Installation

1. Clone the repository:

  git clone [repository-url]
  cd [repository-directory]

2. Install dependencies:


  composer install


3. Set up the environment:

  Copy the .env.example file to .env and update the environment variables as needed.


  cp .env.example .env

4. Generate an application key:


  php artisan key:generate

5. Run migrations:


  php artisan migrate

6. Start the development server:


  php artisan serve

7. API Endpoints

  

  1. User Signup

     POST /api/register
     (Required fields: email, password, password_confirmation)

  2. User Login

     POST /api/login
     (Required fields: email, password)

  3. User Logout

     POST /api/logout
     

       

  3. Board Endpoints

     1. GET /api/boards - List all boards
     2. POST /api/boards - Create a new board
     3. PUT /api/boards/{id} - Update a board
     4. GET /api/boards/{id} - Get a specific board
     5. DELETE /api/boards/{id} - Delete a board

  4. Task Endpoints

     1. GET /api/tasks/board/{id} - List all tasks for a specific board
     2. POST /api/tasks/board/{id} - Create a new task for a specific board
     3. GET /api/tasks/{id} - Get a specific task
     4. PUT /api/tasks/{id} - Update a task
     5. DELETE /api/tasks/{id} - Delete a task

 8. Authentication

    Token Authentication: Use the token provided upon login for authenticated requests.

 9. Rate Limiting

    Throttling: API requests are limited to 10 requests per minute for public endpoints and 60 requests per minute for authenticated endpoints.