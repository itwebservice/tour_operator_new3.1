# Generic Quotation Builder - Change Log & Recovery Checklist

A presentation-free, API-style data engine for Package Tour quotations, plus a
Company Profile activate/deactivate toggle. Any number of quotation template
designs can be built on top of one data source.

All paths below are relative to the project root:
`C:\xampp\htdocs\tour_operator_new3.1-niharika-per-person\tour_operator_new3.1-niharika-per-person\`

---

## 1. NEW FILES (create these)

| # | File | Purpose |
|---|------|---------|
| 1 | `crm/model/app_settings/print_html/quotation_html/generic_quotation_data.php` | Core engine. `get_generic_quotation_data($quotation_id)` returns the COMPLETE quotation data-set (all sections + loops + computed costing) in one array. No output, functions only. |
| 2 | `crm/model/app_settings/print_html/quotation_html/generic_builder_config.php` | Config store. Self-installs the `generic_builder_config` JSON column on `app_settings`. Holds active flag, section toggles, testimonials. Functions: `gqb_get_config()`, `gqb_save_config()`, `gqb_ensure_column()`, `gqb_default_config()`. |
| 3 | `crm/model/app_settings/print_html/quotation_html/quotation_data_api.php` | JSON HTTP endpoint that wraps the engine. Respects the activate/deactivate toggle. |
| 4 | `crm/view/app_settings/quotation_builder/index.php` | Company Profile -> "Quotation Builder" tab UI: activate switch, per-section checkboxes, testimonials editor, shows the API URL. |
| 5 | `crm/controller/app_settings/setting/quotation_builder_save.php` | Save controller for the toggle panel (writes the JSON config). |
| 6 | `crm/model/app_settings/print_html/quotation_html/quotation_html_7/index.php` | EXAMPLE template for the frontend developer. Shows how to consume the engine with zero DB queries. Copy this folder to make `quotation_html_8`, etc. |

## 2. EDITED FILES (modify these)

| # | File | What changed |
|---|------|--------------|
| 1 | `crm/view/app_settings/index.php` | Added a new radio tab `rd_quotation_builder` ("Quotation Builder") and an `if(id=="rd_quotation_builder")` block in `content_reflect()` that loads `quotation_builder/index.php`. |

## 3. DATABASE CHANGE

A column is added automatically on first use (no manual step needed). If the DB
user has no `ALTER` rights on production, run once:

```sql
ALTER TABLE `app_settings` ADD COLUMN `generic_builder_config` LONGTEXT NULL;
```

This single column stores all builder settings as JSON (active flag, section
visibility, testimonials).

---

## 4. HOW TO ACTIVATE (in the CRM)

Admin -> Company Profile -> **Quotation Builder** tab -> tick
**"Activate Generic Quotation Builder"** -> Save.

While OFF, the JSON API returns `data: null` (by design). Use `&force=1` to
bypass during testing.

## 5. HOW TO TEST (Postman / browser)

JSON API (GET or POST):

```
http://localhost/tour_operator_new3.1-niharika-per-person/tour_operator_new3.1-niharika-per-person/crm/model/app_settings/print_html/quotation_html/quotation_data_api.php?quotation_id=1&force=1
```

Example HTML template (renders a full quotation):

```
http://localhost/tour_operator_new3.1-niharika-per-person/tour_operator_new3.1-niharika-per-person/crm/model/app_settings/print_html/quotation_html/quotation_html_7/index.php?quotation_id=1
```

Params: `quotation_id` (required), `force=1` (optional, testing only).

---

## 6. API RESPONSE SHAPE (what the frontend dev consumes)

```jsonc
{
  "status": "ok",
  "builder_active": true,
  "config": { "active": true, "sections": { ... }, "testimonials": [ ... ] },
  "data": {
    "found": true,
    "quotation_id": "1",
    "quotation_code": "QTN/2026/1",
    "counts": { "hotels": 3, "flights": 0, "vehicles": 3, "itinerary": 0, "costing": 1, ... },
    "sections_present": { "hotels": true, "flights": false, ... },

    "hero": { "company_logo","cover_image","tour_name","quotation_code","duration_label","client_name","company_name","login_user","user_email_id","user_contact" },
    "tour_overview": { "destination","tour_id","quotation_date","customer_email","customer_mobile","duration_label","guest_count","package_type_label","pax{...}" },

    "hotels":      [ { "hotel_name","hotel_city","room_category","meal_plan","check_in","check_out","amenities","hotel_photo","rating" } ],
    "flights":     [ { "airline_name","airline_display","airline_logo","class","from_city","to_city","departure_datetime","arrival_datetime" } ],
    "trains":      [ { "from_location","to_location","class","from_date","to_date" } ],
    "cruises":     [ { "from_date","to_date","route","cabin","sharing_type" } ],
    "activities":  [ { "date","city_name","activity_name","transfer_type","vehicle_name","pax{...}" } ],
    "vehicles":    [ { "vehicle_name","description","date","vehicle_type","pickup","drop","vehicle_count" } ],
    "itinerary":   [ { "day_number","date","image","city","special_attraction","detailed_programme","meal_plan","overnight_stay" } ],

    "inclusion_exclusion": { "included","excluded","note","quot_note" },

    "costing": {
      "costing_type": "1|2",
      "costing_type_label": "Group|Per Person",
      "group":      [ raw mapping fields ],
      "per_person": [ raw mapping fields ],
      "computed": {
        "group":      [ { "package_type","tour_cost_display","tax_display","tcs_display","travel_display","total_display" } ],
        "per_person": [ { "package_type","pp_adult_display","pp_cwb_display","pp_cwnb_display","pp_infant_display","grand_total_display" } ],
        "grand_total": 54335, "grand_total_display": "INR 54,335.00"
      },
      "travel": { flight/train/cruise adult/child/infant }, "other": { visa/guide/misc/discount }
    },

    "bank_details":     { "bank_name","account_name","account_no","branch_name","ifsc_code","upi_id","qr_code" },
    "terms_conditions": { "title","terms_and_conditions" },
    "thank_you":        { "company_logo","company_name","company_address","company_email","company_contact","website","user_mobile","prepared_by","quotation_code","issue_date" },
    "testimonials":     [ { "name","designation","review","photo" } ],
    "raw_master":       { ...full package_tour_quotation_master row... }
  }
}
```

---

## 7. RULES FOR THE FRONTEND DEVELOPER

1. Never query the DB. Get everything from `get_generic_quotation_data($id)` (PHP)
   or `quotation_data_api.php` (JSON).
2. Loop the array sections (`hotels`, `flights`, `trains`, `cruises`, `activities`,
   `vehicles`, `itinerary`, `testimonials`). Use `sections_present` to show/hide,
   `counts` to know how many.
3. For money, use `costing.computed` and the `*_display` fields (currency/tax/TCS
   already calculated). Do NOT recalculate.
4. To add a new design = copy `quotation_html_7` to `quotation_html_8`, restyle the
   HTML only. The data lines (the 3 includes/call) never change.

## 8. OPTIONAL / NOT DONE YET

- Hooking `quotation_html_7` into the existing PDF/Word/Email buttons requires
  adding a `format == 7` branch in
  `crm/view/package_booking/quotation/home/quotation_list_reflect.php`
  (and the other `quotation_list_reflect.php` files). Not done yet - ask if needed.
- Bank details fall back to settings globals; if no active bank row exists they
  come back empty (same behavior as existing templates).

---

## 9. QUICK VERIFY (CLI)

From the `quotation_html` folder:

```
php -l generic_quotation_data.php
php -l generic_builder_config.php
php -l quotation_data_api.php
```

All should report "No syntax errors detected".
