<?php
class GeminiController
{
    private $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=AIzaSyXXXXXXXX";
    
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
        {"itinerary":{"destination":[],"total_days":0,"weekend_days":[],"special_attractions":[],"detailed_program":[{"day":0,"date":"YYYY-MM-DD","special_attraction":"","day_wise_program":"","overnight_stay":"","meal_plan":""}],"inclusions":[],"exclusions":[]},"hotels":[{"city_name":"","hotel_name":"","category":"","check_in_date":"YYYY-MM-DD","check_out_date":"YYYY-MM-DD","total_rooms":0,"extra_bed":0}],"vehicle":[{"day":0,"date":"YYYY-MM-DD","vehicle_name":"","service_type":"","pickup_from":"","drop_to":"","total_vehicles":0}],"costings":[{"adult_cost_per_person":0,"adult_with_extra_bed_cost":0,"child_with_extra_bed_cost":0,"child_with_no_bed_cost":0,"total_cost":0,"currency":""}],"terms_and_conditions":{"general":[],"payment_policy":[],"cancellation_policy":[],"important_notes":[]}}

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
        * Full cleaned itinerary description of the day

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

        Map variations like:
        Adult Price / Per Person → adult_cost_per_person
        Adult with EB / Extra Bed → adult_with_extra_bed_cost
        Child with Bed / CWB → child_with_extra_bed_cost
        Child without Bed / CNB → child_with_no_bed_cost
        Total / Package Cost → total_cost

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
