# Transport Information - Update Implementation Notes

## How Transport Data is Saved and Loaded

### Data Format

When users select locations from dropdowns, values are stored in format:
```
city-123
hotel-456  
airport-789
```

Where:
- First part = Type (city, hotel, airport)
- Second part = ID from the respective master table

### Save Process

**JavaScript sends:**
```javascript
pickup_arr[i] = "city-123"      // Full value with prefix
pickup_type_arr[i] = "city"     // Type from optgroup
drop_arr[i] = "hotel-456"       // Full value with prefix  
drop_type_arr[i] = "hotel"      // Type from optgroup
```

**Model extracts:**
```php
if(strpos($pickup_arr[$i], '-') !== false){
  $pickup_parts = explode("-", $pickup_arr[$i]);
  $pickup_type = $pickup_parts[0];  // "city"
  $pickup = $pickup_parts[1];       // "123" (just ID)
}
```

**Database stores:**
```
pickup = "123" (ID only)
pickup_type = "city"
drop_location = "456" (ID only)
drop_type = "hotel"
```

### Load Process (Update)

**PHP loads from database:**
```php
// Query based on type
if ($row_transport['pickup_type'] == 'city') {
    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[pickup]'"));
    $html = '<optgroup value="city" label="City Name">
              <option value="city-' . $row['city_id'] . '">' . $row['city_name'] . '</option>
            </optgroup>';
}
```

**Display shows:**
- User sees: "Mumbai" (city name)
- Select value: "city-123" (with prefix)
- Optgroup value: "city" (type)

### Update Process

When user submits update:
1. JavaScript extracts value ("city-123") and type ("city" from optgroup)
2. Controller passes arrays to model
3. Model splits value to extract ID ("123")
4. Database stores ID and type separately

## Supported Location Types

1. **City** → `city_master` table
2. **Hotel** → `hotel_master` table  
3. **Airport** → `airport_master` table
4. **Other** → Stored as-is (for future expansion)

## Key Features

✅ **Automatic Type Detection** - Extracts type from value prefix
✅ **Fallback Support** - Uses type_arr if prefix not found
✅ **Proper Display** - Shows human-readable names in update form
✅ **Select2 Integration** - AJAX search for locations
✅ **Preserved Values** - Saved values persist after Select2 initialization

## Files Updated

1. `transport_tbl.php` - Loads transport data with proper location names
2. `tours_master.php` - Extracts ID from value with prefix (both save and update)
3. `travelling_tab2.php` - Validates and collects data with type fallback
4. `costing_tab3.php` - Validates and collects data with type fallback

## Testing

Test these scenarios:
- [ ] Save tour with City pickup → City drop
- [ ] Save tour with Hotel pickup → Airport drop
- [ ] Save tour with Airport pickup → Hotel drop
- [ ] Update and verify locations display correctly
- [ ] Modify location and update
- [ ] Add new transport row in update and save

## Expected Behavior

**Save:**
```
User selects: "Mumbai" from City dropdown
JavaScript sends: pickup = "city-123", pickup_type = "city"
Model saves: pickup = "123", pickup_type = "city"
```

**Update:**
```
Database has: pickup = "123", pickup_type = "city"
PHP queries: city_master where city_id='123'
Display shows: "Mumbai" in dropdown
User sees: Mumbai (editable via Select2)
```

---

**Implementation Complete**: Transport data now saves correctly and loads properly in update form, matching the custom packages behavior.



