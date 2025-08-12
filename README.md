# CardFactory

CardFactory is an e-commerce platform for creating, managing, and sharing customizable cards. Users can design cards, add them to their cart, and purchase them. The platform also provides an API for accessing shared cards.

## Prerequisites

Ensure you have the following installed on your system:
- PHP 8.1 or higher
- Composer 2.x
- Symfony CLI
- A sass compiler

## Installation

Follow these steps to set up the project locally:

1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo/CardFactory.git
   cd CardFactory
   ```

2. Install dependencies using Composer:
   ```bash
   composer install
   ```

3. Set up the database:
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. Compile frontend assets
   **Note:** This project doesn't include any sass compiler. It uses Vscode integrated Live Sass Compiler extension to compile SCSS files.
   Input folder is `assets/scss`
   Output folder is `public/css`

5. Update .env variables
    ```
    DATABASE_URL= [Your database connection string here]
    ```

6. Start the development server:
   ```bash
   symfony server:start
   ```

## Loading Fixtures

To populate the database with sample data, run the following command:

```bash
php bin/console doctrine:fixtures:load
```

This will create three users. Their credentials are:
- **User 1**: `user1@example.com`, password: `password1`
- **User 2**: `user2@example.com`, password: `password2`
- **User 3**: `user3@example.com`, password: `password3`

## Features

- User authentication and authorization
- Card creation, editing, and deletion
- Add cards to cart and manage orders
- API for accessing shared cards
- Responsive and user-friendly interface

## API Documentation

CardFactory provides a RESTful API for accessing and managing shared cards. Below are the available endpoints:

### Authentication
- **POST /api/login**
  - Description: Authenticate a user and retrieve a JWT token. The user must have ROLE_ADMIN.
    This step isn't needed to list shared cards.
  - Request Body:
    ```json
    {
      "email": "user@example.com",
      "password": "password"
    }
    ```
  - Response:
    ```json
    {
      "token": "your-jwt-token"
    }
    ```

### Cards
- **GET /api/cards**
  - Description: Retrieve a list of shared cards.
  - Options:
    - `sort`: Sort order (default: `DESC` for newest first)
    - `template`: Filter by template class (optional)
        - `default`
        - `large`
  - Response:
    ```json
    [
      {
        "id": 28,
        "name": "Card 2",
        "author": "user2",
        "card_title": "Title 2",
        "card_subtitle": "Subtitle 2",
        "card_body": "Body content for card 2 by user2.",
        "template": {
          "id": 11,
          "css_class": "large",
          "name": "Large Template",
          "imageWidth": 1200,
          "imageHeight": 900
        },
        "card_image_url": "/uploads/images//uploads/images/fixture-dragon.jpg"
      },
      ...
    ]
    ```

### Sales Data
- **GET /api/sales**
  - Description: Retrieve sales statistics. Only accessible to users with the `ROLE_ADMIN` role.
  - Headers:
    - `Authorization: Bearer your-jwt-token`
  - Query Parameters:
    - `start` (optional): Start date in `YYYY-MM-DD` format.
    - `end` (optional): End date in `YYYY-MM-DD` format.
  - Response:
    ```json
    [
        {
            "salesCoount": 127,
            "salesTotal": 1580.50
        }
    ]
    ```
