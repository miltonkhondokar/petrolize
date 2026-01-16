# ⛽ Fuel Flow – Fuel Station Fuel Management System

Fuel Flow is a fuel-station-centric fuel management system designed to manage fuel pricing, stock, meter readings, expenses, and complaints across multiple fuel stations.

The system is built using Laravel and PostgreSQL with UUID-based soft relations and no database-level foreign keys to ensure scalability, flexibility, and historical data preservation.

---

## 📌 Core Architecture Principles

- Fuel station centric (not pump machine level)
- UUID-based relationships (application-level integrity)
- PostgreSQL-first
- No ENUM columns (string-based statuses)
- Historical data preserved (prices, stocks, readings)
- BigInt ID for internal use, UUID for business logic
- Seeder-safe and environment-safe architecture

---

## 🧱 Tech Stack

| Layer | Technology |
|-----|-----------|
| Backend | Laravel |
| Database | PostgreSQL |
| Auth | Laravel Auth + Spatie Roles |
| ORM | Eloquent |
| Identifiers | UUID (business) + bigint (internal) |
| Seeding | Ordered, dependency-safe |

---

## 📂 Domain Modules

### 1. Users & Audit
- Users
- Roles & Permissions
- Audit Logs

### 2. Fuel Station Management
- Fuel Stations
- Station Managers

### 3. Fuel Operations
- Fuel Units
- Fuel Types
- Fuel Prices
- Fuel Stocks
- Fuel Meter Readings

### 4. Financials
- Cost Categories
- Cost Entries

### 5. Operations & Support
- Fuel Station Complaints

---

## 🗄️ Database Schema

### users

| Column | Type |
|------|------|
| id | bigint |
| uuid | uuid |
| name | string |
| email | string |
| phone | string |
| password | string |
| gender | tinyint |
| user_status | tinyint |
| timestamps | |

---

### audit_logs

| Column | Type |
|------|------|
| uuid | uuid |
| user_id | bigint |
| action | string |
| type | string |
| item_id | bigint |
| ip_address | string |
| user_agent | text |
| created_at | timestamp |

---

### fuel_units

| Column | Type |
|------|------|
| uuid | uuid |
| name | string |
| abbreviation | string |
| description | text |

Examples: `L`, `mL`, `gal`, `imp gal`, `bbl`, `m³`

---

### fuel_types

| Column | Type |
|------|------|
| uuid | uuid |
| name | string |
| code | string |
| rating_value | integer |
| fuel_unit_uuid | uuid |
| is_active | boolean |
| timestamps | |

---

### fuel_stations

| Column | Type |
|------|------|
| uuid | uuid |
| user_uuid | uuid |
| name | string |
| location | string |
| is_active | boolean |
| timestamps | |

---

### fuel_station_fuel_prices

| Column | Type |
|------|------|
| uuid | uuid |
| fuel_station_uuid | uuid |
| fuel_type_uuid | uuid |
| price_per_unit | decimal |
| is_active | boolean |
| timestamps | |

---

### fuel_station_fuel_stocks

| Column | Type |
|------|------|
| uuid | uuid |
| fuel_station_uuid | uuid |
| fuel_type_uuid | uuid |
| fuel_unit_uuid | uuid |
| quantity | decimal |
| purchase_price | decimal |
| total_cost | decimal |
| stock_date | date |
| is_initial_stock | boolean |
| is_active | boolean |
| timestamps | |

---

### fuel_station_fuel_readings

| Column | Type |
|------|------|
| uuid | uuid |
| fuel_station_uuid | uuid |
| fuel_type_uuid | uuid |
| nozzle_number | integer |
| reading | decimal |
| reading_date | date |
| is_active | boolean |
| timestamps | |

---

### cost_categories

| Column | Type |
|------|------|
| uuid | uuid |
| name | string |
| description | text |
| is_active | boolean |
| timestamps | |

---

### cost_entries

| Column | Type |
|------|------|
| uuid | uuid |
| fuel_station_uuid | uuid |
| cost_category_uuid | uuid |
| amount | decimal |
| expense_date | date |
| reference_no | string |
| note | text |
| is_active | boolean |
| timestamps | |

---

### fuel_station_complaints

| Column | Type |
|------|------|
| uuid | uuid |
| fuel_station_uuid | uuid |
| category | string |
| title | string |
| description | text |
| status | string |
| complaint_date | date |
| resolved_date | date |
| is_active | boolean |
| timestamps | |

---

## 🔗 Logical Relationships (UUID Based)

### User
- hasMany FuelStations (user_uuid → uuid)

### FuelStation
- hasMany FuelStationFuelPrices
- hasMany FuelStationFuelStocks
- hasMany FuelStationFuelReadings
- hasMany CostEntries
- hasMany FuelStationComplaints

### FuelUnit
- hasMany FuelTypes

### FuelType
- belongsTo FuelUnit
- hasMany FuelStationFuelPrices
- hasMany FuelStationFuelStocks
- hasMany FuelStationFuelReadings

### CostCategory
- hasMany CostEntries

---

## 📐 ERD (Text Representation)

```
[users]
uuid
|
| user_uuid
v
[fuel_stations]
uuid
|
| fuel_station_uuid
├──────────────┬──────────────┬──────────────┐
v v v v
[fuel_prices] [fuel_stocks] [fuel_readings] [cost_entries]
|
| fuel_type_uuid
v
[fuel_types] ───────► [fuel_units]
```


---

## 🌱 Seeder Execution Order

```
FuelUnitSeeder

FuelTypeSeeder

FuelStationSeeder

FuelStationFuelPriceSeeder

FuelStationFuelStockSeeder

FuelStationFuelReadingSeeder

CostCategorySeeder

CostEntrySeeder

FuelStationComplaintSeeder
```


---

## 🧠 Business Rules

- Latest fuel price retrieved per station and fuel type
- Historical pricing and stock preserved
- Nozzle-wise meter readings supported
- Unit-agnostic fuel management
- No cascading deletes

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
- UUID-based relationships validated
- Seeder-safe architecture
- Production-ready foundation
