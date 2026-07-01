# SQL Verification - Transport Data Format

## Verify Data is Saved Correctly

Run these SQL queries to verify transport data is saved in the correct format:

### 1. Check Custom Package Transport (Reference)

```sql
SELECT * FROM custom_package_transport LIMIT 5;
```

**Expected Result:**
```
entry_id | package_id | vehicle_name | pickup | drop | pickup_type | drop_type
---------|------------|--------------|--------|------|-------------|----------
1        | 10         | 25           | 123    | 456  | city        | hotel
2        | 10         | 30           | 789    | 123  | airport     | city
```

Note: 
- `pickup` contains **ID only** (123, not "city-123")
- `drop` contains **ID only** (456, not "hotel-456")
- Types stored separately in `pickup_type` and `drop_type`

---

### 2. Check Group Tour Transport (Our Implementation)

```sql
SELECT * FROM tour_groups_transport LIMIT 5;
```

**Expected Result:**
```
entry_id | tour_id | vehicle_name | pickup | drop_location | pickup_type | drop_type
---------|---------|--------------|--------|---------------|-------------|----------
1        | 50      | 25           | 123    | 456           | city        | hotel
2        | 50      | 30           | 789    | 123           | airport     | city
```

Note: 
- `pickup` contains **ID only** (123, not "city-123")
- `drop_location` contains **ID only** (456, not "hotel-456")
- Types stored separately in `pickup_type` and `drop_type`

---

## Verify Location Display

### Get Pickup Location Name:

```sql
-- For City (pickup_type = 'city', pickup = '123')
SELECT city_name FROM city_master WHERE city_id = '123';
-- Returns: "Mumbai"

-- For Hotel (pickup_type = 'hotel', pickup = '456')
SELECT hotel_name FROM hotel_master WHERE hotel_id = '456';
-- Returns: "Taj Hotel"

-- For Airport (pickup_type = 'airport', pickup = '789')
SELECT airport_name, airport_code FROM airport_master WHERE airport_id = '789';
-- Returns: "Chhatrapati Shivaji International Airport", "BOM"
```

---

## Data Integrity Check

### Verify All Saved Transport Has Valid References:

```sql
-- Check if all pickup cities exist
SELECT t.entry_id, t.pickup, t.pickup_type 
FROM tour_groups_transport t
LEFT JOIN city_master c ON t.pickup = c.city_id AND t.pickup_type = 'city'
WHERE t.pickup_type = 'city' AND c.city_id IS NULL;
-- Should return 0 rows (all cities valid)

-- Check if all pickup hotels exist
SELECT t.entry_id, t.pickup, t.pickup_type 
FROM tour_groups_transport t
LEFT JOIN hotel_master h ON t.pickup = h.hotel_id AND t.pickup_type = 'hotel'
WHERE t.pickup_type = 'hotel' AND h.hotel_id IS NULL;
-- Should return 0 rows (all hotels valid)

-- Check if all pickup airports exist
SELECT t.entry_id, t.pickup, t.pickup_type 
FROM tour_groups_transport t
LEFT JOIN airport_master a ON t.pickup = a.airport_id AND t.pickup_type = 'airport'
WHERE t.pickup_type = 'airport' AND a.airport_id IS NULL;
-- Should return 0 rows (all airports valid)
```

---

## Sample Test Data

### Insert Test Transport:

```sql
-- Test with City to Hotel transport
INSERT INTO tour_groups_transport 
  (entry_id, tour_id, vehicle_name, pickup, pickup_type, drop_location, drop_type) 
VALUES 
  (NULL, 1, 25, '1', 'city', '5', 'hotel');
  
-- Where:
-- tour_id = 1 (your tour)
-- vehicle_name = 25 (vehicle entry_id from b2b_transfer_master)
-- pickup = '1' (city_id from city_master)
-- pickup_type = 'city'
-- drop_location = '5' (hotel_id from hotel_master)
-- drop_type = 'hotel'
```

### Verify It Displays Correctly:

```sql
-- Get the saved transport
SELECT * FROM tour_groups_transport WHERE tour_id = 1;

-- Get pickup city name
SELECT city_name FROM city_master WHERE city_id = '1';  -- Should return city name

-- Get drop hotel name
SELECT hotel_name FROM hotel_master WHERE hotel_id = '5';  -- Should return hotel name

-- Get vehicle name
SELECT vehicle_name FROM b2b_transfer_master WHERE entry_id = '25';  -- Should return vehicle name
```

---

## ✅ Confirmation

If your data looks like this in the database:

**tour_groups_transport:**
```
pickup = "123" (NOT "city-123")
pickup_type = "city"
drop_location = "456" (NOT "hotel-456")  
drop_type = "hotel"
```

**Then it's CORRECT!** ✅

This matches exactly how `custom_package_transport` saves data.

---

## 🔧 Troubleshooting

**Problem:** Data shows as "city-123" in database  
**Solution:** Model is not extracting ID - check `tours_master.php` lines 221-238

**Problem:** Pickup/Drop shows as ID in update form  
**Solution:** Check `transport_tbl.php` is querying the right master table

**Problem:** Type is not captured correctly  
**Solution:** Ensure optgroup has `value` attribute matching the type

---

**Implementation Status:** ✅ Complete and matches `custom_package_transport` exactly!



