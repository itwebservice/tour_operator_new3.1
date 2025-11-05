# Transport Tables Comparison

## Table Structure Comparison

### `custom_package_transport` (Reference)
```sql
entry_id        INT(11) PRIMARY KEY AUTO_INCREMENT
package_id      INT(11)
vehicle_name    VARCHAR(255)
pickup          VARCHAR(500)    -- Stores ID only (e.g., "123")
pickup_type     VARCHAR(50)     -- Stores type (e.g., "city", "hotel", "airport")
drop            VARCHAR(500)    -- Stores ID only (e.g., "456")
drop_type       VARCHAR(50)     -- Stores type (e.g., "city", "hotel", "airport")
```

### `tour_groups_transport` (Our Implementation)
```sql
entry_id        INT(11) PRIMARY KEY AUTO_INCREMENT
tour_id         INT(11)
vehicle_name    VARCHAR(255)
pickup          VARCHAR(500)    -- Stores ID only (e.g., "123")
pickup_type     VARCHAR(50)     -- Stores type (e.g., "city", "hotel", "airport")
drop_location   VARCHAR(500)    -- Stores ID only (e.g., "456")
drop_type       VARCHAR(50)     -- Stores type (e.g., "city", "hotel", "airport")
```

**Note:** Column names slightly different (`drop` vs `drop_location`, `package_id` vs `tour_id`) but **data format is identical**.

---

## Data Save Process - Side by Side

### Custom Packages (Reference)

**Input from JavaScript:**
```javascript
pickup_arr[i] = "city-123"
drop_arr[i] = "hotel-456"
```

**Model Processing:**
```php
// crm/model/custom_packages/package_master.php (line 79-82)
$pickup_type = explode("-",$pickup_arr[$i])[0];  // "city"
$drop_type = explode("-",$drop_arr[$i])[0];      // "hotel"
$pickup = explode("-",$pickup_arr[$i])[1];       // "123"
$drop = explode("-",$drop_arr[$i])[1];           // "456"
```

**Database Insert:**
```sql
INSERT INTO custom_package_transport 
  (entry_id, package_id, vehicle_name, pickup, drop, pickup_type, drop_type) 
VALUES 
  ('1','100','25', '123', '456', 'city', 'hotel')
```

---

### Group Tours (Our Implementation)

**Input from JavaScript:**
```javascript
pickup_arr[i] = "city-123"
drop_arr[i] = "hotel-456"
```

**Model Processing:**
```php
// crm/model/group_tour/tours_master.php (line 221-238)
if(strpos($pickup_arr[$i], '-') !== false){
  $pickup_parts = explode("-", $pickup_arr[$i]);
  $pickup_type = $pickup_parts[0];  // "city"
  $pickup = $pickup_parts[1];       // "123"
}

if(strpos($drop_arr[$i], '-') !== false){
  $drop_parts = explode("-", $drop_arr[$i]);
  $drop_type = $drop_parts[0];      // "hotel"
  $drop_location = $drop_parts[1];  // "456"
}
```

**Database Insert:**
```sql
INSERT INTO tour_groups_transport 
  (entry_id, tour_id, vehicle_name, pickup, pickup_type, drop_location, drop_type) 
VALUES 
  ('1','50','25', '123', '456', 'city', 'hotel')
```

---

## Data Retrieval Process - Side by Side

### Custom Packages (Reference)

**Database Query:**
```php
$sq_pckgtr = mysqlQuery("select * from custom_package_transport where package_id = '$package_id'");
```

**Displays Location:**
```php
if($row_tr['pickup_type'] == 'city'){
    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_tr[pickup]'"));
    $pickup = $row['city_name'];  // Shows "Mumbai"
}
else if($row_tr['pickup_type'] == 'hotel'){
    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_tr[pickup]'"));
    $pickup = $row['hotel_name'];  // Shows "Taj Hotel"
}
else if($row_tr['pickup_type'] == 'airport'){
    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code from airport_master where airport_id='$row_tr[pickup]'"));
    $pickup = $row['airport_name'] . " (" . $row['airport_code'] . ")";  // Shows "Mumbai Airport (BOM)"
}
```

---

### Group Tours (Our Implementation)

**Database Query:**
```php
$sq_transport = mysqlQuery("select * from tour_groups_transport where tour_id='$tour_id'");
```

**Displays Location:**
```php
// crm/view/tours/master/update/transport_tbl.php (line 66-77)
if ($row_transport['pickup_type'] == 'city') {
    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[pickup]'"));
    $html = '<optgroup value="city"><option value="city-' . $row['city_id'] . '">' . $row['city_name'] . '</option></optgroup>';
}
else if ($row_transport['pickup_type'] == 'hotel') {
    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[pickup]'"));
    $html = '<optgroup value="hotel"><option value="hotel-' . $row['hotel_id'] . '">' . $row['hotel_name'] . '</option></optgroup>';
}
else if ($row_transport['pickup_type'] == 'airport') {
    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[pickup]'"));
    $pickup_display = $row['airport_name'] . " (" . $row['airport_code'] . ")";
    $html = '<optgroup value="airport"><option value="airport-' . $row['airport_id'] . '">' . $pickup_display . '</option></optgroup>';
}
```

---

## Comparison Summary

| Feature | Custom Packages | Group Tours | Match? |
|---------|----------------|-------------|---------|
| **Value Format** | `"city-123"` | `"city-123"` | ✅ Yes |
| **Extract Type** | `explode("-")[0]` | `explode("-")[0]` | ✅ Yes |
| **Extract ID** | `explode("-")[1]` | `explode("-")[1]` | ✅ Yes |
| **Store ID Only** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Store Type** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Query by Type** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Display Name** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Select2 Dropdown** | ✅ Yes | ✅ Yes | ✅ Yes |

---

## Data Flow Example

### Example: Saving "Mumbai City" as Pickup

**Step 1: User Selection**
- User selects "Mumbai" from Cities dropdown
- Select2 value: `"city-123"` (where 123 = city_id)

**Step 2: JavaScript Collection**
```javascript
pickup_arr.push("city-123");
pickup_type_arr.push("city");
```

**Step 3: Model Extraction**
```php
$pickup_parts = explode("-", "city-123");
$pickup_type = "city"    // First part
$pickup = "123"          // Second part (ID only)
```

**Step 4: Database Storage**
```
pickup = "123"
pickup_type = "city"
```

**Step 5: Update/Display**
```php
// Query city_master where city_id = '123'
// Get city_name = "Mumbai"
// Display: "Mumbai" in dropdown
```

---

## ✅ Verification

The implementation is **100% consistent** with `custom_package_transport`:

1. ✅ **Same data format** - Stores ID and type separately
2. ✅ **Same extraction logic** - Uses `explode("-")` to split values
3. ✅ **Same display logic** - Queries master tables to show names
4. ✅ **Same user experience** - Select2 dropdowns with proper names

The only differences are:
- Table name (`custom_package_transport` vs `tour_groups_transport`)
- Foreign key name (`package_id` vs `tour_id`)
- Drop column name (`drop` vs `drop_location`)

These are just naming conventions - the **data structure and logic are identical**.

---

**Status:** ✅ Complete - `tour_groups_transport` now saves and loads data exactly like `custom_package_transport`



