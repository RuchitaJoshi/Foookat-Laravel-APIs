<?php

namespace App\Repositories;

use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DealRepository
{

    /**
     * Get running live deals
     * This method returns running live deals based on user's latitude, longitude and selected filters.
     * @param $user
     * @param $latitude
     * @param $longitude
     * @param $offset : category_id (optional), search_term (optional)
     * @param $filters
     * @return array : returns array of deals
     */
    public function getRunningLiveDeals($user, $latitude, $longitude, $offset, $filters = null)
    {
        $deals = array();
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        $current_day = strtolower(date("D", strtotime($current_date)));

        // $sql_for_counting_running_live_deals is for counting total running live deals.

        $sql_for_counting_running_live_deals = "SELECT count(*) AS total_count
            FROM stores AS s
            INNER JOIN deals AS d ON d.store_id = s.id
            INNER JOIN categories AS c ON c.id = d.category_id";

        $sql_for_counting_running_live_deals .= " WHERE s.active = 1
            AND d.active = 1 
            AND c.active = 1
            AND s.approved = 'Approved'    
            AND d.approved = 'Approved'                 
            AND d." . $current_day . " = 1 
            AND (d.start_date <= '" . $current_date . "' AND d.end_date >= '" . $current_date . "') 
            AND (d.start_time <= '" . $current_time . "' AND d.end_time >= '" . $current_time . "')          
            AND s.deleted_at IS NULL
            AND d.deleted_at IS NULL    
            AND c.deleted_at IS NULL
            AND (3959 * ACOS(COS(RADIANS(" . $latitude . ")) * COS(RADIANS(s.latitude)) * COS(RADIANS(s.longitude) - RADIANS(" . $longitude . ")) + SIN(RADIANS(" . $latitude . ")) * SIN(RADIANS(s.latitude)))) * 1.609344 < " . Config::get('constants.range');

        if (isset($filters['category_id'])) {
            $sql_for_counting_running_live_deals .= " AND d.category_id = " . $filters['category_id'];
        }

        if (isset($filters['search_term'])) {
            $sql_for_counting_running_live_deals .= " AND (s.name like '%" . Helper::mysql_escape($filters['search_term']) . "%' OR d.name LIKE '%" . Helper::mysql_escape($filters['search_term']) . "%' OR d.details LIKE '%" . Helper::mysql_escape($filters['search_term']) . "%')";
        }

        $result = DB::select($sql_for_counting_running_live_deals);

        if ($result) {
            $current_page = $offset / Config::get('constants.offset') + 1;
            $per_page = Config::get('constants.offset');
            $total_count = $result[0]->total_count;
            $total_pages = ceil($total_count / Config::get('constants.offset'));
            $deals['metadata'] = array(
                'current_page' => $current_page,
                'per_page' => $per_page,
                'total_count' => $total_count,
                'total_pages' => $total_pages
            );
        } else {
            $current_page = $offset / Config::get('constants.offset') + 1;
            $per_page = Config::get('constants.offset');
            $total_count = 0;
            $total_pages = 0;
            $deals['metadata'] = array(
                'current_page' => $current_page,
                'per_page' => $per_page,
                'total_count' => $total_count,
                'total_pages' => $total_pages
            );
        }

        // $sql_for_fetching_running_live_deals is for fetching current running live deals.

        $sql_for_fetching_running_live_deals = "SELECT d.*, 
            s.name AS store_name, s.overview AS store_overview, s.address AS store_address, s.city AS store_city, s.zip_code AS store_zip_code, s.latitude AS store_latitude, s.longitude AS store_longitude, s.logo as store_logo, s.email AS store_email, s.mobile_number AS store_mobile_number, s.phone_number AS store_phone_number, 
            s.mon_open, s.mon_close, s.tue_open, s.tue_close, s.wed_open, s.wed_close, s.thu_open, s.thu_close, s.fri_open, s.fri_close, s.sat_open, s.sat_close, s.sun_open, s.sun_close, 
            s.cover_image1 AS store_cover_image1, s.cover_image2 AS store_cover_image2, s.cover_image3 AS store_cover_image3,
            dr.status AS redeem_status,
            ufd.user_id AS is_deal_favourite,
            (3959 * ACOS(COS(RADIANS(" . $latitude . ")) * COS(RADIANS(s.latitude)) * COS(RADIANS(s.longitude) - RADIANS(" . $longitude . ")) + SIN(RADIANS(" . $latitude . ")) * SIN(RADIANS(s.latitude)))) * 1.609344 AS distance
            FROM stores AS s 
            INNER JOIN deals AS d ON d.store_id = s.id 
            INNER JOIN categories AS c ON c.id = d.category_id     
            LEFT JOIN deals_redeems AS dr ON dr.deal_id = d.id AND dr.user_id = " . $user->id . " AND dr.created_at = (SELECT MAX(created_at) FROM deals_redeems where deal_id = d.id)
            LEFT JOIN users_favourites_deals AS ufd ON ufd.deal_id = d.id AND ufd.user_id = " . $user->id;

        $sql_for_fetching_running_live_deals .= " WHERE s.active = 1
            AND d.active = 1 
            AND c.active = 1
            AND s.approved = 'Approved'    
            AND d.approved = 'Approved'                 
            AND d." . $current_day . " = 1 
            AND (d.start_date <= '" . $current_date . "' AND d.end_date >= '" . $current_date . "') 
            AND (d.start_time <= '" . $current_time . "' AND d.end_time >= '" . $current_time . "')          
            AND s.deleted_at IS NULL
            AND d.deleted_at IS NULL    
            AND c.deleted_at IS NULL";

        if (isset($filters['category_id'])) {
            $sql_for_fetching_running_live_deals .= " AND d.category_id = " . $filters['category_id'];
        }

        if (isset($filters['search_term'])) {
            $sql_for_fetching_running_live_deals .= " AND (s.name like '%" . Helper::mysql_escape($filters['search_term']) . "%' OR d.name LIKE '%" . Helper::mysql_escape($filters['search_term']) . "%' OR d.details LIKE '%" . Helper::mysql_escape($filters['search_term']) . "%')";
        }

        $sql_for_fetching_running_live_deals .= " HAVING distance < " . Config::get('constants.range') . " ORDER BY distance ASC, end_time ASC LIMIT " . $offset . " , " . Config::get('constants.offset');

        $result = DB::select($sql_for_fetching_running_live_deals);

        $deals['deals'] = array();

        foreach ($result as $deal) {
            $deal = array(
                "id" => $deal->id,
                "name" => $deal->name,
                "details" => $deal->details,
                "overview" => $deal->overview,
                "original_price" => $deal->original_price ? (double)$deal->original_price : 0,
                "percentage_off" => $deal->percentage_off ? (double)$deal->percentage_off : 0,
                "amount_off" => $deal->amount_off ? (double)$deal->amount_off : 0,
                "new_price" => $deal->new_price ? (double)$deal->new_price : 0,
                "start_date" => $deal->start_date,
                "end_date" => $deal->end_date,
                "start_time" => $deal->start_time,
                "end_time" => $deal->end_time,
                "mon" => $deal->mon ? TRUE : FALSE,
                "tue" => $deal->tue ? TRUE : FALSE,
                "wed" => $deal->wed ? TRUE : FALSE,
                "thu" => $deal->thu ? TRUE : FALSE,
                "fri" => $deal->fri ? TRUE : FALSE,
                "sat" => $deal->sat ? TRUE : FALSE,
                "sun" => $deal->sun ? TRUE : FALSE,
                "redeem_code" => $deal->redeem_code,
                "used_once" => $deal->used_once ? TRUE : FALSE,
                "cover_image1" => $deal->cover_image1,
                "cover_image2" => $deal->cover_image2 ? $deal->cover_image2 : "",
                "cover_image3" => $deal->cover_image3 ? $deal->cover_image3 : "",
                "distance" => $deal->distance,
                "is_favourite" => $deal->is_deal_favourite ? TRUE : FALSE,
                "redeem_status" => $deal->redeem_status ? $deal->redeem_status : "",
                "store" => array(
                    "id" => $deal->store_id,
                    "name" => $deal->store_name,
                    "overview" => $deal->store_overview,
                    "address" => $deal->store_address,
                    "city" => $deal->store_city,
                    "zip_code" => $deal->store_zip_code,
                    "latitude" => (double)$deal->store_latitude,
                    "longitude" => (double)$deal->store_longitude,
                    "logo" => $deal->store_logo ? $deal->store_logo : "",
                    "email" => $deal->store_email ? $deal->store_email : "",
                    "mobile_number" => $deal->store_mobile_number ? $deal->store_mobile_number : "",
                    "phone_number" => $deal->store_phone_number ? $deal->store_phone_number : "",
                    "mon_open" => $deal->mon_open,
                    "mon_close" => $deal->mon_close,
                    "tue_open" => $deal->tue_open,
                    "tue_close" => $deal->tue_close,
                    "wed_open" => $deal->wed_open,
                    "wed_close" => $deal->wed_close,
                    "thu_open" => $deal->thu_open,
                    "thu_close" => $deal->thu_close,
                    "fri_open" => $deal->fri_open,
                    "fri_close" => $deal->fri_close,
                    "sat_open" => $deal->sat_open,
                    "sat_close" => $deal->sat_close,
                    "sun_open" => $deal->sun_open,
                    "sun_close" => $deal->sun_close,
                    "cover_image1" => $deal->store_cover_image1,
                    "cover_image2" => $deal->store_cover_image2 ? $deal->store_cover_image2 : "",
                    "cover_image3" => $deal->store_cover_image3 ? $deal->store_cover_image3 : ""
                )
            );
            array_push($deals['deals'], $deal);
        }

        return $deals;
    }

    /**
     * Get starting soon deals
     * This method returns starting soon deals based on user's latitude, longitude and selected filters.
     * @param $user
     * @param $latitude
     * @param $longitude
     * @param $offset : category_id (optional), search_term (optional)
     * @param $filters
     * @return array : returns array of deals
     */
    public function getStartingSoonDeals($user, $latitude, $longitude, $offset, $filters = null)
    {
        $deals = array();
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        $current_day = strtolower(date("D", strtotime($current_date)));

        // $sql_for_counting_starting_soon_deals is for counting total starting soon deals.

        $sql_for_counting_starting_soon_deals = "SELECT count(*) AS total_count
            FROM stores AS s
            INNER JOIN deals AS d ON d.store_id = s.id
            INNER JOIN categories AS c ON c.id = d.category_id";

        $sql_for_counting_starting_soon_deals .= " WHERE s.active = 1
            AND d.active = 1 
            AND c.active = 1
            AND s.approved = 'Approved'    
            AND d.approved = 'Approved'                 
            AND d." . $current_day . " = 1  
            AND (d.start_date <= '" . $current_date . "' AND d.end_date >= '" . $current_date . "') 
            AND d.start_time > '" . $current_time . "'        
            AND s.deleted_at IS NULL
            AND d.deleted_at IS NULL    
            AND c.deleted_at IS NULL
            AND (3959 * ACOS(COS(RADIANS(" . $latitude . ")) * COS(RADIANS(s.latitude)) * COS(RADIANS(s.longitude) - RADIANS(" . $longitude . ")) + SIN(RADIANS(" . $latitude . ")) * SIN(RADIANS(s.latitude)))) * 1.609344 < " . Config::get('constants.range');

        if (isset($filters['category_id'])) {
            $sql_for_counting_starting_soon_deals .= " AND d.category_id = " . $filters['category_id'];
        }

        if (isset($filters['search_term'])) {
            $sql_for_counting_starting_soon_deals .= " AND (s.name like '%" . Helper::mysql_escape($filters['search_term']) . "%' OR d.name LIKE '%" . Helper::mysql_escape($filters['search_term']) . "%' OR d.details LIKE '%" . Helper::mysql_escape($filters['search_term']) . "%')";
        }

        $result = DB::select($sql_for_counting_starting_soon_deals);

        if ($result) {
            $current_page = $offset / Config::get('constants.offset') + 1;
            $per_page = Config::get('constants.offset');
            $total_count = $result[0]->total_count;
            $total_pages = ceil($total_count / Config::get('constants.offset'));
            $deals['metadata'] = array(
                'current_page' => $current_page,
                'per_page' => $per_page,
                'total_count' => $total_count,
                'total_pages' => $total_pages
            );
        } else {
            $current_page = $offset / Config::get('constants.offset') + 1;
            $per_page = Config::get('constants.offset');
            $total_count = 0;
            $total_pages = 0;
            $deals['metadata'] = array(
                'current_page' => $current_page,
                'per_page' => $per_page,
                'total_count' => $total_count,
                'total_pages' => $total_pages
            );
        }

        // $sql_for_fetching_starting_soon_deals is for fetching starting soon deals.

        $sql_for_fetching_starting_soon_deals = "SELECT d.*, 
            s.name AS store_name, s.overview AS store_overview, s.address AS store_address, s.city AS store_city, s.zip_code AS store_zip_code, s.latitude AS store_latitude, s.longitude AS store_longitude, s.logo as store_logo, s.email AS store_email, s.mobile_number AS store_mobile_number, s.phone_number AS store_phone_number, 
            s.mon_open, s.mon_close, s.tue_open, s.tue_close, s.wed_open, s.wed_close, s.thu_open, s.thu_close, s.fri_open, s.fri_close, s.sat_open, s.sat_close, s.sun_open, s.sun_close, 
            s.cover_image1 AS store_cover_image1, s.cover_image2 AS store_cover_image2, s.cover_image3 AS store_cover_image3,
            dr.status AS redeem_status,
            ufd.user_id AS is_deal_favourite,
            (3959 * ACOS(COS(RADIANS(" . $latitude . ")) * COS(RADIANS(s.latitude)) * COS(RADIANS(s.longitude) - RADIANS(" . $longitude . ")) + SIN(RADIANS(" . $latitude . ")) * SIN(RADIANS(s.latitude)))) * 1.609344 AS distance
            FROM stores AS s 
            INNER JOIN deals AS d ON d.store_id = s.id 
            INNER JOIN categories AS c ON c.id = d.category_id          
            LEFT JOIN deals_redeems AS dr ON dr.deal_id = d.id AND dr.user_id = " . $user->id . " AND dr.created_at = (SELECT MAX(created_at) FROM deals_redeems where deal_id = d.id)
            LEFT JOIN users_favourites_deals AS ufd ON ufd.deal_id = d.id AND ufd.user_id = " . $user->id;

        $sql_for_fetching_starting_soon_deals .= " WHERE s.active = 1
            AND d.active = 1 
            AND c.active = 1
            AND s.approved = 'Approved'    
            AND d.approved = 'Approved'                 
            AND d." . $current_day . " = 1  
            AND (d.start_date <= '" . $current_date . "' AND d.end_date >= '" . $current_date . "') 
            AND d.start_time > '" . $current_time . "'        
            AND s.deleted_at IS NULL
            AND d.deleted_at IS NULL    
            AND c.deleted_at IS NULL";

        if (isset($filters['category_id'])) {
            $sql_for_fetching_starting_soon_deals .= " AND d.category_id = " . $filters['category_id'];
        }

        if (isset($filters['search_term'])) {
            $sql_for_fetching_starting_soon_deals .= " AND (s.name like '%" . Helper::mysql_escape($filters['search_term']) . "%' OR d.name LIKE '%" . Helper::mysql_escape($filters['search_term']) . "%' OR d.details LIKE '%" . Helper::mysql_escape($filters['search_term']) . "%')";
        }

        $sql_for_fetching_starting_soon_deals .= " HAVING distance < " . Config::get('constants.range') . " ORDER BY distance ASC, start_time ASC LIMIT " . $offset . " , " . Config::get('constants.offset');

        $result = DB::select($sql_for_fetching_starting_soon_deals);

        $deals['deals'] = array();

        foreach ($result as $deal) {
            $deal = array(
                "id" => $deal->id,
                "name" => $deal->name,
                "details" => $deal->details,
                "overview" => $deal->overview,
                "original_price" => $deal->original_price ? (double)$deal->original_price : 0,
                "percentage_off" => $deal->percentage_off ? (double)$deal->percentage_off : 0,
                "amount_off" => $deal->amount_off ? (double)$deal->amount_off : 0,
                "new_price" => $deal->new_price ? (double)$deal->new_price : 0,
                "start_date" => $deal->start_date,
                "end_date" => $deal->end_date,
                "start_time" => $deal->start_time,
                "end_time" => $deal->end_time,
                "mon" => $deal->mon ? TRUE : FALSE,
                "tue" => $deal->tue ? TRUE : FALSE,
                "wed" => $deal->wed ? TRUE : FALSE,
                "thu" => $deal->thu ? TRUE : FALSE,
                "fri" => $deal->fri ? TRUE : FALSE,
                "sat" => $deal->sat ? TRUE : FALSE,
                "sun" => $deal->sun ? TRUE : FALSE,
                "redeem_code" => $deal->redeem_code,
                "used_once" => $deal->used_once ? TRUE : FALSE,
                "cover_image1" => $deal->cover_image1,
                "cover_image2" => $deal->cover_image2 ? $deal->cover_image2 : "",
                "cover_image3" => $deal->cover_image3 ? $deal->cover_image3 : "",
                "distance" => $deal->distance,
                "is_favourite" => $deal->is_deal_favourite ? TRUE : FALSE,
                "redeem_status" => $deal->redeem_status ? $deal->redeem_status : "",
                "store" => array(
                    "id" => $deal->store_id,
                    "name" => $deal->store_name,
                    "overview" => $deal->store_overview,
                    "address" => $deal->store_address,
                    "city" => $deal->store_city,
                    "zip_code" => $deal->store_zip_code,
                    "latitude" => (double)$deal->store_latitude,
                    "longitude" => (double)$deal->store_longitude,
                    "logo" => $deal->store_logo ? $deal->store_logo : "",
                    "email" => $deal->store_email ? $deal->store_email : "",
                    "mobile_number" => $deal->store_mobile_number ? $deal->store_mobile_number : "",
                    "phone_number" => $deal->store_phone_number ? $deal->store_phone_number : "",
                    "mon_open" => $deal->mon_open,
                    "mon_close" => $deal->mon_close,
                    "tue_open" => $deal->tue_open,
                    "tue_close" => $deal->tue_close,
                    "wed_open" => $deal->wed_open,
                    "wed_close" => $deal->wed_close,
                    "thu_open" => $deal->thu_open,
                    "thu_close" => $deal->thu_close,
                    "fri_open" => $deal->fri_open,
                    "fri_close" => $deal->fri_close,
                    "sat_open" => $deal->sat_open,
                    "sat_close" => $deal->sat_close,
                    "sun_open" => $deal->sun_open,
                    "sun_close" => $deal->sun_close,
                    "cover_image1" => $deal->store_cover_image1,
                    "cover_image2" => $deal->store_cover_image2 ? $deal->store_cover_image2 : "",
                    "cover_image3" => $deal->store_cover_image3 ? $deal->store_cover_image3 : ""
                )
            );
            array_push($deals['deals'], $deal);
        }

        return $deals;
    }

    /**
     * Get a deal
     *
     * @param $user
     * @param $deal_id
     * @return array : deal
     */
    public function getDeal($user, $deal_id)
    {
        $sql = "SELECT d.*, 
            s.name AS store_name, s.overview AS store_overview, s.address AS store_address, s.city AS store_city, s.zip_code AS store_zip_code, s.latitude AS store_latitude, s.longitude AS store_longitude, s.logo as store_logo, s.email AS store_email, s.mobile_number AS store_mobile_number, s.phone_number AS store_phone_number, 
            s.mon_open, s.mon_close, s.tue_open, s.tue_close, s.wed_open, s.wed_close, s.thu_open, s.thu_close, s.fri_open, s.fri_close, s.sat_open, s.sat_close, s.sun_open, s.sun_close, 
            s.cover_image1 AS store_cover_image1, s.cover_image2 AS store_cover_image2, s.cover_image3 AS store_cover_image3,
            dr.status AS redeem_status,
            ufd.user_id AS is_favourite";

        $sql .= " FROM stores AS s 
            INNER JOIN deals AS d ON d.store_id = s.id 
            INNER JOIN categories AS c ON c.id = d.category_id          
            LEFT JOIN deals_redeems AS dr ON dr.deal_id = d.id AND dr.user_id = " . $user->id . " AND dr.created_at = (SELECT MAX(created_at) FROM deals_redeems where deal_id = d.id)
            LEFT JOIN users_favourites_deals AS ufd ON ufd.deal_id = d.id AND ufd.user_id = " . $user->id;

        $sql .= " WHERE d.id = " . $deal_id . " 
            AND s.active = 1
            AND d.active = 1 
            AND c.active = 1
            AND s.approved = 'Approved'    
            AND d.approved = 'Approved'                 
            AND s.deleted_at IS NULL
            AND d.deleted_at IS NULL    
            AND c.deleted_at IS NULL";

        $result = DB::select($sql);

        if ($result) {
            $deal = $result[0];
            $response = array(
                "id" => $deal->id,
                "name" => $deal->name,
                "details" => $deal->details,
                "overview" => $deal->overview,
                "original_price" => $deal->original_price ? (double)$deal->original_price : 0,
                "percentage_off" => $deal->percentage_off ? (double)$deal->percentage_off : 0,
                "amount_off" => $deal->amount_off ? (double)$deal->amount_off : 0,
                "new_price" => $deal->new_price ? (double)$deal->new_price : 0,
                "start_date" => $deal->start_date,
                "end_date" => $deal->end_date,
                "start_time" => $deal->start_time,
                "end_time" => $deal->end_time,
                "mon" => $deal->mon ? TRUE : FALSE,
                "tue" => $deal->tue ? TRUE : FALSE,
                "wed" => $deal->wed ? TRUE : FALSE,
                "thu" => $deal->thu ? TRUE : FALSE,
                "fri" => $deal->fri ? TRUE : FALSE,
                "sat" => $deal->sat ? TRUE : FALSE,
                "sun" => $deal->sun ? TRUE : FALSE,
                "redeem_code" => $deal->redeem_code,
                "used_once" => $deal->used_once ? TRUE : FALSE,
                "cover_image1" => $deal->cover_image1,
                "cover_image2" => $deal->cover_image2 ? $deal->cover_image2 : "",
                "cover_image3" => $deal->cover_image3 ? $deal->cover_image3 : "",
                "is_favourite" => $deal->is_favourite ? TRUE : FALSE,
                "redeem_status" => $deal->redeem_status ? $deal->redeem_status : "",
                "store" => array(
                    "id" => $deal->store_id,
                    "name" => $deal->store_name,
                    "overview" => $deal->store_overview,
                    "address" => $deal->store_address,
                    "city" => $deal->store_city,
                    "zip_code" => $deal->store_zip_code,
                    "latitude" => (double)$deal->store_latitude,
                    "longitude" => (double)$deal->store_longitude,
                    "logo" => $deal->store_logo ? $deal->store_logo : "",
                    "email" => $deal->store_email ? $deal->store_email : "",
                    "mobile_number" => $deal->store_mobile_number ? $deal->store_mobile_number : "",
                    "phone_number" => $deal->store_phone_number ? $deal->store_phone_number : "",
                    "mon_open" => $deal->mon_open,
                    "mon_close" => $deal->mon_close,
                    "tue_open" => $deal->tue_open,
                    "tue_close" => $deal->tue_close,
                    "wed_open" => $deal->wed_open,
                    "wed_close" => $deal->wed_close,
                    "thu_open" => $deal->thu_open,
                    "thu_close" => $deal->thu_close,
                    "fri_open" => $deal->fri_open,
                    "fri_close" => $deal->fri_close,
                    "sat_open" => $deal->sat_open,
                    "sat_close" => $deal->sat_close,
                    "sun_open" => $deal->sun_open,
                    "sun_close" => $deal->sun_close,
                    "cover_image1" => $deal->store_cover_image1,
                    "cover_image2" => $deal->store_cover_image2 ? $deal->store_cover_image2 : "",
                    "cover_image3" => $deal->store_cover_image3 ? $deal->store_cover_image3 : ""
                )
            );
            return $response;
        } else {
            return null;
        }
    }
}