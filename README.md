# Storages

Warehouse management system — purchase products from providers, store them, sell to clients, and track refunds and profit.

## Stack

- PHP 8.4 / Laravel 13
- MySQL 9
- Nginx
- Docker Compose

## Getting Started

```bash
# Build and start containers
docker compose up -d --build

# Run migrations
docker compose exec app php artisan migrate

# Seed test data
docker compose exec app php artisan db:seed
```

API base URL: `http://localhost:8080/api`

## Database Connection

| | Value |
|---|---|
| Host (inside Docker) | `mysql` |
| Host (from host machine) | `127.0.0.1` |
| Port (from host machine) | `3307` — set via `DB_EXTERNAL_PORT` in `.env` |
| Port (inside Docker) | `3306` — set via `DB_PORT` in `.env` |
| Database | `storages` |
| Username | `storages` |
| Password | `secret` |

> `DB_PORT` is used by Laravel to connect inside Docker (always 3306).
> `DB_EXTERNAL_PORT` is for external tools like TablePlus or DBeaver.

## Common Commands

```bash
docker compose up -d                                       # start
docker compose down                                        # stop
docker compose logs -f app                                 # logs
docker compose exec app php artisan migrate:fresh --seed   # reset and reseed
```

## Seeders

Run all seeders via `php artisan db:seed`. Three seeders run in order:

### StorageSeeder
Creates 4 warehouse locations:
- Main Warehouse
- Chilanzar Warehouse
- Yunusabad Warehouse
- Sergeli Warehouse

### ProviderCategorySeeder
Creates 2 providers with hierarchical category trees (root → child → grandchild):

**Ahmad Tea Co**
```
Ahmad Tea (root)
├── Black Tea
│   ├── Earl Grey
│   └── English Blend
├── Green Tea
│   └── Sencha
└── White Tea
    └── Classic White
```

**Unilever (Lipton)**
```
Lipton (root)
├── Tea Bags
│   ├── Yellow Label
│   └── Green Label
└── Loose Leaf
    ├── Ceylon Black
    └── Darjeeling
```

> Root categories are linked to a provider (`provider_id`). Child categories use `parent_id`. A category cannot have both.

### ClientSeeder
Creates 3 clients:
- Korzinka Market
- Makro Supermarket
- Havas Market

---

## API Endpoints

### Task-required endpoints

#### POST /api/batches/purchase — Purchase products and add to storage

```json
{
  "code": "BATCH-001",
  "provider_id": 1,
  "storage_id": 1,
  "products": [
    { "id": 1, "qty": 100, "purchase_price": 38000 },
    { "id": 2, "qty": 50,  "purchase_price": 18000 }
  ]
}
```

Response `201`:
```json
{ "message": "Purchased successfully", "batch_id": 1, "code": "BATCH-001" }
```

---

#### POST /api/batches/refund — Refund unsold products back to provider

```json
{
  "items": [
    { "batch_item_id": 1, "qty": 10 }
  ]
}
```

Response `200`:
```json
{ "message": "Purchase refund processed" }
```

---

#### GET /api/products/available — List available products for ordering

Response `200`:
```json
[
  {
    "id": 1,
    "name": "Ahmad Earl Grey 500g",
    "category_name": "Earl Grey",
    "sale_price": 45000,
    "available_qty": 90
  }
]
```

---

#### POST /api/orders — Create client order (FIFO batch assignment)

Backend automatically selects the oldest available batch per product.

```json
{
  "client_id": 1,
  "products": [
    { "id": 1, "qty": 20 },
    { "id": 2, "qty": 10 }
  ]
}
```

Response `201`:
```json
{ "message": "Order created", "order_id": 1 }
```

---

#### GET /api/products/remaining?date=2026-01-31 — Remaining stock as of a date

Returns quantities based on all inventory movements up to and including the given date.

Response `200`:
```json
[
  {
    "id": 1,
    "name": "Ahmad Earl Grey 500g",
    "category_name": "Earl Grey",
    "sale_price": 45000,
    "remaining_qty": 70
  }
]
```

---

#### GET /api/batches/profit — Profit per batch

Accounts for purchase refunds (reduce cost) and client order refunds (reduce revenue).

Response `200`:
```json
[
  {
    "batch_id": 1,
    "provider": "Ahmad Tea Co",
    "cost": 3800000,
    "revenue": 4500000,
    "profit": 700000
  }
]
```

---

### Additional endpoints

#### POST /api/orders/refund — Refund products from client order

```json
{
  "items": [
    { "order_item_id": 1, "batch_item_id": 2, "qty": 5 }
  ]
}
```

Response `200`:
```json
{ "message": "Order refund processed" }
```

#### GET /api/orders — List all orders with items and inventory movements
#### GET /api/batches — List all batches with items and products

#### Providers CRUD
```
GET    /api/providers
POST   /api/providers
GET    /api/providers/{id}
PUT    /api/providers/{id}
DELETE /api/providers/{id}
```

#### Categories CRUD
```
GET    /api/categories
POST   /api/categories
GET    /api/categories/{id}
PUT    /api/categories/{id}
```

#### Products CRUD
```
GET    /api/products
POST   /api/products
GET    /api/products/{id}
PUT    /api/products/{id}
```

#### Storages
```
GET    /api/storages
POST   /api/storages
GET    /api/storages/{id}
PUT    /api/storages/{id}
GET    /api/storages/remaining
```

---

## Database Schema

```
providers → categories → products
                              ↓
batches → batch_items ←───────┘
    ↓          ↓
storages   inventory_movements ← order_items ← orders → clients
```

**Tables:** `providers`, `categories`, `products`, `storages`, `clients`, `batches`, `batch_items`, `orders`, `order_items`, `inventory_movements`
