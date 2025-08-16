# Ecommerce API

A robust e-commerce API built with Laravel and structured using Domain-Driven Design (DDD) principles. This architecture
promotes modularity, maintainability, and scalability and is ideal for enterprise-level applications.

### Features

- Built on Laravel with expressive, elegant syntax.
- Modular codebase using Domain-Driven Design.
- Clean separation across layers: `Application`, `Domain`, `Infrastructure`.
- Docker support for consistent development environments
- Well written unit tests.
- Event-driven architecture.
- Event sourcing for full audit log.

### Tech Stacks

- Language & Framework: PHP, Laravel.
- Architecture: Domain-Driven Design (DDD).
- Database: PostgreSQL.
- Containerization: Docker & Docker Compose.
- Testing: PHP Pest.

### Getting Started

Ensure you have docker installed containerized setup.

### Installation

#### Step 1: Clone the repository

```bash
git clone https://github.com/ayodeleoniosun/ecommerce-api.git
```

#### Step 2: Switch to the repo folder

```bash
cd ecommerce-api
```

#### Step 3: Setup environment variable

- Copy `.env.example` to `.env` i.e `cp .env.example .env`
- Update all the variables as needed

#### Step 4: Setup docker containers and install all dependencies

```bash
bash setup.sh
```

### Documentation

The Postman API collection is locally available [Here](public/postman_collection.json). <br/>

### Testing

```bash
docker exec -it app php artisan test
```
