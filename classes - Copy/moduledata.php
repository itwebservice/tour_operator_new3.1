<?php
class ModuleData
{

    private $connection = null;
    private $b2cBaseUrl = "";
    private $crmBaseUrl = "";

    public function __construct(
        $connection,
        $btcBaseUrl,
        $crmBaseUrl
    ) {

        $this->connection = $connection;
        $this->b2cBaseUrl = $btcBaseUrl;
        $this->crmBaseUrl = $crmBaseUrl;
    }

    public function getBlogData($blogId)
    {

        $query = mysqli_query($this->connection, "SELECT * FROM `b2c_blogs` WHERE entry_id = $blogId");
        $row = mysqli_fetch_assoc($query);
        if (!$row) {
            return null;
        }

        $blogUrl = $row['image'];
        $position = strstr($blogUrl, 'uploads');


        if ($position !== false) {

            $replacedUrl = preg_replace('/(\/+)/', '/', $blogUrl);
            $blogUrl = $this->crmBaseUrl . str_replace('../', '', $replacedUrl);
        }

        $title = $row['title'];

        $description = $row['description'];

        return [
            "url" => $blogUrl,
            "title" => $title,
            "description" => $description
        ];
    }

    public function getGalleryImages()
    {

        $query = mysqli_query($this->connection, "SELECT gallery FROM `b2c_settings`");
        $results = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $galleryImages = $results['gallery'] ? json_decode($results['gallery'], true) : [];

        $galleryData = [];
        $j = 0;
        foreach ($galleryImages as $image) {

            $galleryData[$j]['dest_id'] = $image['dest_id'];
            $galleryData[$j]['image_url'] = $image['image_url'];
            $position = strstr($image['image_url'], 'uploads');
            if ($position !== false) {
                $replacedUrl = preg_replace('/(\/+)/', '/', $image['image_url']);
                $galleryData[$j]['image_url'] = $this->crmBaseUrl . str_replace('../', '', $replacedUrl);
            }
            $j++;
        }
        return $galleryData;
    }


    public function getB2cSettings($settingsName)
    {

        $query = mysqli_query($this->connection, "SELECT $settingsName FROM `b2c_settings` LIMIT 1");
        $row = mysqli_fetch_assoc($query);
        if ($row) {
            return $row[$settingsName];
        }

        return "";
    }

    public function getBlogs()
    {
        $query = mysqli_query($this->connection, "SELECT * FROM `b2c_blogs` where active_flag=0 ORDER BY entry_id");
        $rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
        return $rows;
    }

    public function getB2cColorScheme()
    {

        $query = mysqli_query($this->connection, "SELECT * FROM `b2c_color_scheme` LIMIT 1");
        $row = mysqli_fetch_object($query);
        if ($row) {
            return $row;
        }

        return null;
    }
}
