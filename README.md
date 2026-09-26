# Laravel RESTful API

Book and User management API built with Laravel 12, Sanctum authentication.

## Stack

- PHP ^8.2
- Laravel ^12.0
- Laravel Sanctum ^4.0
- MySQL

## Features

- Register / login gated by HTTP Basic Auth
- Token auth (Sanctum Bearer) for Book and User routes
- Book CRUD with pagination
- User + UserProfile CRUD in DB transaction
- Auto-generated OpenAPI docs via Scramble

## Getting Started

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Configure in `.env`:

```env
APP_URL=http://localhost:8000
BASIC_AUTH_USERNAME=your-username
BASIC_AUTH_PASSWORD=your-password
```

`BASIC_AUTH_*` values are read in `config/app.php` (`app.basic_auth`) and enforced by `app/Http/Middleware/BasicAuthMiddleware.php`.

## API Documentation (Scramble)

- UI: `GET /docs/api`
- OpenAPI JSON: `GET /docs/api.json`

Scramble documents all routes under `api_path: api`.

## Authentication

Two layers:

1. **HTTP Basic Auth** — required for `register` and `login` only.

```bash
curl -u username:password http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@mail.com","password":"password123"}'
```

2. **Bearer token** — required for all `/book/*` and `/user/*` routes. Token is issued by `login`.

```bash
curl http://localhost:8000/api/v1/book/list \
  -H "Authorization: Bearer <token>" \
  -H "Accept: application/json"
```

## Endpoints

Base prefix: `/api/v1`

### Auth (Basic Auth required)

#### POST `/register`

Body:

```json
{
    "name": "John",
    "email": "john@mail.com",
    "password": "password123"
}
```

- `name`: required
- `email`: required, email, unique
- `password`: required, min 8

Success `200`:

```json
{
    "message": "User created successfully!",
    "user": { "id": 1, "name": "John", "email": "john@mail.com" }
}
```

#### POST `/login`

Body:

```json
{
    "email": "john@mail.com",
    "password": "password123"
}
```

Success `200`:

```json
{
    "message": "User login successfully!",
    "token": { "token": "<plain-text-token>", "type": "Bearer" }
}
```

Failure `401`:

```json
{ "error": "The provided credentials are incorects" }
```

### Book (Bearer required)

#### GET `/book/list?perPage=5`

`perPage` defaults to `5`. Success `200` returns Laravel paginator in `data`. Empty result returns `404`:

```json
{ "message": "Tidak ada data buku" }
```

#### POST `/book/create`

```json
{
    "title": "Clean Code",
    "description": "A book about writing code"
}
```

- `title`: required
- `description`: nullable

Success returns `{ "message": "Book created successfully!", "data": {...} }`.

#### PUT `/book/update/{id}`

Same body as create. Success returns `{ "message": "Book updated successfully!", "data": {...} }`. Non-existent id throws `404` via `findOrFail`.

#### DELETE `/book/delete/{book}`

Uses route model binding. Success:

```json
{ "message": "Book deleted successfully!" }
```

### User (Bearer required)

#### GET `/user/list`

Returns all users with `profile` relation:

```json
{ "data": [{ "id": 1, "name": "John", "profile": {...} }] }
```

Status `201` (as implemented).

#### POST `/user/create`

```json
{
    "name": "John",
    "email": "john@mail.com",
    "password": "password123",
    "first_name": "John",
    "last_name": "Doe"
}
```

Success `201`:

```json
{
  "message": "User & profile has been created!",
  "data": { "id": 1, "profile": {...} }
}
```

Failure `500`:

```json
{
    "message": "Failed to create user & profile",
    "error": "<exception message>"
}
```

#### PUT `/user/update/{user}`

Same fields, `password` is `nullable` (omitted = keep existing). Uses `updateOrCreate` for profile. Success `201`, failure `500`.

#### DELETE `/user/delete/{user}`

Soft deletes (model uses `SoftDeletes`). Success `200`:

```json
{ "message": "User & profile has been deleted!" }
```

## Status Codes

| Code | Meaning                                           |
| ---- | ------------------------------------------------- |
| 200  | Success (auth, book routes, user delete)          |
| 201  | Created / success (user list, user create/update) |
| 401  | Missing/invalid Basic Auth, or invalid login      |
| 404  | Empty book list, model not found                  |
| 422  | Validation error                                  |
| 500  | User transaction failure                          |
