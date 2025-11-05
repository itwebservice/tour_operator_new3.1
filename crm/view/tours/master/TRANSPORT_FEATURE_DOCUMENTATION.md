# Transport Information Feature Documentation

## Overview
Added comprehensive transport information management to Group Tour Master (both Save and Update functionality). This feature allows users to add vehicle transport details including pickup and drop-off locations for each tour.

---

## Database Table

### Table: `tour_groups_transport`

```sql
CREATE TABLE tour_groups_transport (
    entry_id INT(11) NOT NULL AUTO_INCREMENT,
    tour_id INT(11) NOT NULL,
    vehicle_name VARCHAR(255) NOT NULL,
    pickup VARCHAR(500) NOT NULL,
    pickup_type VARCHAR(50) NOT NULL,
    drop_location VARCHAR(500) NOT NULL,
    drop_type VARCHAR(50) NOT NULL,
    PRIMARY KEY (entry_id)
);
```

---

## Files Modified/Created

### ✅ SAVE Functionality

#### 1. **View Layer - UI**
- **File**: `crm/view/tours/master/save/travelling_tab2.php`
- **Changes**:
  - Added new "Transport Information" accordion section after Hotel Information
  - Includes table `tbl_package_tour_transport` with columns:
    - Checkbox (selection)
    - Sr No
    - Vehicle Name (dropdown from `b2b_transfer_master`)
    - Pickup Location (destination dropdown)
    - Drop-off Location (destination dropdown)
  - Added buttons: Add Vehicle, Add Airport, Add/Delete rows
  - Added JavaScript validation to collect transport data
  - Validates mandatory fields (vehicle, pickup, drop location)

#### 2. **JavaScript - Data Collection**
- **File**: `crm/view/tours/js/master.js`
- **Changes**:
  - Added transport data collection logic
  - Creates arrays: `vehicle_name_arr`, `pickup_arr`, `pickup_type_arr`, `drop_arr`, `drop_type_arr`
  - Validates that vehicle name is provided
  - Extracts pickup/drop location types from optgroup values
  - Includes transport data in Ajax POST to controller
  - Added safety check with `if(table)` to handle cases where transport table doesn't exist

#### 3. **Controller**
- **File**: `crm/controller/group_tour/tours/tour_master_save.php`
- **Changes**:
  - Added POST parameters:
    ```php
    $vehicle_name_arr = isset($_POST['vehicle_name_arr']) ? $_POST['vehicle_name_arr'] : [];
    $pickup_arr = isset($_POST['pickup_arr']) ? $_POST['pickup_arr'] : [];
    $pickup_type_arr = isset($_POST['pickup_type_arr']) ? $_POST['pickup_type_arr'] : [];
    $drop_arr = isset($_POST['drop_arr']) ? $_POST['drop_arr'] : [];
    $drop_type_arr = isset($_POST['drop_type_arr']) ? $_POST['drop_type_arr'] : [];
    ```
  - Passed parameters to `tour_master_save()` model function

#### 4. **Model - Business Logic**
- **File**: `crm/model/group_tour/tours_master.php`
- **Changes**:
  - Updated `tour_master_save()` function signature with transport parameters
  - Added call to `$this->transport_entries_save()` after hotel entries
  - Created new method `transport_entries_save()`:
    ```php
    public function transport_entries_save($max_tour_id,$vehicle_name_arr,$pickup_arr,$pickup_type_arr,$drop_arr,$drop_type_arr)
    ```
  - Loops through transport arrays and inserts into `tour_groups_transport` table
  - Includes proper error handling

---

### ✅ UPDATE Functionality

#### 1. **View Layer - UI**
- **File**: `crm/view/tours/master/update/travelling_tab2.php`
- **Changes**:
  - Added new "Transport Information" accordion section after Hotel Information
  - Includes reference to `transport_tbl.php`

- **File**: `crm/view/tours/master/update/transport_tbl.php` (NEW)
- **Purpose**: Loads existing transport data for update
- **Features**:
  - If no transport data exists: Shows empty row for adding new transport
  - If transport data exists: Loads all existing transport records
  - Each row includes:
    - Disabled checkbox (checked)
    - Sr No
    - Vehicle dropdown (pre-selected with existing vehicle)
    - Pickup location (pre-selected with existing pickup + type in optgroup)
    - Drop location (pre-selected with existing drop + type in optgroup)
    - Hidden field for transport_entry_id (for update tracking)
  - Initializes Select2 for location dropdowns

#### 2. **JavaScript - Data Collection**
- **File**: `crm/view/tours/master/update/costing_tab3.php`
- **Changes**:
  - Added transport data collection before cruise information
  - Creates arrays including `transport_entry_id_arr` for tracking existing records
  - Validates that vehicle name is provided
  - Extracts pickup/drop location types from optgroup values
  - Includes transport data in Ajax POST to controller

