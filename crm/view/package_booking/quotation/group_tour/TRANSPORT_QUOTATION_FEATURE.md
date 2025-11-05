# Group Tour Quotation - Transport Information Feature

## Overview
Added transport information functionality to Group Tour Quotations. When a tour is selected, transport details from the tour master are automatically populated. Users can also manually add/modify transport information.

---

## Features

### ✅ Auto-Population from Tour Master
When a user selects a Group Tour:
- System queries `tour_groups_transport` table for that tour
- Automatically populates transport rows with:
  - Vehicle Name
  - Pickup Location (shows actual name: city/hotel/airport)
  - Drop Location (shows actual name: city/hotel/airport)
- If no transport data exists in tour master, shows empty row (blank fields)

### ✅ Manual Entry
Users can:
- Add new transport rows
- Modify auto-populated transport data
- Select different vehicles, pickups, and drops
- Remove transport rows

### ✅ Data Storage
Transport data is saved to `group_tour_quotation_transport_entries` table with same format as tour master.

---

## Files Modified/Created

### Created Files:
1. ✅ `crm/view/package_booking/quotation/group_tour/save/get_transport_info.php` - Fetch transport from tour master

### Modified Files:

#### View Layer:
1. ✅ `crm/view/package_booking/quotation/group_tour/save/tab3.php` 
   - Added Transport Information section after Hotel Information
   - Added transport table with vehicle, pickup, drop columns
   - Added buttons to add vehicle and manage rows

#### JavaScript:
2. ✅ `crm/view/package_booking/quotation/group_tour/save/tab2.php`
   - Added Ajax call to fetch transport info when tour is selected
   - Populates transport table with fetched data
   - Initializes Select2 dropdowns

3. ✅ `crm/view/package_booking/quotation/group_tour/save/tab4.php`
   - Added transport data collection logic
   - Collects: vehicle, pickup, pickup_type, drop, drop_type
   - Included in quotation save Ajax POST

#### Controller & Model:
4. ✅ `crm/model/package_tour/quotation/group_tour/quotation_save.php`
   - Added POST parameters for transport arrays
   - Added `transport_entries_save()` method
   - Extracts ID from prefixed values ("city-123" → "123")
   - Inserts into `group_tour_quotation_transport_entries`

#### Database:
5. ✅ `crm/db/modification.sql`
   - Added `group_tour_quotation_transport_entries` table structure

---

## Database Tables

### Source Table: `tour_groups_transport`
Stores transport information for tour master:
```sql
tour_id, vehicle_name, pickup, pickup_type, drop_location, drop_type
```

### Destination Table: `group_tour_quotation_transport_entries`
```sql
CREATE TABLE group_tour_quotation_transport_entries (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    quotation_id INT(11) NOT NULL,
    vehicle_name VARCHAR(255) NOT NULL,
    pickup VARCHAR(500) NOT NULL,
    pickup_type VARCHAR(50) NOT NULL,
    drop_location VARCHAR(500) NOT NULL,
    drop_type VARCHAR(50) NOT NULL
);
```

---

## Data Flow

### 1. Tour Selection → Auto-Population

```
User selects Tour in tab2
  ↓
seats_availability_reflect() called
  ↓
Ajax: get_transport_info.php (tour_id)
  ↓
Queries: tour_groups_transport WHERE tour_id
  ↓
For each transport:
  - Fetches vehicle name from b2b_transfer_master
  - Fetches pickup location from respective master (city/hotel/airport)
  - Fetches drop location from respective master (city/hotel/airport)
  ↓
Returns JSON array with:
  {vehicle_id, vehicle_name, pickup_value, pickup_location, drop_value, drop_location}
  ↓
JavaScript populates table rows with Select2 options
```

### 2. Quotation Save

