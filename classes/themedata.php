    <?php
    class ThemeData
    {

        private $connection = null;
        private $b2cBaseUrl = "";

        private $crmBaseUrl = "";

        private $selectedCurrency = "";

        private $currencyList = [];

        public function __construct(
            $connection,
            $btcBaseUrl,
            $crmBaseUrl,
            $currency
        ) {

            $this->connection = $connection;
            $this->b2cBaseUrl = $btcBaseUrl;
            $this->crmBaseUrl = $crmBaseUrl;
            $this->selectedCurrency = $currency;
            $this->fetchCurrencyMasterList();
        }


        private function fetchCurrencyMasterList()
        {

            $query = mysqli_query($this->connection, "SELECT * FROM currency_name_master");
            $rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
            foreach ($rows as $row) {
                $this->currencyList[$row['id']] = $row['default_currency'];
            }
        }

        public function getCurrencySymbol($currencyId)
        {
            return $this->currencyList[$currencyId] ?? "";
        }

        public function getBanners()
        {
            $settingsRow = mysqli_fetch_assoc(mysqli_query($this->connection, "select banner_images from b2c_settings LIMIT 1"));
            $images = $settingsRow['banner_images'] ? json_decode($settingsRow['banner_images'], true) : [];
            $banners = [];
            foreach ($images as $image) {
                $banners[] = (!empty($image['image_url'])) ? $this->crmBaseUrl . $this->filterImgUrl($image['image_url']) : $this->b2cBaseUrl . 'images/banner.png';
            }
            return $banners;
        }



        public function getSocialIcons()
        {
            $settingsRow = mysqli_fetch_assoc(mysqli_query($this->connection, "select social_media from b2c_settings LIMIT 1"));
            return $settingsRow['social_media'] ? json_decode($settingsRow['social_media'], true) : [];
        }

        public function getGroupTourDropDownData()
        {
            $query = "SELECT 
            tour_master.tour_id, 
            tour_master.tour_type, 
            tour_master.tour_name,
            tour_master.dest_id, 
            destination_master.dest_name
            FROM tour_master
            INNER JOIN tour_groups 
            ON tour_groups.tour_id = tour_master.tour_id 
            AND tour_groups.status = 'Active'
            INNER JOIN destination_master 
            ON destination_master.dest_id = tour_master.dest_id
            WHERE tour_master.active_flag = 'Active' GROUP BY tour_master.dest_id";

            $result = mysqli_query($this->connection, $query);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $data;
        }

        public function hydrateGroupTourDropDownData(array $groupTours)
        {
            $domesticGroupTours = [];
            $internationalGroupTours = [];
            foreach ($groupTours as $tour) {
                if ($tour['tour_type'] === 'Domestic') {
                    $domesticGroupTours[] = $tour;
                }
                if ($tour['tour_type'] === 'International') {
                    $internationalGroupTours[] = $tour;
                }
            }

            return [
                $domesticGroupTours,
                $internationalGroupTours
            ];
        }

        public function getCurrencyDropDownData()
        {
            $query = "select * from currency_name_master order by currency_code";
            $result =  mysqli_query($this->connection, $query);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getHolidayPackagesDropDownData()
        {
            $query = "
                SELECT 
                    cpm.package_id, 
                    cpm.dest_id, 
                    cpm.package_name, 
                    cpm.tour_type, 
                    dm.dest_name 
                FROM custom_package_master cpm
                LEFT JOIN destination_master dm ON cpm.dest_id = dm.dest_id
                WHERE cpm.status = 'Active' GROUP BY cpm.dest_id
            ";
            $result = mysqli_query($this->connection, $query);

            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $data;
        }

        public function hydratePackageTourDropDownData(array $packageTours)
        {
            $domesticPackageTours = [];
            $internationalPackageTours = [];
            foreach ($packageTours as $tour) {
                if ($tour['tour_type'] === 'Domestic') {
                    $domesticPackageTours[] = $tour;
                }
                if ($tour['tour_type'] === 'International') {
                    $internationalPackageTours[] = $tour;
                }
            }


            return [
                $domesticPackageTours,
                $internationalPackageTours
            ];
        }

        public function getPopularPackages()
        {

            $result = mysqli_query(
                $this->connection,
                "SELECT
                custom_package_master.*,
                destination_master.dest_name,
                gallary_master.entry_id,
                gallary_master.description,
                gallary_master.image_url,
                custom_package_images.image_entry_id,
                custom_package_images.image_url,
                custom_package_tariff.hotel_type,
                custom_package_tariff.min_pax,
                custom_package_tariff.max_pax,
                custom_package_tariff.from_date,
                custom_package_tariff.to_date,
                custom_package_tariff.badult,
                custom_package_tariff.bcwb,
                custom_package_tariff.bcwob,
                custom_package_tariff.binfant,
                custom_package_tariff.cadult,
                custom_package_tariff.ccwb,
                custom_package_tariff.ccwob,
                custom_package_tariff.cinfant,
                custom_package_tariff.bextra,
                custom_package_tariff.cextra
            FROM
                custom_package_master
            LEFT JOIN destination_master ON custom_package_master.dest_id = destination_master.dest_id
            LEFT JOIN gallary_master ON destination_master.dest_id = gallary_master.dest_id
            LEFT JOIN custom_package_images ON custom_package_master.package_id = custom_package_images.package_id
            LEFT JOIN custom_package_tariff ON custom_package_master.package_id = custom_package_tariff.package_id
            WHERE
                custom_package_master.status = 'Active'
            GROUP BY
                custom_package_master.package_id"
            );
            $packageData = array();
            while ($data = mysqli_fetch_array($result)) {
                $packageData[] = array(
                    'package_id' => $data['package_id'],
                    'dest_id' => $data['dest_id'],
                    'package_code' => $data['package_code'],
                    'package_name' => $data['package_name'],
                    'seo_slug' => $data['seo_slug'],
                    'total_days' => $data['total_days'],
                    'total_nights' => $data['total_nights'],
                    'adult_cost' => $data['adult_cost'],
                    'child_cost' => $data['child_cost'],
                    'infant_cost' => $data['infant_cost'],
                    'child_with' => $data['child_with'],
                    'child_without' => $data['child_without'],
                    'extra_bed' => $data['extra_bed'],
                    'tour_cost' => $data['tour_cost'],
                    'markup_cost' => $data['markup_cost'],
                    'taxation_id' => $data['taxation_id'],
                    'service_tax' => $data['service_tax'],
                    'service_tax_subtotal' => $data['service_tax_subtotal'],
                    'total_tour_cost' => $data['total_tour_cost'],
                    'inclusions' => $data['inclusions'],
                    'exclusions' => $data['exclusions'],
                    'status' => $data['status'],
                    'created_at' => $data['created_at'],
                    'clone' => $data['clone'],
                    'tour_type' => $data['tour_type'],
                    'currency_id' => $data['currency_id'],
                    'taxation' => $data['taxation'],
                    'update_flag' => $data['update_flag'],
                    'note' => $data['note'],
                    'dest_image' => $data['dest_image'],

                );
            }

            $setData = mysqli_fetch_object(mysqli_query($this->connection, "SELECT * FROM b2c_settings LIMIT 1"));
            $popularDest = json_decode($setData->popular_dest);

            if (empty($popularDest)) {
                return [];
            }

            $popularDestData = [];
            foreach ($popularDest as $destination) {

                $destinationdata = [];
                $gallerydata = [];
                $imagedata = [];
                $tariffdata = [];

                foreach ($packageData as  $data) {
                    if ($destination->package_id == $data['package_id']) {
                        $package_name = $data['package_name'];
                        $currency_id = $data['currency_id'];
                        $destid = $data['dest_id'];
                        $currency_logo_d = mysqli_fetch_object(mysqli_query($this->connection, "SELECT default_currency, currency_code FROM currency_name_master WHERE id = $currency_id LIMIT 1"));
                        $currency_code = $currency_logo_d->currency_code;
                        $package_fname = str_replace(' ', '_', $package_name);
                        $file_name = 'package_tours/' . $package_fname . '-' . $data['package_id'] . '.php';

                        $data['file_name_url'] = $file_name;
                        $data['main_img_url'] = $destination->url;
                        $data['currency'] = $currency_logo_d;

                        $resultdest = mysqli_query($this->connection, "SELECT * FROM destination_master where dest_id=$destid");
                        while ($datadest = mysqli_fetch_array($resultdest)) {

                            $resultgallery = mysqli_query($this->connection, "SELECT * FROM gallary_master where dest_id='" . $datadest['dest_id'] . "'");
                            while ($datagall = mysqli_fetch_array($resultgallery)) {
                                $gallerydata[] = [
                                    'entry_id' => $datagall['entry_id'],
                                    'dest_id' => $datagall['dest_id'],
                                    'description' => $datagall['description'],
                                    'image_url' => $datagall['image_url']
                                ];
                            }

                            $destinationdata = array(
                                'dest_id' => $datadest['dest_id'],
                                'dest_name' => $datadest['dest_name'],
                                'status' => $datadest['status'],
                                'gallery_images' => $gallerydata
                            );
                        }

                        $data['destination'] = $destinationdata;
                        $resultimage = mysqli_query($this->connection, "SELECT * FROM custom_package_images where package_id='" . $data['package_id'] . "'");
                        while ($dataimage = mysqli_fetch_array($resultimage)) {


                            $imagedata[] = [
                                'image_entry_id' => $dataimage['image_entry_id'],
                                'image_url' => $dataimage['image_url'],
                                'package_id' => $dataimage['package_id'],

                            ];
                        }

                        $data['images'] = $imagedata;

                        $resulttariff = mysqli_query($this->connection, "SELECT * FROM custom_package_tariff where package_id='" . $data['package_id'] . "' ORDER BY entry_id ASC LIMIT 1");
                        while ($datatariff = mysqli_fetch_array($resulttariff)) {


                            $tariffdata = [
                                'entry_id' => $datatariff['entry_id'],
                                'package_id' => $datatariff['package_id'],
                                'hotel_type' => $datatariff['hotel_type'],
                                'min_pax' => $datatariff['min_pax'],
                                'from_date' => $datatariff['from_date'],
                                'to_date' => $datatariff['to_date'],
                                'badult' => $datatariff['badult'],
                                'bcwob' => $datatariff['bcwob'],
                                'binfant' => $datatariff['binfant'],
                                'cadult' => $datatariff['cadult'],
                                'ccwb' => $datatariff['ccwb'],
                                'cinfant' => $datatariff['cinfant'],
                                'bextra' => $datatariff['bextra'],
                                'cextra' => $datatariff['cextra'],

                            ];
                        }

                        $data['tariff'] = $tariffdata;
                        array_push($popularDestData, $data);
                    }
                }
            }
            return $popularDestData;
        }

        public function getCustomerTestimonials($limit)
        {
            if ($limit != '')
                $query = "SELECT  * FROM `b2c_testimonials` LIMIT $limit";
            else
                $query = "SELECT  * FROM `b2c_testimonials` where 1";
            $result = mysqli_query($this->connection, $query);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }


        public function getPopularGroupTours($limit = 10)
        {

            $popularToursQuery = "SELECT popular_tours FROM b2c_settings";
            $popularToursResult = mysqli_query($this->connection, $popularToursQuery);
            $popularToursData = mysqli_fetch_assoc($popularToursResult);

            $popularTours = $popularToursData['popular_tours'] ? json_decode($popularToursData['popular_tours'], true) : null;

            if (!$popularTours) {
                return [];
            }

            $tourIds = [];

            foreach ($popularTours as $tour) {
                $tourIds[] = $tour['tour_id'];
            }

            $query = "SELECT
                tm.tour_id,
                tm.tour_type,
                tm.tour_name,
                tm.seo_slug,
                tm.adult_cost,
                tm.active_flag,
                tm.dest_id,
                tm.dest_image,
                dm.dest_name,
                gm.image_url
            FROM
                tour_master AS tm
            INNER JOIN destination_master AS dm
            ON
                dm.dest_id = tm.dest_id
            INNER JOIN gallary_master gm ON
                gm.entry_id = tm.dest_image
            WHERE
                tm.active_flag = 'Active' AND tm.tour_id IN (" . implode(',', $tourIds) . ") LIMIT $limit";

            $result = mysqli_query($this->connection, $query);

            $data =  mysqli_fetch_all($result, MYSQLI_ASSOC);

            foreach ($data as $key => $tour) {


                $tourDatesQuery = mysqli_query($this->connection, "SELECT from_date, to_date FROM tour_groups WHERE status ='Active'  AND tour_id = '" . $tour['tour_id'] . "' ORDER BY group_id ");
                $tourDatesRows = mysqli_fetch_all($tourDatesQuery, MYSQLI_ASSOC);

                $tourDates = "";
                foreach ($tourDatesRows as $tourDate) {

                    $from_date = date("d-m-Y", strtotime($tourDate['from_date']));
                    $to_date = date("d-m-Y", strtotime($tourDate['to_date']));

                    $val = (int)date_diff(date_create(date("d-m-Y")), date_create($to_date))->format("%R%a");
                    if ($val <= 0)  continue; // skipping the ended group tours (only used group quotation)
                    $tourDates .= '<i class="fa-solid fa-calendar-days me-1"></i>' . $from_date . " To " . $to_date . "<br/>";
                }

                $package_fname = str_replace(' ', '_', $tour['tour_name']);
                $file_name = 'group_tours/' . $package_fname . '-' . $tour['tour_id'] . '.php';

                $full_path = getcwd() . '/' . $file_name;


                if (!file_exists($full_path)) {
                    file_put_contents($full_path, "<?php include \"../group-tour-detail.php\"; ?>");
                }
                $data[$key]['file_name_url'] = trim($file_name);
                $data[$key]['tour_dates'] = $tourDates;
            }

            return $data;
        }


        public function getPopularActivities()
        {

            $setData = mysqli_fetch_assoc(mysqli_query($this->connection, "SELECT popular_activities FROM b2c_settings LIMIT 1"));

            $popularAct = json_decode($setData['popular_activities']);

            if (empty($popularAct)) {
                return [];
            }

            $query = mysqli_query($this->connection, "SELECT * FROM excursion_master_tariff where active_flag='Active'");
            $activities = [];
            while ($data = $query->fetch_assoc()) {
                $activities[] = $data;
            }


            $selectedActivities = [];
            $citydata = [];
            $imagesdata = [];
            foreach ($activities as $main) {
                foreach ($popularAct as $act) {

                    if ($act->exc_id == $main['entry_id']) {

                        $basic = mysqli_fetch_object(mysqli_query($this->connection, "SELECT * FROM excursion_master_tariff_basics where exc_id='" . $main['entry_id'] . "' ORDER BY entry_id DESC LIMIT 1"));

                        $resultimage = mysqli_query($this->connection, "SELECT * FROM  excursion_master_images where exc_id ='" . $main['entry_id'] . "'");
                        while ($dataactivity = mysqli_fetch_array($resultimage)) {

                            $imagesdata[] = array(
                                'entry_id' => $dataactivity['entry_id'],
                                'exc_id' => $dataactivity['exc_id'],
                                'image_url' => $dataactivity['image_url'],

                            );
                        }

                        $getqueryimage = mysqli_fetch_object(mysqli_query($this->connection, "SELECT image_url FROM excursion_master_images where exc_id='" . $main['entry_id'] . "' ORDER BY entry_id DESC LIMIT 1"));


                        $main['basics'] = $basic;
                        $main['images'] = $imagesdata;
                        $imgUrl = (!empty($getqueryimage->image_url)) ? "crm/" . $getqueryimage->image_url : $this->b2cBaseUrl . 'images/activity_default.png';
                        $main['main_img_url'] = $this->filterImgUrl($imgUrl);


                        $datacity = mysqli_fetch_array(mysqli_query($this->connection, "SELECT * FROM  city_master where city_id ='" . $main['city_id'] . "'"));
                        // while ($datacity = mysqli_fetch_array($resulthotel)) {

                        $main['city_details'] =  [
                            'city_id' => $datacity['city_id'],
                            'city_name' => $datacity['city_name'],
                            'active_flag' => $datacity['active_flag'],

                        ];
                        // }

                        // $main['city_details'] = $citydata;
                        array_push($selectedActivities, $main);
                    }
                }
            }
            return $selectedActivities;
        }


        private function filterImgUrl($imgUrlMain)
        {
            if (empty($imgUrlMain)) {
                return 0;
            }
            $url = $imgUrlMain;
            $pos = strstr($url, 'uploads');
            if ($pos != false) {
                $newUrl1 = preg_replace('/(\/+)/', '/', $imgUrlMain);
                $newUrl = str_replace('../', '', $newUrl1);
            } else {
                $newUrl =  $imgUrlMain;
            }
            return $newUrl;
        }


        public function getPartners()
        {
            $count = 55;
            $sq_setting = mysqli_fetch_assoc(mysqli_query($this->connection, "select assoc_logos from b2c_settings where 1"));
            $logos = ($sq_setting['assoc_logos'] != '' && $sq_setting['assoc_logos'] != 'null') ? json_decode($sq_setting['assoc_logos']) : [];

            $partners = [];
            $dir = 'https://itourscloud.com/destination_gallery/association-logo/';
            for ($i = 1; $i <= $count; $i++) {

                $image_path = $dir . $i . '.png';
                if (in_array($i, $logos)) {
                    array_push($partners, $image_path);
                }
            }

            return $partners;
        }

        public function getBlogsData($limit = 10)
        {

            $query = "SELECT * FROM b2c_blogs where active_flag='0' ORDER BY entry_id DESC LIMIT $limit";
            $result = mysqli_query($this->connection, $query);
            $data =  mysqli_fetch_all($result, MYSQLI_ASSOC);
            $blogs = [];
            foreach ($data as $item) {
                $blogs[] = [
                    'id' => $item['entry_id'],
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'image_path' => $this->filterImgUrl($item['image'])

                ];
            }
            return $blogs;
        }

        public function getPopularHotels($limit = 20)
        {

            $btcHotelRow = mysqli_fetch_assoc(mysqli_query($this->connection, "SELECT popular_hotels FROM b2c_settings LIMIT 1"));

            $b2cHotels = $btcHotelRow['popular_hotels'] ? json_decode($btcHotelRow['popular_hotels']) : null;

            if (!$b2cHotels) {
                return [];
            }

            $hotelIds = [];
            foreach ($b2cHotels as $hotelId) {
                $hotelIds[] = $hotelId->hotel_id;
            }


            $query = mysqli_query($this->connection, "
                                SELECT 
                                    hotel_master.*, 
                                    hotel_contracted_tarrif.double_bed 
                                FROM hotel_master
                                LEFT JOIN hotel_contracted_tarrif 
                                    ON hotel_contracted_tarrif.entry_id = hotel_master.hotel_id
                                WHERE hotel_master.active_flag = 'Active' AND hotel_master.hotel_id IN (" . implode(',', $hotelIds) . ") LIMIT $limit
            ");


            $popularHotels = [];
            while ($data = $query->fetch_assoc()) {
                $popularHotels[] = $data;
            }

            $selectedHotel = [];
            $hoteldata = [];
            $citydata = [];
            foreach ($popularHotels as $hotel) {

                $getqueryimage = mysqli_fetch_object(mysqli_query($this->connection, "SELECT hotel_pic_url FROM hotel_vendor_images_entries where hotel_id='" . $hotel['hotel_id'] . "' ORDER BY id DESC LIMIT 1"));
                $hotel['main_img'] = (!empty($getqueryimage->hotel_pic_url)) ? $this->crmBaseUrl . $this->filterImgUrl($getqueryimage->hotel_pic_url) : $this->b2cBaseUrl . 'images/hotel_general.png';

                $resulthotel = mysqli_query($this->connection, "SELECT * FROM  hotel_vendor_images_entries where hotel_id ='" . $hotel['hotel_id'] . "'");
                while ($datahotel = mysqli_fetch_array($resulthotel)) {

                    $hoteldata = [
                        'id' => $datahotel['id'],
                        'hotel_id' => $datahotel['hotel_id'],
                        'hotel_pic_url' => $datahotel['hotel_pic_url'],

                    ];
                }

                $hotel['hotel_image'] = $hoteldata;
                $resulthotel = mysqli_query($this->connection, "SELECT * FROM  city_master where city_id ='" . $hotel['city_id'] . "'");
                while ($datacity = mysqli_fetch_array($resulthotel)) {

                    $citydata = [
                        'city_id' => $datacity['city_id'],
                        'city_name' => $datacity['city_name'],
                        'active_flag' => $datacity['active_flag'],

                    ];
                }

                $hotel['hotel_city'] = $citydata;
                array_push($selectedHotel, $hotel);
            }

            return $selectedHotel;
        }

        public function convertCurrency($amountInInr, $toCurrencyId)
        {
            $query = mysqli_query($this->connection, "SELECT * FROM roe_master where currency_id = $toCurrencyId");
            $row = mysqli_fetch_assoc($query);
            if ($row && $row['currency_rate']) {

                $newValue = number_format(($amountInInr * $row['currency_rate']), 2);
                return $this->getCurrencySymbol($toCurrencyId) . ' ' . $newValue;
            }

            return "&#8377;" . $amountInInr;
        }
    }