#### 3. **Controller**
- **File**: `crm/controller/group_tour/tours/tour_master_update.php`
- **Changes**:
  - Added POST parameters:
    ```php
    $vehicle_name_arr = isset($_POST['vehicle_name_arr']) ? $_POST['vehicle_name_arr'] : [];
    $pickup_arr = isset($_POST['pickup_arr']) ? $_POST['pickup_arr'] : [];
    $pickup_type_arr = isset($_POST['pickup_type_arr']) ? $_POST['pickup_type_arr'] : [];
    $drop_arr = isset($_POST['drop_arr']) ? $_POST['drop_arr'] : [];
    $drop_type_arr = isset($_POST['drop_type_arr']) ? $_POST['drop_type_arr'] : [];
    $transport_entry_id_arr = isset($_POST['transport_entry_id_arr']) ? $_POST['transport_entry_id_arr'] : [];
    ```
  - Passed parameters to `tour_master_update()` model function

#### 4. **Model - Business Logic**
- **File**: `crm/model/group_tour/tours_master.php`
- **Changes**:
  - Updated `tour_master_update()` function signature with transport parameters
  - Added call to `$this->transport_entries_update()` after hotel entries
  - Created new method `transport_entries_update()`:
    ```php
    public function transport_entries_update($tour_id,$vehicle_name_arr,$pickup_arr,$pickup_type_arr,$drop_arr,$drop_type_arr,$transport_entry_id_arr)
    ```
  - Logic:
    - If `transport_entry_id` exists: Updates existing record
    - If `transport_entry_id` is empty: Inserts new record
  - Includes proper error handling

---

## How to Use

### Adding Transport Information (Save)

1. Navigate to: **CRM → Group Tour → Tour Master → Add New**
2. Fill in Tour Information (Tab 1)
3. Go to **Travelling Tab (Tab 2)**
4. Expand **Transport Information** accordion
5. Click "Add Row" to add more transport entries
6. For each row:
   - Select Vehicle from dropdown
   - Select Pickup Location (Airport/Hotel/City/Location)
   - Select Drop-off Location (Airport/Hotel/City/Location)
7. Continue with other tabs and save

### Updating Transport Information (Update)

1. Navigate to: **CRM → Group Tour → Tour Master → List**
2. Click "View" or "Edit" on a tour
3. Go to **Travelling Tab (Tab 2)**
4. Expand **Transport Information** accordion
5. Existing transport records will be pre-loaded
6. Modify existing records or add new rows
7. Click "Update" to save changes

---

## Features

### Data Validation
- Vehicle Name is mandatory
- Pickup Location validation (optional but recommended)
- Drop-off Location validation (optional but recommended)
- Prevents form submission if validation fails

### Dynamic Dropdowns
- Vehicle dropdown from `b2b_transfer_master` table
- Pickup/Drop locations loaded using `destinationLoading()` function
- Supports grouped locations (Airport, Hotel, City, Location)
- Select2 integration for better UX

### Error Handling
- Display error messages if transport data fails to save
- Transaction rollback on save errors
- User-friendly error messages with row numbers

---

## Technical Details

### JavaScript Functions Used
- `addRow()` - Adds new row to transport table
- `deleteRow()` - Removes selected row from transport table
- `destinationLoading()` - Loads pickup/drop locations dynamically
- `vehicle_save_modal()` - Opens modal to add new vehicle
- `airport_airline_save_modal()` - Opens modal to add new airport

### Location Type Detection
Pickup and drop locations are stored with their type (extracted from optgroup):
- **Airport** - Airport locations
- **Hotel** - Hotel locations
- **City** - City locations
- **Location** - Other locations

The type is captured using:
```javascript
pickup_type = $("option:selected", $("#" + row.cells[3].childNodes[0].id)).parent().attr('value');
```

---

## Integration Points

### Related Tables
- `b2b_transfer_master` - Vehicle master data
- `tour_master` - Tour basic information
- `tour_groups` - Tour group dates and capacity
- `tour_groups_transport` - Transport information (newly used)

### Related Functions
- `mysqlREString()` - SQL string sanitization
- `mysqlQuery()` - Database query execution
- `begin_t()` / `commit_t()` / `rollback_t()` - Transaction management

---

## Testing Checklist

- [ ] Create new tour with transport information
- [ ] Create new tour without transport information (optional)
- [ ] Update existing tour - add new transport
- [ ] Update existing tour - modify existing transport
- [ ] Update existing tour - add additional transport rows
- [ ] Verify data is saved correctly in `tour_groups_transport` table
- [ ] Check that vehicle dropdown loads correctly
- [ ] Test pickup/drop location dropdowns
- [ ] Verify error messages display correctly
- [ ] Test form validation

---

## Future Enhancements

1. Add ability to delete individual transport records during update
2. Add cost field for transport entries
3. Link transport to specific tour groups/dates
4. Add transport vendor information
5. Generate transport-specific reports

---

## Support

For any issues or questions regarding this feature, refer to:
- Similar implementation in Custom Package module: `crm/view/custom_packages/master/package/save_modal.php`
- Transport table structure: `crm/db/modification.sql` (line 162)

---

**Feature Implemented By**: AI Assistant  
**Date**: November 4, 2025  
**Status**: ✅ Complete (Both Save and Update)



