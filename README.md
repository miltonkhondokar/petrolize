# ⛽ Fuel Flow – Pump Fuel Management System

Fuel Flow is a pump-centric fuel management system designed to manage fuel pricing, stock, readings, costs, and complaints across multiple fuel pumps.  
The system is built using Laravel, PostgreSQL, and UUID-based soft relations without hard foreign keys.

---

## 📌 Core Design Principles

- UUID-based relationships (no hard foreign keys)
- PostgreSQL compatible
- Scalable multi-pump architecture
- Historical data preserved (prices, stocks, readings)
- Application-level relational integrity
- No ENUM columns (flexible string/status fields)

---

## 🧱 Tech Stack

| Layer       | Technology                             |
| ----------- | -------------------------------------- |
| Backend     | Laravel                                |
| Database    | PostgreSQL                             |
| Auth        | Laravel Auth + Spatie Roles            |
| Identifiers | UUID (business) + bigint ID (internal) |
| ORM         | Eloquent                               |
| Seeding     | Environment-safe seeders               |

---

## 📂 Domain Modules

### 1. User & Audit
- Users
- Roles & Permissions
- Audit Logs

### 2. Pump Management
- Pumps
- Pump Managers (Users)

### 3. Fuel Management
- Fuel Types
- Fuel Units (International)
- Fuel Prices
- Fuel Stocks
- Fuel Meter Readings

### 4. Financials
- Cost Categories
- Cost Entries

### 5. Operations
- Pump Complaints

---

## 🗄️ Database Structure

### users

| Column      | Type    |
| ----------- | ------- |
| id          | bigint  |
| uuid        | uuid    |
| name        | string  |
| email       | string  |
| phone       | string  |
| password    | string  |
| gender      | tinyint |
| user_status | tinyint |
| timestamps  |         |

---

### audit_logs

| Column     | Type      |
| ---------- | --------- |
| uuid       | uuid      |
| user_id    | bigint    |
| action     | string    |
| type       | string    |
| item_id    | bigint    |
| ip_address | string    |
| user_agent | text      |
| created_at | timestamp |

---

### fuel_units

| Column       | Type   |
| ------------ | ------ |
| uuid         | uuid   |
| name         | string |
| abbreviation | string |
| description  | text   |

Examples: `L`, `mL`, `gal`, `imp gal`, `bbl`, `m³`

---

### fuel_types

| Column         | Type    |
| -------------- | ------- |
| uuid           | uuid    |
| name           | string  |
| code           | string  |
| rating_value   | integer |
| fuel_unit_uuid | uuid    |
| is_active      | boolean |
| timestamps     |         |

---

### pumps

| Column     | Type    |
| ---------- | ------- |
| uuid       | uuid    |
| user_uuid  | uuid    |
| name       | string  |
| location   | string  |
| is_active  | boolean |
| timestamps |         |

---

### pump_fuel_prices

| Column         | Type    |
| -------------- | ------- |
| uuid           | uuid    |
| pump_uuid      | uuid    |
| fuel_type_uuid | uuid    |
| price_per_unit | decimal |
| is_active      | boolean |
| timestamps     |         |

---

### pump_fuel_stocks

| Column           | Type    |
| ---------------- | ------- |
| uuid             | uuid    |
| pump_uuid        | uuid    |
| fuel_type_uuid   | uuid    |
| fuel_unit_uuid   | uuid    |
| quantity         | decimal |
| purchase_price   | decimal |
| total_cost       | decimal |
| stock_date       | date    |
| is_initial_stock | boolean |
| is_active        | boolean |
| timestamps       |         |

---

### pump_fuel_readings

| Column         | Type    |
| -------------- | ------- |
| uuid           | uuid    |
| pump_uuid      | uuid    |
| fuel_type_uuid | uuid    |
| nozzle_number  | int     |
| reading        | decimal |
| reading_date   | date    |
| is_active      | boolean |
| timestamps     |         |

---

### cost_categories

| Column      | Type    |
| ----------- | ------- |
| uuid        | uuid    |
| name        | string  |
| description | text    |
| is_active   | boolean |
| timestamps  |         |

---

### cost_entries

| Column             | Type    |
| ------------------ | ------- |
| uuid               | uuid    |
| pump_uuid          | uuid    |
| cost_category_uuid | uuid    |
| amount             | decimal |
| expense_date       | date    |
| reference_no       | string  |
| note               | text    |
| is_active          | boolean |
| timestamps         |         |

---

### pump_complaints

| Column         | Type    |
| -------------- | ------- |
| uuid           | uuid    |
| pump_uuid      | uuid    |
| category       | string  |
| title          | string  |
| description    | text    |
| status         | string  |
| complaint_date | date    |
| resolved_date  | date    |
| is_active      | boolean |
| timestamps     |         |

---

## 🔗 Logical Relationships (UUID Based)

User (uuid)
- hasMany Pumps (user_uuid)

Pump (uuid)
- hasMany PumpFuelPrices
- hasMany PumpFuelStocks
- hasMany PumpFuelReadings
- hasMany CostEntries
- hasMany PumpComplaints

FuelUnit (uuid)
- hasMany FuelTypes

FuelType (uuid)
- hasMany PumpFuelPrices
- hasMany PumpFuelStocks
- hasMany PumpFuelReadings
- belongsTo FuelUnit

CostCategory (uuid)
- hasMany CostEntries

---

## 📐 ERD (Text Representation)
```
[users]
   uuid
     |
     | user_uuid
     v
[pumps]───────────────┐
   uuid               │
     │                │
     │ pump_uuid      │ pump_uuid
     v                v
[pump_fuel_prices]  [pump_fuel_stocks]
     │                │
     │ fuel_type_uuid │ fuel_type_uuid
     v                v
         [fuel_types]───────[fuel_units]
```

---

## 🌱 Seeder Execution Order


FuelUnitSeeder
FuelTypeSeeder
PumpSeeder
PumpFuelPriceSeeder
PumpFuelStockSeeder
PumpFuelReadingSeeder
CostCategorySeeder
CostEntrySeeder
PumpComplaintSeeder


---

## 🧠 Key Business Logic

- Latest fuel price retrieved per pump and fuel type
- Historical pricing and stock maintained
- Nozzle-wise meter readings supported
- Unit-agnostic fuel management

---

## 🚀 Future Enhancements

- Sales calculation from readings
- Automatic stock deduction
- Profit and loss reports
- Unit conversion engine
- Analytics dashboard

---

## ✅ Project Status

- Database schema finalized
- UUID-based relationships verified
- Seeder-safe architecture
- Production-ready foundation
