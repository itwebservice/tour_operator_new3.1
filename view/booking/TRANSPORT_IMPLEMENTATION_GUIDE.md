# Transport Information in Booking Save - Implementation Guide

## ✅ **Completed Steps:**

### 1. **UI - Transport Section Added** ✅
**File:** `crm/view/booking/booking_save/tab_2/tab_2.php`
- Added Transport Details panel after Hotel Details
- Includes `transport_info.php`

### 2. **Transport Form Created** ✅
**File:** `crm/view/booking/booking_save/tab_2/transport_info.php`
- Dynamic table with Add/Delete row functionality
- Fields: Vehicle Name, Start Date, End Date, Pickup, Drop, Service Duration, Vehicles
- JavaScript for handling row operations

### 3. **Data Fetching Logic** ✅
**File:** `crm/view/booking/booking_save/tab_2/get_transport_info.php`
- Fetches transport data from `tour_groups_transport` when tour is selected
- Returns JSON with vehicle details, pickup/drop locations

### 4. **Auto-Population Logic** ✅
**File:** `crm/view/booking/js/tab_1_tour_info_sec.js`
- Added AJAX call in `tour_details_reflect()` function
- Populates transport table when tour group is selected
- Handles dynamic row creation

### 5. **Database Table Created** ✅
**File:** `crm/db/group_tour_booking_transport_entries.sql`
```sql
CREATE TABLE `group_tour_booking_transport_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `vehicle_name` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pickup` varchar(500) NOT NULL,
  `pickup_type` varchar(50) NOT NULL,
  `drop_location` varchar(500) NOT NULL,
  `drop_type` varchar(50) NOT NULL,
  `service_duration` varchar(50) DEFAULT NULL,
  `vehicle_count` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

---

## ⚠️ **Remaining Steps (Save/Update Logic):**

### 6. **Collect Transport Data in Tab 2**
You need to find the booking save controller and add transport data collection similar to train/plane/hotel data.

**Look for files like:**
- `crm/controller/booking/booking_save.php` OR
- `crm/model/booking/booking_save.php`

**Data to collect:**
```javascript
var transport_vehicle_arr = [];
var transport_start_date_arr = [];
var transport_end_date_arr = [];
var transport_pickup_arr = [];
var transport_pickup_type_arr = [];
var transport_drop_arr = [];
var transport_drop_type_arr = [];
var transport_service_duration_arr = [];
var transport_no_vehicles_arr = [];

// Loop through table rows
var table = document.getElementById("tbl_booking_transport");
for(var i=0; i<table.rows.length; i++){
  var row = table.rows[i];
  if(row.cells[0].childNodes[0].checked){
    transport_vehicle_arr.push(row.cells[2].childNodes[0].value);
    transport_start_date_arr.push(row.cells[3].childNodes[0].value);
    transport_end_date_arr.push(row.cells[4].childNodes[0].value);
    
    // Extract pickup location and type
    var pickup_val = row.cells[5].childNodes[0].value;
    var pickup_parts = pickup_val.split('-');
    transport_pickup_type_arr.push(pickup_parts[0]);
    transport_pickup_arr.push(pickup_parts[1]);
    
    // Extract drop location and type
    var drop_val = row.cells[6].childNodes[0].value;
    var drop_parts = drop_val.split('-');
    transport_drop_type_arr.push(drop_parts[0]);
    transport_drop_arr.push(drop_parts[1]);
    
    // Get service duration text
    var $serviceDuration = $('#' + row.cells[7].childNodes[0].id);
    var service_duration = $serviceDuration.find('option:selected').text();
    transport_service_duration_arr.push(service_duration);
    
    transport_no_vehicles_arr.push(row.cells[8].childNodes[0].value);
  }
}
```

### 7. **Backend Save Logic**

**In your booking save PHP file, add:**

```php
// Receive transport arrays
$transport_vehicle_arr = isset($_POST['transport_vehicle_arr']) ? $_POST['transport_vehicle_arr'] : [];
$transport_start_date_arr = isset($_POST['transport_start_date_arr']) ? $_POST['transport_start_date_arr'] : [];
$transport_end_date_arr = isset($_POST['transport_end_date_arr']) ? $_POST['transport_end_date_arr'] : [];
$transport_pickup_arr = isset($_POST['transport_pickup_arr']) ? $_POST['transport_pickup_arr'] : [];
$transport_pickup_type_arr = isset($_POST['transport_pickup_type_arr']) ? $_POST['transport_pickup_type_arr'] : [];
$transport_drop_arr = isset($_POST['transport_drop_arr']) ? $_POST['transport_drop_arr'] : [];
$transport_drop_type_arr = isset($_POST['transport_drop_type_arr']) ? $_POST['transport_drop_type_arr'] : [];
$transport_service_duration_arr = isset($_POST['transport_service_duration_arr']) ? $_POST['transport_service_duration_arr'] : [];
$transport_no_vehicles_arr = isset($_POST['transport_no_vehicles_arr']) ? $_POST['transport_no_vehicles_arr'] : [];

