# Inventory Report

## Overview
The Inventory Report displays tour capacity, booked seats, and available seats for all group tours. This report helps track tour availability and booking status.

## Features

### Report Columns
1. **Sr No** - Serial number
2. **Destination** - Destination name (from tour master or destination_master)
3. **From Date** - Tour start date (dd-mm-yyyy format)
4. **To Date** - Tour end date (dd-mm-yyyy format)
5. **Total Capacity** - Maximum seats available for the tour group
6. **Booked Seats** - Number of seats already booked (excluding cancelled bookings)
7. **Available Seats** - Remaining seats available (Total Capacity - Booked Seats)

### Filters Available
1. **Tour Name** - Filter by specific tour (dropdown with all active tours)
2. **Tour Date** - Filter by specific tour group/date (dynamically loaded based on selected tour)
3. **Status** - Filter by Active/Inactive status
4. **Proceed Button** - Apply filters and generate report

### Color Coding
- **Red (Danger)** - Fully booked (Available Seats = 0)
- **Orange (Warning)** - Almost full (Available Seats ≤ 5)
- **Default** - Adequate availability

### Excel Export
- Click the Excel button to export the report to a professional Excel file
- Uses PHPExcel library for proper Excel formatting
- File name format: `Inventory Report(DD-MM-YYYY HH:ii).xls`
- Includes:
  - Report header with filter information (Report Name, Tour Name, Tour Date, Status)
  - Formatted table headers with borders and background colors
  - Color-coded rows (Light Red = Fully booked, Light Orange = Almost full)
  - Total summary row at the bottom
  - Auto-sized columns for better readability
- Includes all applied filters

## How to Access

1. Navigate to: **CRM → Reports → Group Tour → Inventory Report**
2. Select filters as needed (optional - leave blank for all tours)
3. Click "Proceed" to generate the report
4. Use Excel button to export the report

## Database Tables Used

1. **tour_master** - Tour information and destination details
2. **tour_groups** - Tour group dates, capacity, and status
3. **tourwise_traveler_details** - Booking records
4. **travelers_details** - Individual traveler information
5. **destination_master** - Destination names (optional)

## Booked Seats Calculation

The report uses the existing `tour_booked_seats` class to calculate booked seats:
- Counts active bookings only
- Excludes cancelled tours and cancelled travelers
- Handles both CRM bookings and B2C/B2B bookings

## File Structure

```
crm/view/reports/reports_content/group_tour/inventory_report/
├── index.php              # Main report page with filters and UI
├── inventory_report.php   # Backend logic to fetch and process data
├── export_excel.php       # Excel export functionality
└── README.md             # This documentation file
```

## Technical Notes

- Uses DataTables for pagination and sorting
- Implements Select2 for enhanced dropdown selection
- Real-time dynamic tour group loading based on selected tour
- Responsive design compatible with mobile devices
- Follows existing report structure and coding standards

## Support

For any issues or enhancements, contact the development team.

