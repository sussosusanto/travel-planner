# Travel Planner REST API

## Overview

Travel Planner is a RESTful API built using the Laravel framework. This API allows users to manage their travel plans, including CRUD operations for users and travels, with authentication implemented via Laravel Sanctum.

## Features

- User Registration and Login
- Token-Based Authentication (using Laravel Sanctum)
- CRUD operations for managing travel plans
- Input validation and error handling
- Comprehensive testing including unit and integration tests

## Requirements

- Docker
- PHP 8.2 or higher
- Composer
- Laravel 11
- MySQL or SQLite (for testing)
- Redis

### Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/sussosusanto/travel-planner.git
   cd travel-planner
   ```

2. **Install dependencies:**

   ```bash
   composer install
   ```
3. **Run the docker container for redis and mysql:**

   ```bash 
   docker compose -f docker-compose.yml up -d
   ```

4. **Copy the `.env` file:**

   ```bash
   cp .env.example .env
   ```

5. **Configure your database and redis connection in the `.env` file:**

   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=laravel

   CACHE_STORE=redis
   CACHE_PREFIX=test


   REDIS_CLIENT=predis
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

   **Run migrations:**

   ```bash
   php artisan migrate
   ```

## Running the Application

   **Start the development server:**

   ```bash
   php artisan serve
   ```


## API Endpoints

### Authentication

- `POST /api/register` - Register a new user
- `POST /api/login` - Log in a user
- `POST /api/logout` - Log out a user (requires token)

### Travels

- `GET /api/travels` - Get a list of all travel plans for the logged-in user
- `GET /api/travels/{id}` - Get details of a specific travel plan
- `POST /api/travels` - Create a new travel plan
- `PUT /api/travels/{id}` - Update a travel plan
- `DELETE /api/travels/{id}` - Delete a travel plan

## Running Tests

1. **Unit and Feature Tests:**

   ```bash
   php artisan test
   ```

   This command will run all unit and feature tests, ensuring that your application works as expected.

