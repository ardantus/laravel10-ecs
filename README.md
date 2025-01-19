# Laravel 10.3.3 Simulation

This project is a simulation of a Laravel 10 application using PHP 8.3, MySQL 8.0, and Nginx. It also uses S3 for storage. The project includes a Dockerfile to create an image that can be hosted on ECS and a docker-compose.yml file to run the application locally.

## Requirements

- Docker
- Docker Compose

## Setup

1. Clone the repository:
    ```sh
    git clone https://github.com/your-repo/laravel-10-simulation.git
    cd laravel-10-simulation
    ```

2. Build the Docker image:
    ```sh
    docker build -t laravel-10-simulation .
    ```

3. Run the application locally using Docker Compose:
    ```sh
    docker-compose up -d --build
    ```

4. Install dependencies:
    ```sh
    docker-compose exec app composer install
    ```

5. Run database migrations:
    ```sh
    docker-compose exec app php artisan migrate
    ```

# Username
php artisan tinker
use Illuminate\Support\Facades\Hash;
use App\Models\User;

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com', // Gunakan email ini untuk username
    'password' => Hash::make('password123') // Gunakan password ini untuk login
]);
exit


## Dockerfile

```Dockerfile
FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
```

## docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    image: laravel-10-simulation
    container_name: laravel_app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - .:/var/www
    networks:
      - laravel

  web:
    image: nginx:alpine
    container_name: nginx
    restart: unless-stopped
    ports:
      - "80:80"
    volumes:
      - .:/var/www
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    networks:
      - laravel

  db:
    image: mysql:8.0
    container_name: mysql
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: laravel
      MYSQL_USER: laravel
      MYSQL_PASSWORD: secret
    ports:
      - "3306:3306"
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - laravel

networks:
  laravel:

volumes:
  dbdata:
```

## Storage

This project uses S3 for storage. Make sure to configure your S3 credentials in the `.env` file.