```
User fills quotation → Clicks Save (tab4)
  ↓
JavaScript collects transport data:
  - transport_vehicle_arr
  - transport_pickup_arr (with prefix: "city-123")
  - transport_pickup_type_arr
  - transport_drop_arr (with prefix: "hotel-456")
  - transport_drop_type_arr
  ↓
Ajax POST to quotation_save.php
  ↓
Model: transport_entries_save()
  - Extracts ID from value ("city-123" → "123")
  - Stores ID and type separately
  ↓
INSERT into group_tour_quotation_transport_entries
```

---

## How to Use

### Creating Quotation with Transport:

1. Navigate to: **CRM → Quotation → Group Tour → New Quotation**
2. **Tab 1**: Select customer/enquiry
3. **Tab 2**: Select Tour and Tour Date
   - System automatically fetches transport from tour master
4. **Tab 3**: Review Travel And Stay
   - Expand **Transport Information**
   - Auto-populated transport rows appear (if configured in tour master)
   - Add more rows or modify as needed
5. **Tab 4**: Review costing and Save

### If Tour Has No Transport:
- Empty row is shown with blank dropdowns
- User can manually select vehicle, pickup, and drop locations
- Or leave unchecked if transport not required

---

## Code Examples

### Auto-Population JavaScript (tab2.php):
```javascript
//Transport Info
$.ajax({
    type:'post',
    url: '../group_tour/save/get_transport_info.php',
    data:{ tour_id : tour_id },
    success:function(result){
        var transport_arr = JSON.parse(result);
        
        // Populate rows
        for(var i=0; i<transport_arr.length; i++){
            var row = table.rows[i];
            row.cells[2].childNodes[0].value = transport_arr[i]['vehicle_id'];
            
            // Set pickup with Select2
            var pickupOption = new Option(
                transport_arr[i]['pickup_location'], 
                transport_arr[i]['pickup_value'], 
                true, true
            );
            $('#'+row.cells[3].childNodes[0].id).append(pickupOption);
        }
    }
});
```

### Save Method (quotation_save.php):
```php
public function transport_entries_save($quotation_id, $transport_vehicle_arr, ...) {
    for($i=0; $i<sizeof($transport_vehicle_arr); $i++){
        // Extract ID from "city-123" format
        if(strpos($transport_pickup_arr[$i], '-') !== false){
            $pickup_parts = explode("-", $transport_pickup_arr[$i]);
            $pickup_type = $pickup_parts[0];  // "city"
            $pickup = $pickup_parts[1];       // "123"
        }
        
        // Insert into database
        $sq_transport = mysqlQuery("INSERT INTO group_tour_quotation_transport_entries ...");
    }
}
```

---

## Testing Checklist

- [ ] Select tour with transport configured in tour master
- [ ] Verify transport auto-populates with vehicle, pickup, drop names
- [ ] Modify auto-populated transport
- [ ] Add additional transport row
- [ ] Save quotation and verify data in `group_tour_quotation_transport_entries` table
- [ ] Select tour without transport
- [ ] Verify empty row shows (unchecked)
- [ ] Manually add transport
- [ ] Save and verify

---

## Key Features

✅ **Auto-Population** - Fetches from `tour_groups_transport` when tour selected  
✅ **Blank Fields** - Shows empty row if no transport configured in tour  
✅ **Manual Entry** - Users can add/modify transport  
✅ **Location Names** - Displays actual names (not IDs)  
✅ **Data Consistency** - Same format as tour master transport  
✅ **Validation** - Optional (no mandatory validation)  
✅ **Error Handling** - Proper error messages  

---

## Database Relationships

```
tour_master
    ↓ (tour_id)
tour_groups_transport
    ↓ (fetched during quotation)
    ↓ (auto-populated)
group_tour_quotation_transport_entries
```

---

## Benefits

1. **Time Saving** - Auto-populates from tour master
2. **Consistency** - Transport from tour master by default
3. **Flexibility** - Can modify or add custom transport
4. **User Friendly** - Shows readable names instead of IDs
5. **Optional** - Not mandatory, can leave blank

---

**Status:** ✅ Complete - Group Tour Quotation transport feature fully implemented!



