# Blog API

A robust and modern RESTful API for a social blogging platform, built with **Laravel 12** and **PHP 8.4**.

This API provides a complete backend solution for managing users, blog posts, comments, and likes, featuring secure authentication via Laravel Sanctum.

## 🚀 Features

-   **Authentication**: Secure user registration, login, and password management using Laravel Sanctum.
-   **User Management**: Profile retrieval, updates, and account deletion.
-   **Blog Posts**: Create, read, update, and delete posts. Includes a "Feed" of latest posts.
-   **Comments**: Threaded commenting system (supports one level of replies).
-   **Likes**: Toggle likes on posts and comments.
-   **Authorization**: Policy-based access control ensuring users can only edit/delete their own content.

## 🛠️ Tech Stack

-   **Framework**: Laravel 12
-   **Language**: PHP 8.4
-   **Database**: MySQL / SQLite (Configurable)
-   **Testing**: Pest PHP v4
-   **API Authentication**: Laravel Sanctum v4

## 📦 Installation

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd blog-api
    ```

2. **Install Dependencies**

    ```bash
    composer install
    ```

3. **Environment Setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database Setup**
   Configure your database credentials in `.env`, then run migrations:

    ```bash
    php artisan migrate
    ```

5. **Serve the Application**
    ```bash
    php artisan serve
    ```

## 🔗 API Endpoints

### Authentication

-   `POST /api/register` - Register a new user
-   `POST /api/login` - Login and receive API token
-   `POST /api/logout` - Revoke current token
-   `POST /api/change-password` - Update password

### Users

-   `GET /api/user` - Get authenticated user profile
-   `GET /api/user/{id}` - Get specific user profile
-   `PATCH /api/user` - Update profile details
-   `DELETE /api/user` - Delete account

### Posts

-   `GET /api/posts` - List all posts
-   `POST /api/posts` - Create a new post
-   `GET /api/posts/{id}` - View a single post
-   `PUT /api/posts/{id}` - Update a post
-   `DELETE /api/posts/{id}` - Delete a post
-   `GET /api/feed` - Get latest posts
-   `GET /api/{user}/posts` - Get posts by a specific user

### Comments

-   `POST /api/comments` - Add a comment (supports `parent_comment_id` for replies)
-   `PUT /api/comments/{id}` - Update a comment
-   `DELETE /api/comments/{id}` - Delete a comment

### Likes

-   `POST /api/like-toggle` - Toggle like on a Post or Comment
