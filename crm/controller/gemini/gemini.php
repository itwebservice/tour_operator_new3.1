<?php
class GeminiController
{
    private $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=AIzaSyC9diotLu1KA4NvNz5vzh6a1HD-nIppOFY';
    
    private $promptTemplate = 
        'You are an AI that extracts structured travel itinerary data from raw, unstructured text.
        The input will be messy and may include:

        Broken formatting
        Mixed sections (itinerary, pricing, remarks, policies, terms & conditions)
        Irrelevant or noisy text
        Missing labels or merged fields

        Your job is to:
        Clean and understand the text
        Extract only relevant travel data
        Normalize it into strict JSON format

        IMPORTANT:
        Do NOT return code
        Do NOT explain anything
        Do NOT wrap in markdown
        Return ONLY valid JSON

        CRITICAL VALIDATION RULE:
        If the input text does NOT contain travel-related information (such as itinerary, destinations, hotels, transport, dates, or trip details), OR the content is unrelated (e.g., random text, technical content, chat, etc.), then return EXACTLY (STRICT JSON ONLY):
        {"Error":"Invalid input: content is not related to travel itinerary data"}

        CRITICAL OUTPUT RULES:
        Output MUST be valid JSON
        Output MUST start with {
        Output MUST end with }
        Do NOT include ``` or json or explanations
        Do NOT return partial output
        Do NOT return empty structure if input is invalid → return Error JSON instead
        Return ONLY valid JSON (no explanation, no text outside JSON)
        Do NOT truncate the response
        Ensure all arrays and objects are fully closed
        If response is long, still complete it fully
        Use null where needed (not empty string for missing values)

        OUTPUT FORMAT (STRICT JSON ONLY)
        {"itinerary":{"destination":[],"total_days":0,"weekend_days":[],"special_attractions":[],"detailed_program":[{"day":0,"date":"YYYY-MM-DD","special_attraction":"","day_wise_program":"","overnight_stay":"","meal_plan":""}],"inclusions":[],"exclusions":[]},"hotels":[{"city_name":"","hotel_name":"","category":"","check_in_date":"YYYY-MM-DD","check_out_date":"YYYY-MM-DD","total_rooms":0,"extra_bed":0}],"vehicle":{"vehicle_name":"","pickup_from":"","drop_to":"","total_vehicles":0},"costings":[{"adult_cost_per_person":0,"adult_with_extra_bed_cost":0,"child_with_extra_bed_cost":0,"child_with_no_bed_cost":0,"total_cost":0,"currency":""}],"terms_and_conditions":{"general":[],"payment_policy":[],"cancellation_policy":[],"important_notes":[]}}

        EXTRACTION RULES:
        Do NOT hallucinate missing values → use null
        Ignore duplicate or repeated pricing blocks
        Output must be valid JSON only

        DATES:
        Convert all dates to YYYY-MM-DD
        Infer year from Travel Date if missing

        ITINERARY:
        Extract destinations from hotel cities or itinerary flow
        total_days = count of itinerary days
        weekend_days = detect based on date (Friday, Saturday, Sunday)

        PROGRAM:
        Extract day-wise itinerary
        Combine broken sentences
        Remove unnecessary timing info

        DETAILED PROGRAM FIELD RULES:
        special_attraction →
        Extract key highlights, major experiences, or unique attractions of the day
        If highlights are NOT clearly available, use the Day title (e.g., "Arrival in Srinagar", "Excursion to Sonmarg")
        If both highlights and title are missing → use null

        day_wise_program → full cleaned itinerary description of the day

        overnight_stay →
        Extract the city/place where night stay is planned.
        If it is the LAST day of the itinerary:

        If overnight stay content is available → use it
        If NOT available → return "Tour End"
        For all other days, return the actual overnight stay location or null if not available

        meal_plan → normalized readable version (e.g., Breakfast, Breakfast + Dinner)

        MEALS PLAN:
        If N/A → null
        Normalize as: Breakfast, Lunch, Dinner, B+L, B+D, L+D, B+L+D, Room Only, No Meals, All Inclusive

        HOTELS:
        Extract city, hotel, category
        Parse Nights to compute checkout
        ROOM: 03 → 3
        EB → extra_bed

        VEHICLE:
        Extract vehicle name
        pickup_from → first location
        drop_to → last location
        total_vehicles → default 0

        COSTINGS:
        Extract and normalize pricing into the following fields:
        adult_cost_per_person
        adult_with_extra_bed_cost
        child_with_extra_bed_cost
        child_with_no_bed_cost
        total_cost
        currency

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
