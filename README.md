# symfony fil rouge



## requires

composer require symfony/maker-bundle --dev

composer require orm

composer require orm-fixtures --dev

composer require symfony/serializer-pack




## tools

composer install

symfony server:start

php bin/console make:entity

php bin/console doctrine:database:create

php bin/console doctrine:schema:update --force

php bin/console doctrine:fixtures:load

php bin/console make:controller

# deploy dev environment:

clone git repository

in console at root:

    composer install

    php bin/console doctrine:database:create

    php bin/console doctrine:schema:update --force

    php bin/console doctrine:fixtures:load


create virtualhost WAMP

    symfony server:start


# PersonalSpaceController Documentation

## Overview

The `PersonalSpaceController` is a Symfony controller that provides API endpoints for managing user profile information, historical records, and submitting financial data for the previous month.

## Routes

### 1. Get User Profile

**Endpoint:** `/api/user/profile`\
**Method:** `GET`\
**Route Name:** `profile`

**Description:**\
Retrieves the profile information of the currently authenticated user.

**Parameters:** None

**Response:**

- `200 OK` with JSON representation of the user's profile.
- `401 Unauthorized` if no user is authenticated.

**Example Response:**

```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john.doe@example.com"
}
```

---

### 2. Get User History

**Endpoint:** `/api/user/history`\
**Method:** `GET`\
**Route Name:** `history`

**Description:**\
Retrieves the financial history records of the authenticated user.

**Parameters:** None

**Response:**

- `200 OK` with JSON representation of the user's history.
- `401 Unauthorized` if no user is authenticated.

**Example Response:**

```json
[
  {
    "id": 1,
    "date": "2024-01-01",
    "amount": 1000
  },
  {
    "id": 2,
    "date": "2024-02-01",
    "amount": 1200
  }
]
```

---

### 3. Submit Last Month's Amount

**Endpoint:** `/api/user/submit`\
**Method:** `POST`\
**Route Name:** `submit`

**Description:**\
Allows the authenticated user to submit the financial amount for the previous month. If an entry does not exist, it will create one; otherwise, it will update the existing entry if the amount is null.

**Request Body:**

```json
{
  "amount": 1500
}
```

**Response:**

- `200 OK` if the amount was successfully recorded.
- `400 Bad Request` if the amount is missing or invalid.
- `401 Unauthorized` if no user is authenticated.

**Example Success Response:**

```json
{
  "status": "success",
  "message": "New record created and amount submitted for the last month.",
  "amount": 1500
}
```

**Example Error Response:**

```json
{
  "status": "error",
  "message": "Invalid amount provided."
}
```

---

# UserController Documentation

## Overview

The `UserController` is a Symfony controller that provides an API endpoint for user registration.

## Routes

### 1. Create User

**Endpoint:** `/api/login_signin`\
**Method:** `POST`\
**Route Name:** `createUser`

**Description:**\
Creates a new user account by accepting user details in JSON format and storing them in the database with a hashed password.

**Request Body:**

```json
{
  "email": "user@example.com",
  "password": "securepassword",
  "name": "John Doe"
}
```

**Response:**

- `201 Created` if the user was successfully registered.
- `400 Bad Request` if the request data is invalid.

**Example Success Response:**

```json
{
  "id": 1,
  "email": "user@example.com",
  "name": "John Doe"
}
```

---

# Authentication API Documentation

## Overview

The `api/login` route is used to authenticate users and generate a JWT token for accessing secured endpoints.

### 1. User Login

**Endpoint:** `/api/login`\
**Method:** `POST`

**Description:**\
Authenticates a user and returns a JWT token if the credentials are valid.

**Request Body:**

```json
{
  "email": "user@example.com",
  "password": "securepassword"
}
```

**Response:**

- `200 OK` if authentication is successful, returning a JWT token.
- `401 Unauthorized` if authentication fails due to invalid credentials.

**Example Success Response:**

```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Example Error Response:**

```json
{
  "message": "Invalid credentials."
}
```