// Call save function
transport_entries_save($booking_id, $transport_vehicle_arr, $transport_start_date_arr, $transport_end_date_arr, 
                       $transport_pickup_arr, $transport_pickup_type_arr, $transport_drop_arr, $transport_drop_type_arr,
                       $transport_service_duration_arr, $transport_no_vehicles_arr);
```

**Save Function:**
```php
function transport_entries_save($booking_id, $transport_vehicle_arr, $transport_start_date_arr, $transport_end_date_arr, 
                                 $transport_pickup_arr, $transport_pickup_type_arr, $transport_drop_arr, $transport_drop_type_arr,
                                 $transport_service_duration_arr, $transport_no_vehicles_arr) {
    
    for($i=0; $i<sizeof($transport_vehicle_arr); $i++){
        
        $vehicle_name = mysqlREString($transport_vehicle_arr[$i]);
        $start_date = get_date_db($transport_start_date_arr[$i]);
        $end_date = get_date_db($transport_end_date_arr[$i]);
        $pickup = mysqlREString($transport_pickup_arr[$i]);
        $pickup_type = mysqlREString($transport_pickup_type_arr[$i]);
        $drop_location = mysqlREString($transport_drop_arr[$i]);
        $drop_type = mysqlREString($transport_drop_type_arr[$i]);
        $service_duration = mysqlREString($transport_service_duration_arr[$i]);
        $vehicle_count = mysqlREString($transport_no_vehicles_arr[$i]);
        
        $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from group_tour_booking_transport_entries"));
        $id = $sq_max['max'] + 1;
        
        $sq_transport = mysqlQuery("insert into group_tour_booking_transport_entries 
                                    (id, booking_id, vehicle_name, start_date, end_date, pickup, pickup_type, 
                                     drop_location, drop_type, service_duration, vehicle_count) 
                                    values 
                                    ('$id','$booking_id','$vehicle_name', '$start_date', '$end_date', '$pickup', 
                                     '$pickup_type', '$drop_location', '$drop_type', '$service_duration', '$vehicle_count')");
        
        if(!$sq_transport){
            echo "error--Sorry, Transport details not saved!";
            exit;
        }
    }
}
```

### 8. **Update Logic (Similar to Save)**

For booking update, add similar logic but with entry_id tracking:

```php
function transport_entries_update($booking_id, $transport_vehicle_arr, ..., $transport_entry_id_arr) {
    
    // First delete existing entries
    mysqlQuery("delete from group_tour_booking_transport_entries where booking_id='$booking_id'");
    
    // Then insert all entries (same as save)
    // OR use UPDATE if entry_id exists, INSERT if not
    for($i=0; $i<sizeof($transport_vehicle_arr); $i++){
        // ... similar to save logic
    }
}
```

---

## 🎯 **How It Works:**

### **Flow:**
```
1. User selects Tour Name → Tour Group
   ↓
2. tour_details_reflect() called
   ↓
3. AJAX call to get_transport_info.php
   ↓
4. Transport data from tour_groups_transport fetched
   ↓
5. Table populated with vehicle, pickup, drop locations
   ↓
6. User fills Service Duration & Vehicle Count
   ↓
7. User can Add/Delete rows
   ↓
8. On Save: Transport data collected and sent to backend
   ↓
9. Backend saves to group_tour_booking_transport_entries
```

---

## 📋 **Testing Checklist:**

- [ ] Transport section appears in Tab 2
- [ ] When tour is selected, transport data populates
- [ ] Add Row button adds new transport row
- [ ] Delete Row button removes selected rows
- [ ] Pickup/Drop dropdowns work with Select2
- [ ] Date pickers work correctly
- [ ] Data saves to `group_tour_booking_transport_entries` table
- [ ] Update booking shows saved transport data
- [ ] Transport data can be edited and re-saved

---

## 🚀 **Next Steps:**

1. **Run the SQL** to create the table:
   ```bash
   mysql -u username -p database_name < crm/db/group_tour_booking_transport_entries.sql
   ```

2. **Find the booking save controller** and add transport data collection

3. **Test the complete flow** from tour selection to save

4. **Implement update logic** if needed

---

**Most of the UI and auto-population is complete! Just need to connect the save/update backend logic.** 🎉



