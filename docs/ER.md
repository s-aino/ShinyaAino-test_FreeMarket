# ER Diagram (Mermaid)

```mermaid
erDiagram
  users ||--o{ items : "sells"
  users ||--o{ addresses : "has"
  users ||--o{ comments : "writes"
  users ||--o{ orders : "buys(buyer_id)"

  categories ||--o{ items : "includes"
  items ||--o{ comments : "has"
  items |o--|| orders : "sold in"
  orders }o--|| addresses : "ships to"

users {
        bigint id PK
        varchar name
        varchar email
        varchar password
        datetime email_verified_at
        timestamps created_at updated_at
    }

    categories {
        bigint id PK
        varchar name
        varchar slug
        timestamps created_at updated_at
    }
    
  items {
    bigint id PK
    bigint user_id FK
    bigint category_id FK
    varchar title
    text description
    unsigned_int price
    varchar status
    varchar image_path
    timestamps created_at updated_at
  }

  addresses {
    bigint id PK
    bigint user_id FK
    varchar postal
    varchar prefecture
    varchar city
    varchar line1
    varchar line2
    varchar phone
    boolean is_default
    timestamps created_at updated_at
  }

  orders {
    bigint id PK
    bigint buyer_id FK
    bigint item_id FK
    unsigned_int price
    unsigned_int qty
    varchar status
    datetime ordered_at
    bigint address_id FK
    timestamps created_at updated_at
  }

  comments {
    bigint id PK
    bigint user_id FK
    bigint item_id FK
    varchar body
    timestamps created_at updated_at
  }
