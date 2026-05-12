<?php
class GeminiController
{
    private $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=AIzaSyC9diotLu1KA4NvNz5vzh6a1HD-nIppOFY';
    
    private $promptTemplate = 
        'You are an AI that extracts structured travel itinerary data from raw, unstructured text.
        The input will be messy and may include:

        * Broken formatting
        * Mixed sections (itinerary, pricing, remarks, policies, terms & conditions)
        * Irrelevant or noisy text
        * Missing labels or merged fields

        Your job is to:
        * Clean and understand the text
        * Extract only relevant travel data
        * Normalize it into strict JSON format

        IMPORTANT:
        * Do NOT return code
        * Do NOT explain anything
        * Do NOT wrap in markdown
        * Return ONLY valid JSON

        CRITICAL VALIDATION RULE:
        If the input text does NOT contain travel-related information (such as itinerary, destinations, hotels, transport, dates, or trip details), OR the content is unrelated (e.g., random text, technical content, chat, etc.) then return EXACTLY:
        {"Error":"Invalid input: content is not related to travel itinerary data"}

        CRITICAL OUTPUT RULES:
        * Output MUST be valid JSON
        * Output MUST start with {
        * Output MUST end with }
        * Do NOT include ``` or json or explanations
        * Do NOT return partial output
        * Do NOT return empty structure if input is invalid
        * Return ONLY valid JSON
        * Do NOT truncate the response
        * Ensure all arrays and objects are fully closed
        * If response is long, still complete it fully
        * Use null where needed
        * EXCEPT vehicle object fields where empty string "" must be used

        OUTPUT FORMAT (STRICT JSON ONLY):
        {"itinerary":{"destination":[],"total_days":0,"weekend_days":[],"special_attractions":[],"detailed_program":[{"day":0,"date":"YYYY-MM-DD","special_attraction":"","day_wise_program":"","overnight_stay":"","meal_plan":""}],"inclusions":[],"exclusions":[]},"day_wise_activity":[{"day":0,"date":"YYYY-MM-DD","activity_title":"","activity_type":"","activity_location":null,"start_time":null,"end_time":null,"duration":null,"is_optional":false,"activity_cost":0,"currency":null,"description":""}],"hotels":[{"city_name":"","hotel_name":"","category":"","check_in_date":"YYYY-MM-DD","check_out_date":"YYYY-MM-DD","total_rooms":0,"extra_bed":0}],"vehicle":[{"day":0,"date":"YYYY-MM-DD","vehicle_name":"","service_type":"","pickup_from":"","drop_to":"","total_vehicles":0}],"costings":[{"adult_cost_per_person":0,"adult_with_extra_bed_cost":0,"child_with_extra_bed_cost":0,"child_with_no_bed_cost":0,"total_cost":0,"currency":""}],"terms_and_conditions":{"general":[],"payment_policy":[],"cancellation_policy":[],"important_notes":[]}}

        EXTRACTION RULES:
        * Do NOT hallucinate missing values → use null
        * Ignore duplicate or repeated pricing blocks
        * Output must be valid JSON only

        DATES:
        * Convert all dates to YYYY-MM-DD
        * Infer year from Travel Date if missing

        ITINERARY:
        * Extract destinations from hotel cities or itinerary flow
        * total_days = count of itinerary days
        * weekend_days = detect based on date (Friday, Saturday, Sunday)

        SPECIAL ATTRACTIONS EXTRACTION (VERY IMPORTANT):

        PRIMARY RULE:

        DAY TITLE PRIORITY RULE FOR detailed_program[].special_attraction

        HIGHEST PRIORITY RULE:

        Before applying any other special_attraction extraction logic, first extract the title text that appears immediately after the day label.

        Examples:
        Day 1: Hanoi Arrival - City Tour (-)
        → day title = "Hanoi Arrival - City Tour"

        Day 2: Hanoi – Halong Bay by Shuttle Bus – Overnight on Cruise (B, L, D)
        → day title = "Hanoi – Halong Bay by Shuttle Bus – Overnight on Cruise"

        Day 4: Danang – Bana Hill – Golden Bridge - Danang (B)
        → day title = "Danang – Bana Hill – Golden Bridge - Danang"

        EXTRACTION STEPS:

        1. Detect the line beginning with:
        Day X:

        2. Extract all text after the colon ":" until the meal notation or end of line.

        3. Remove meal indicators such as:
        (-), (B), (L), (D), (B, L), (B, L, D), (Br)

        4. Trim spaces and trailing punctuation.

        PRIMARY ASSIGNMENT RULE:

        If the extracted day title contains meaningful words, use it directly as:
        detailed_program[].special_attraction

        Do NOT replace it with individual attraction names.

        GENERIC TITLES THAT SHOULD NOT BE USED DIRECTLY:

        Arrival
        Departure
        Leisure
        Free Day
        Transfer
        Check-in
        Check-out

        If the title contains only generic text, apply the normal attraction extraction rules.

        FALLBACK RULE:

        If no valid day title is found, then apply all existing special_attraction extraction rules.

        MANDATORY RULE:

        Whenever a meaningful day title exists, detailed_program[].special_attraction MUST be populated with that cleaned day title.

        Every detailed_program[].special_attraction MUST contain meaningful content whenever a day contains:

        * sightseeing
        * attractions
        * tours
        * temple visits
        * beaches
        * activities
        * monuments
        * experiences
        * excursions
        * adventure activities
        * transfers linked with famous places
        * destination highlights

        DO NOT leave detailed_program[].special_attraction empty or null if meaningful content exists anywhere in the day text.

        DETAILED PROGRAM SPECIAL ATTRACTION EXTRACTION RULES:

        1. First extract actual sightseeing places, attractions, tours, temples, beaches, monuments, waterfalls, activities, experiences, transfers, or destination highlights.

        2. If multiple attractions exist:
        Combine them into a comma-separated readable string.

        Example:
        "Uluwatu Temple, Water Sports"

        3. If no attraction exists but a clear activity/service title exists:
        Use that title.

        Examples:

        "ATV Activity"
        "Sunset Cruise"
        "City Tour"

        4. If the day only contains arrival/departure/transfer information:

        Use meaningful labels such as:

        "Arrival Transfer"
        "Departure Transfer"
        "Airport Transfer"

        5. MANDATORY FALLBACK RULE:

        If NO explicit attraction/activity/title exists,
        then generate a SHORT readable summary from the day_wise_program field.

        IMPORTANT:

        * This fallback summary MUST be concise
        * Maximum 3 to 6 words preferred
        * Use the main route/activity/theme of the day
        * Do NOT copy the full day_wise_program
        * Do NOT return long sentences

        FALLBACK EXAMPLES:

        day_wise_program:
        "Arrival at Bali airport and transfer to Kuta hotel for overnight stay."

        special_attraction:
        "Arrival at Kuta"

        day_wise_program:
        "Travel from Shimla to Manali via Kullu Valley."

        special_attraction:
        "Shimla to Manali"

        day_wise_program:
        "Leisure day at beach resort with evening free for shopping."

        DAY-WISE ACTIVITY EXTRACTION (STRICT RULES – ONLY EXPLICIT ACTIVITY SECTIONS)

        CRITICAL PURPOSE:
        The day_wise_activity array is used ONLY to capture activities that are explicitly listed in dedicated activity sections such as:

        * Optional Activities
        * Activities
        * Day-wise Activities
        * Excursions
        * Add-on Tours
        * Supplement Activities
        * Recommended Activities
        * Sightseeing Activities
        * Extra Activities

        DO NOT populate day_wise_activity from detailed_program or itinerary day descriptions unless the activity is explicitly listed in one of the above activity sections.

        SOURCE PRIORITY:
        1. Optional Activities section
        2. Activities section
        3. Day-wise Activities section
        4. Excursions section
        5. Add-on Tours section
        6. Supplement Activities section
        7. Recommended Activities section

        STRICT EXTRACTION RULES:
        1. Create one day_wise_activity object ONLY for days that have explicitly listed activities in dedicated activity sections.
        2. If only Day 2 and Day 5 have activities listed, then day_wise_activity must contain ONLY two objects.
        3. Do NOT create day_wise_activity entries for all itinerary days.
        4. Do NOT derive activities from detailed_program, special_attraction, or day_wise_program.
        5. Do NOT create default arrival, departure, sightseeing, or transfer activities unless they appear inside a dedicated activity section.
        6. If no dedicated activity section exists anywhere in the input, return:
        "day_wise_activity":[]
        7. Multiple activity types (optional, excursion, daily activity, etc.) should all be captured using the same structure.
        8. Preserve the referenced itinerary day number and corresponding date from detailed_program if available.

        FIELDS:
        activity_title →
        * Main title of the listed activity.
        * Examples:
        - "Albanian Night - Dinner with Folk Show"
        - "Dinner with Sea Food Experience"
        - "Sunset Cruise"
        - "ATV Adventure"

        activity_type →
        Categorize into one of:
        * "optional"
        * "excursion"
        * "activity"
        * "meal"
        * "city_tour"
        * "sightseeing"
        * "leisure"
        * "other"

        activity_location →
        * Extract the city/location mentioned in the activity title or description.
        * If not available → null.

        start_time →
        * Extract if explicitly mentioned; otherwise null.

        end_time →
        * Extract if explicitly mentioned; otherwise null.

        duration →
        * Extract if explicitly mentioned (e.g., "2 hours", "2 hrs"); otherwise null.

        is_optional →
        * true if activity comes from Optional Activities or similar add-on sections.
        * false otherwise.

        activity_cost →
        * Extract numeric amount only.
        * Examples:
        - €68/p → 68
        - USD 120 → 120
        * If not available → 0.

        currency →
        * Extract currency code or symbol (EUR, USD, €, etc.).
        * If not available → null.

        description →
        * Short cleaned summary of the listed activity.

        SECTION DETECTION EXAMPLES:
        The following headings should trigger extraction:
        * OPTIONAL ACTIVITIES:
        * ACTIVITIES:
        * DAY WISE ACTIVITIES:
        * EXCURSIONS:
        * ADD-ON TOURS:
        * RECOMMENDED ACTIVITIES:
        * SUPPLEMENT ACTIVITIES:

        EXAMPLE:
        If input contains:
        Day 2: Albanian Night - Dinner with Folk Show, 2 hours. €68/p
        Day 5: Dinner with Sea Food Experience in Vlore, 2 hrs. €35/p

        Then day_wise_activity must contain ONLY:
        - One object for Day 2
        - One object for Day 5

        NOT eight objects for all itinerary days.

        FINAL OUTPUT RULE:
        day_wise_activity is an independent array containing ONLY explicitly listed activities from dedicated activity sections.

        It must NOT be auto-generated from itinerary day descriptions.

        special_attraction:
        "Beach Leisure"

        6. NEVER keep detailed_program[].special_attraction null or empty when day_wise_program contains meaningful travel content.

        7. Use null ONLY when absolutely no usable itinerary content exists for that day.

        AGGREGATED itinerary.special_attractions RULE:

        * Extract ALL unique attractions from every detailed_program[].special_attraction
        * Split comma-separated values
        * Remove duplicates
        * Preserve readable names
        * Ignore null or empty values

        IMPORTANT:

        If ANY detailed_program[].special_attraction contains value,
        then itinerary.special_attractions MUST NOT be empty.

        If no attractions exist anywhere:
        Return:
        "special_attractions":[]

        Do NOT generate generic attractions.
        Only extract or derive from actual itinerary content.

        PROGRAM:
        * Extract day-wise itinerary
        * Combine broken sentences
        * Remove unnecessary timing info

        DETAILED PROGRAM FIELD RULES:
        special_attraction →
        * Extract key highlights, attractions, tours, experiences, transfer types, or major activities of the day
        * If not available, generate SHORT meaningful summary from day_wise_program
        * NEVER keep null when meaningful itinerary content exists

        day_wise_program →
        FULL CONTENT PRESERVATION RULE (HIGHEST PRIORITY):
        The value of detailed_program[].day_wise_program MUST contain the COMPLETE cleaned narrative for that itinerary day.
        MANDATORY RULES:
        1. Preserve ALL paragraphs belonging to the day until the next "Day X:" heading or until a non-itinerary section begins (such as Hotels, Price, Inclusions, Exclusions, Notes, Terms and Conditions).
        2. Do NOT stop extraction at periods (.), colons (:), semicolons (;), or ellipses (...).
        3. Do NOT truncate after the first sentence.
        4. Preserve all bracketed and parenthetical content exactly as written, including:
        - (B)
        - (B, L, D)
        - (Note: Hotel’s check in is 14.00/15.00, Early check in is NOT included)
        - (go to Halong Bay cruise by Shuttle Bus)
        - (no guide)
        - quoted text such as "Little Mekong Delta"
        5. Preserve special punctuation and symbols such as:
        - &
        - /
        - –
        - -
        - :
        - ...
        6. Merge broken lines and paragraphs into one continuous readable text.
        7. Maintain the original sentence order.
        8. Remove only excessive whitespace and duplicate blank lines.
        9. Do NOT remove Note:, Important:, or similar informational sentences if they belong to the itinerary day.

        OUTPUT FORMAT RULE:
        Return day_wise_program as a single cleaned string containing the full day description.
        EXAMPLE:
        Input:
        Day 1: Hanoi Arrival - City Tour (-)
        Upon arrival at Noi Bai International Airport, our local guide and private driver will warmly welcome you and transfer you to the city center. Check-in at your hotel (Note: Hotels check in is 14.00/15.00, Early check in is NOT included)
        Visit the Ho Chi Minh Complex, where Vietnams beloved leader rests peacefully.
        Overnight in Hanoi.
        Output day_wise_program:
        "Upon arrival at Noi Bai International Airport, our local guide and private driver will warmly welcome you and transfer you to the city center. Check-in at your hotel (Note: Hotels check in is 14.00/15.00, Early check in is NOT included) Visit the Ho Chi Minh Complex, where Vietnam beloved leader rests peacefully."
        
        overnight_stay →
        * Extract city/place where night stay is planned

        LAST DAY RULE:
        * If overnight stay exists → use it
        * Else → "Tour End"

        OTHER DAYS:
        * Use actual overnight stay location or null

        meal_plan →
        * Normalized readable version

        MEALS PLAN (FINAL FIX - BLOCK BASED EXTRACTION):

        CRITICAL RULE:
        Hotel data is in structured blocks. Treat the following as ONE unit:

        Nights line (e.g., "1st, 2nd Nights at Shimla")
        Check-in / Check-out
        Hotel name
        Meal line

        Do NOT break this structure.

        STEP 1: IDENTIFY HOTEL BLOCK
        Group lines until next "Nights at" appears

        STEP 2: EXTRACT MEAL FROM SAME BLOCK ONLY
        Extract ONLY meal portion from the last line
        Ignore room details after meal

        Example:
        "Dinner + Breakfast • 1 Executive with balcony (2 Pax)" → "Dinner + Breakfast"

        STEP 3: CLEAN TEXT
        Lowercase
        Remove symbols: + / - , . • |
        Remove words: room, pax, balcony, executive, deluxe, etc.

        STEP 4: DETECT KEYWORDS
        breakfast → B
        lunch → L
        dinner → D

        STEP 5: NORMALIZE (ORDER FIXED)
        Always: Breakfast → Lunch → Dinner

        Mappings:
        Breakfast + Dinner → B+D
        Breakfast + Lunch → B+L
        Lunch + Dinner → L+D
        All three → B+L+D

        Single:
        Breakfast → Breakfast
        Lunch → Lunch
        Dinner → Dinner

        STEP 6: MAP TO ITINERARY DAYS
        Use check-in and check-out

        Rule:
        Apply from check-in date to (check-out - 1 day)

        Example:
        09-11 → Day 1, Day 2
        11-14 → Day 3, 4, 5

        STEP 7: PRIORITY
        Hotel meals override itinerary meals

        STEP 8: FALLBACK
        If not in hotel → check itinerary → else null

        STRICT RULES:
        Do NOT mix meals between hotels
        Do NOT extract from room text
        Do NOT skip mapping
        Always bind meal to correct hotel block

        HOTELS:
        * Extract city, hotel, category
        * Parse Nights to compute checkout
        * ROOM: 03 → 3
        * EB → extra_bed

        VEHICLE RULES:
        * Extract vehicles day-wise
        * One vehicle object per itinerary day
        * Do NOT merge days
        * Ignore sightseeing/activity names as vehicle_name

        VALID vehicle examples:
        * Innova
        * Crysta
        * Ertiga
        * Tempo Traveller
        * Haice
        * Coach
        * Bus
        * SUV
        * Sedan
        * Mini Van

        If unclear:
        "vehicle_name":""

        If vehicle data does not exist:
        "vehicle":[]

        For missing vehicle text fields:
        return ""

        For missing numeric fields:
        return 0

        NEVER use null inside vehicle object.

        COSTINGS:
        Extract and normalize pricing into the following fields:
        * adult_cost_per_person
        * adult_with_extra_bed_cost
        * child_with_extra_bed_cost
        * child_with_no_bed_cost
        * total_cost
        * currency

        STANDARD FIELD MAPPING:
        Adult Price / Per Person / Package Price / Cost Per Person
        → adult_cost_per_person

        Adult with EB / Extra Bed / Extra Bed Cost / Extra Person Cost
        → adult_with_extra_bed_cost

        Child with Bed / Child with Extra Bed / CWB
        → child_with_extra_bed_cost

        Child without Bed / Child No Bed / CNB
        → child_with_no_bed_cost

        Total / Package Cost / Grand Total
        → total_cost

        EXTRA BED DISAMBIGUATION RULE (VERY IMPORTANT):

        If the pricing text contains phrases such as:
        - Extra bed
        - Extra Bed Cost
        - Extra Person
        - Additional Person
        - Extra Mattress
        - Additional Adult
        - Extra Adult

        AND the text does NOT explicitly mention any child-related keyword such as:
        - Child
        - Children
        - Kid
        - Infant
        - CWB
        - CNB

        THEN map the amount to:
        adult_with_extra_bed_cost

        NOT to:
        child_with_extra_bed_cost

        CHILD-SPECIFIC RULE:

        Only populate child_with_extra_bed_cost when the pricing text explicitly contains child-related wording such as:
        - Child with Bed
        - Child with Extra Bed
        - Children with Bed
        - CWB

        Only populate child_with_no_bed_cost when the pricing text explicitly contains:
        - Child without Bed
        - Child No Bed
        - CNB

        EXAMPLE 1:

        Input:
        Extra bed 01. Rs.9500/-

        Output:
        adult_with_extra_bed_cost = 9500

        EXAMPLE 2:

        Input:
        Child with Bed Rs.9500

        Output:
        child_with_extra_bed_cost = 9500

        EXAMPLE 3:

        Input:
        Additional Adult Rs.9500

        Output:
        adult_with_extra_bed_cost = 9500

        EXAMPLE 4:

        Input:
        CWB Rs.7500

        Output:
        child_with_extra_bed_cost = 7500

        TOTAL COST EXTRACTION RULE:

        If text contains:
        - Total cost of package Rs.18700 per person
        - Package Price Rs.18700 per person

        Then:
        adult_cost_per_person = 18700

        Set total_cost only when an actual total package amount is explicitly stated for the whole booking rather than per person.

        CURRENCY RULE:

        Extract currency symbols and normalize:
        - Rs., INR, ₹ → INR
        - USD, $ → USD
        - EUR, € → EUR

        MISSING VALUES RULE:
        If a pricing field is not present, return 0.
        DO NOT GUESS child pricing from generic "Extra Bed" text.

        TERMS AND CONDITIONS EXTRACTION:
        Extract all terms & conditions from INPUT_TEXT
        Categorize into:
        general → common rules, liabilities, responsibilities
        payment_policy → advance, balance payment, due dates
        cancellation_policy → refund rules, cancellation charges, timelines
        important_notes → travel notes, restrictions, disclaimers
        Clean and split into readable bullet points
        Remove duplicates and noise
        If no data found → return empty arrays

        INCLUSIONS & EXCLUSIONS EXTRACTION RULES:

        SECTION DETECTION
        Identify sections using keywords (case-insensitive):
        Inclusions → inclusion, inclusions, package includes, cost includes, price includes
        Exclusions → exclusion, exclusions, package excludes, cost excludes, price excludes

        STANDARD EXTRACTION
        If sections exist:
        Extract all bullet points or lines under each section
        Stop when a new unrelated section begins
        Clean symbols (•, -, *, !!), trim spaces, remove duplicates

        PARAGRAPH-BASED EXTRACTION
        If written in sentence form (e.g., “includes hotel, meals… not includes airfare…”):
        Split into inclusions and exclusions correctly
        Convert into clean bullet points

        INFERENCE RULES (ONLY IF SECTION MISSING)
        Infer inclusions from itinerary content ONLY when clearly supported:
        Hotel stay → "Accommodation"
        Meals mentioned → "Meals as per itinerary"
        Transport mentioned → "Transportation"
        Sightseeing → "Sightseeing as per itinerary"

        For exclusions:
        Extract ONLY if explicitly stated (e.g., airfare not included, personal expenses, taxes extra)
        DO NOT guess exclusions

        NORMALIZATION
        Convert short forms: B/F → Breakfast
        Merge duplicates (e.g., Hotel stay & Accommodation → keep one)
        Keep entries concise and readable

        STRICT OUTPUT RULE
        Always return arrays
        If nothing found → []
        NEVER return null for inclusions/exclusions

        NOISE FILTERING
        Ignore:
        Terms & conditions
        Payment or cancellation text
        Marketing/promotional lines

        EDGE CASE HANDLING
        If inclusions & exclusions are merged → split using keywords (include/exclude)
        If unclear → return empty arrays (do NOT guess)

        CLEANING:
        Remove !! and noise
        Fix merged words
        Normalize currency
        Convert numbers

        INPUT:
        {{INPUT_TEXT}}

        OUTPUT:
        Return ONLY JSON';

    public function generateContent($text)
    {
        $text = trim((string)$text);
        if ($text === '')
            return array('status' => false, 'error' => 'Text is required.');
        
        $promptWithInput = str_replace('{{INPUT_TEXT}}', $text, $this->promptTemplate);

        $payload = array(
            "contents" => array(
                array(
                    "role" => "user",
                    "parts" => array(
                        array("text" => $promptWithInput)
                    )
                )
            ),
            "generationConfig" => array(
                "temperature" => 0,
                "topK" => 1,
                "topP" => 1,
                "maxOutputTokens" => 50000
            )
        );

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false)
            return array('status' => false, 'error' => 'cURL error: ' . $curlError);

        $decoded = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            $error = isset($decoded['error']['message']) ? $decoded['error']['message'] : 'Gemini API request failed.';
            return array('status' => false, 'error' => $error, 'raw' => $decoded);
        }

        $reply = '';
        if (isset($decoded['candidates'][0]['content']['parts'][0]['text']))
            $reply = trim($decoded['candidates'][0]['content']['parts'][0]['text']);

        $parsed = json_decode($reply, true);

        if (isset($parsed['Error'])) {
            return array(
                'status' => false,
                'error' => $parsed['Error']
            );
        }

        return array('status' => true, 'reply' => $reply, 'raw' => $decoded);
    }
}

function gemini_generate_content($text)
{
    $gemini = new GeminiController();
    return $gemini->generateContent($text);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $text = isset($_POST['text']) ? $_POST['text'] : (isset($_POST['message']) ? $_POST['message'] : '');
    echo json_encode(gemini_generate_content($text));
    exit;
}
