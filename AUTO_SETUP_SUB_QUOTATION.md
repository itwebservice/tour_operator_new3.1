# Automatic Database Setup System

## Overview
The system now automatically sets up both sub-quotation and itinerary image functionality when users log in. This ensures that all necessary tables, fields, and indexes are created without manual intervention.

## How It Works

### 1. Automatic Setup on Login
When any user logs in (Admin, B2B, or Customer), the system automatically:
- Checks if sub-quotation fields exist in the database
- Checks if itinerary image fields exist in the database
- Creates missing fields and indexes if needed
- Logs the setup process for monitoring
- Continues with normal login process

### 2. Login Points Covered
The automatic setup runs in these login files:
- **Admin Login**: `/crm/controller/login/login_verify.php`
- **B2B Login**: `/crm/controller/b2b_customer/login/login_verify.php`
- **Customer Login**: `/view/flight/login.php`

### 3. Setup Process
The setup process includes:
1. **Sub-Quotation Fields**: Adds `is_sub_quotation`, `parent_quotation_id`, `quotation_id_display` fields
2. **Itinerary Image Fields**: Adds `itinerary_image` and `day_image` fields to relevant tables
3. **Index Creation**: Adds performance indexes for better query performance
4. **Verification**: Checks that all fields and indexes were created successfully
5. **Logging**: Records setup success/failure in error logs

## Files Created/Modified

### New Files
1. **Setup Class**: `/crm/model/database_setup/sub_quotation_setup.php`
   - Contains all database setup logic
   - Handles field creation, index creation, and verification
   - Provides status checking and statistics

2. **Status Page**: `/crm/view/database_setup/sub_quotation_status.php`
   - Admin interface to check system status
   - Shows database structure and statistics
   - Allows manual setup if needed

3. **Manual Setup Handler**: `/crm/view/database_setup/sub_quotation_setup.php`
   - Handles manual setup requests
   - Provides feedback on setup results

### Modified Files
1. **Admin Login**: `/crm/controller/login/login_verify.php`
   - Added automatic setup call after successful login
   - Includes error handling and logging

2. **B2B Login**: `/crm/controller/b2b_customer/login/login_verify.php`
   - Added automatic setup call after successful B2B login
   - Includes error handling and logging

3. **Customer Login**: `/view/flight/login.php`
   - Added automatic setup call after successful customer login
   - Includes error handling and logging

## Database Changes Applied Automatically

### 1. Sub-Quotation Table Alterations
```sql
-- Add sub-quotation tracking fields
ALTER TABLE package_tour_quotation_master 
ADD COLUMN is_sub_quotation ENUM('0','1') DEFAULT '0' AFTER quotation_id,
ADD COLUMN parent_quotation_id INT(11) DEFAULT NULL AFTER is_sub_quotation,
ADD COLUMN quotation_id_display VARCHAR(50) DEFAULT NULL AFTER parent_quotation_id;
```

### 2. Itinerary Image Table Alterations
```sql
-- Add itinerary image fields
ALTER TABLE itinerary_master 
ADD COLUMN itinerary_image VARCHAR(500) NULL DEFAULT NULL AFTER overnight_stay;

ALTER TABLE custom_package_program 
ADD COLUMN day_image VARCHAR(255) NULL DEFAULT NULL AFTER meal_plan;

ALTER TABLE package_quotation_program 
ADD COLUMN day_image VARCHAR(255) NULL DEFAULT NULL AFTER meal_plan;
```

### 3. Index Creation
```sql
-- Add sub-quotation performance indexes
ALTER TABLE package_tour_quotation_master 
ADD INDEX idx_is_sub_quotation (is_sub_quotation),
ADD INDEX idx_parent_quotation_id (parent_quotation_id),
ADD INDEX idx_quotation_sub_quotation (is_sub_quotation, parent_quotation_id);

-- Add itinerary image performance indexes
ALTER TABLE itinerary_master 
ADD INDEX idx_itinerary_image (itinerary_image);

ALTER TABLE custom_package_program 
ADD INDEX idx_day_image (day_image);

ALTER TABLE package_quotation_program 
ADD INDEX idx_day_image (day_image);
```

## Monitoring and Status

### 1. Error Logs
All setup activities are logged to the PHP error log:
- Successful setup: `Sub-quotation database setup completed successfully for user: [username]`
- Failed setup: `Warning: Sub-quotation database setup failed for user: [username]`
- Errors: `Error during sub-quotation setup: [error_message]`

### 2. Status Page
Access the status page at: `/crm/view/database_setup/sub_quotation_status.php`
- Shows system readiness status
- Displays database structure information
- Provides statistics on quotations
- Allows manual setup if needed

### 3. System Status Check
The system automatically checks:
- Field existence
- Index existence
- Data integrity
- Overall system readiness

## Benefits

### 1. Zero Manual Intervention
- No need to run SQL scripts manually
- Automatic setup on first login
- Works for all user types

### 2. Safe and Reliable
- Checks before creating fields/indexes
- Won't duplicate existing structures
- Graceful error handling

### 3. Monitoring
- Complete logging of setup process
- Status page for administrators
- Statistics and health checks

### 4. Performance
- Creates necessary indexes for better performance
- Optimized queries for sub-quotation operations

## Usage

### For Administrators
1. **Check Status**: Visit `/crm/view/database_setup/sub_quotation_status.php`
2. **Monitor Logs**: Check error logs for setup activities
3. **Manual Setup**: Use the manual setup button if needed

### For Developers
1. **Setup Class**: Use `sub_quotation_setup` class for programmatic setup
2. **Status Check**: Use `get_system_status()` method to check readiness
3. **Statistics**: Use `get_setup_stats()` method to get usage statistics

## Error Handling

### 1. Setup Failures
- Logged to error log
- Login process continues normally
- Manual setup available as fallback

### 2. Field Conflicts
- Checks for existing fields before creation
- Skips creation if fields already exist
- Logs warnings for duplicate attempts

### 3. Index Conflicts
- Handles existing indexes gracefully
- Logs warnings but continues setup
- Won't fail if indexes already exist

## Security Considerations

### 1. Database Permissions
- Requires ALTER TABLE permissions
- Requires CREATE INDEX permissions
- Uses existing database connection

### 2. Error Information
- Sensitive information not exposed in error messages
- Detailed errors logged to error log only
- User-friendly messages for status page

## Troubleshooting

### 1. Setup Not Running
- Check if login files are properly modified
- Verify include paths are correct
- Check error logs for PHP errors

### 2. Setup Failing
- Check database permissions
- Verify table exists (`package_tour_quotation_master`)
- Check error logs for specific error messages

### 3. Status Page Not Working
- Verify file permissions
- Check include paths
- Ensure database connection is working

## Future Enhancements

### 1. Version Control
- Track setup versions
- Handle database migrations
- Update existing structures

### 2. Advanced Monitoring
- Real-time status updates
- Performance metrics
- Usage analytics

### 3. Automated Testing
- Test setup process
- Validate database structure
- Check system functionality

## Conclusion

The automatic sub-quotation database setup ensures that the system is always ready to use without manual intervention. It provides a seamless experience for users while maintaining data integrity and system performance.

For any issues or questions, check the error logs and status page, or contact the development team.
