<?php

namespace App\Repositories;

use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class UserRepository
{

    /**
     * Get user favourite deals
     * Favourite deals should be currently running.
     *
     * @param $user
     * @param $offset
     * @return mixed
     */
    public function getFavouriteDeals($user, $offset)
    {
        $deals = array();
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        $current_day = strtolower(date("D", strtotime($current_date)));

        // $sql_for_counting_favourite_deals is for counting total favourite deals.

        $sql_for_counting_favourite_deals = "SELECT count(*) AS total_count";

        $sql_for_counting_favourite_deals .= " FROM users_favourites_deals AS ufd
            INNER JOIN deals AS d ON d.id = ufd.deal_id
            INNER JOIN stores AS s ON s.id = d.store_id
            INNER JOIN categories AS c ON c.id = d.category_id";

        $sql_for_counting_favourite_deals .= " WHERE ufd.user_id = " . $user->id . "
            AND s.active = 1
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

        $result = DB::select($sql_for_counting_favourite_deals);

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

        // $sql_for_fetching_favourite_deals is for fetching favourite deals.

        $sql_for_fetching_favourite_deals = "SELECT d.*, 
            s.name AS store_name, s.overview AS store_overview, s.address AS store_address, s.city AS store_city, s.zip_code AS store_zip_code, s.latitude AS store_latitude, s.longitude AS store_longitude, s.logo as store_logo, s.email AS store_email, s.mobile_number AS store_mobile_number, s.phone_number AS store_phone_number, 
            s.mon_open, s.mon_close, s.tue_open, s.tue_close, s.wed_open, s.wed_close, s.thu_open, s.thu_close, s.fri_open, s.fri_close, s.sat_open, s.sat_close, s.sun_open, s.sun_close, 
            s.cover_image1 AS store_cover_image1, s.cover_image2 AS store_cover_image2, s.cover_image3 AS store_cover_image3,
            dr.status AS redeem_status,
            ufd.user_id AS is_favourite";

        $sql_for_fetching_favourite_deals .= " FROM users_favourites_deals AS ufd
            INNER JOIN deals AS d ON d.id = ufd.deal_id
            INNER JOIN stores AS s ON s.id = d.store_id
            INNER JOIN categories AS c ON c.id = d.category_id
            LEFT JOIN deals_redeems AS dr ON dr.deal_id = d.id AND dr.user_id = " . $user->id . " AND dr.created_at = (SELECT MAX(created_at) FROM deals_redeems where deal_id = d.id)";

        $sql_for_fetching_favourite_deals .= " WHERE ufd.user_id = " . $user->id . " 
            AND s.active = 1
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

        $sql_for_fetching_favourite_deals .= " ORDER BY ufd.created_at DESC LIMIT " . $offset . " , " . Config::get('constants.offset');

        $result = DB::select($sql_for_fetching_favourite_deals);

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
                "is_favourite" => $deal->is_favourite ? TRUE : FALSE,
                "redeem_status" => $deal->redeem_status ? $deal->redeem_status : "",
                "store" => array(
                    "id" => $deal->store_id,
                    "name" => $deal->store_name,
                    "overview" => $deal->store_overview,
                    "address" => $deal->store_address,
                    "city" => $deal->store_city,
                    "zip_code" => $deal->store_zip_code,
                    "latitude" => $deal->store_latitude,
                    "longitude" => $deal->store_longitude,
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
     * Get user favourite stores
     *
     * @param $user
     * @param $offset
     * @return mixed
     */
    public function getFavouriteStores($user, $offset)
    {
        $stores = array();

        // $sql_for_counting_favourite_stores is for counting total favourite stores.

        $sql_for_counting_favourite_stores = "SELECT count(*) AS total_count";

        $sql_for_counting_favourite_stores .= " FROM users_favourites_stores AS ufs
            INNER JOIN stores AS s ON s.id = ufs.store_id";

        $sql_for_counting_favourite_stores .= " WHERE ufs.user_id = " . $user->id . " 
            AND s.active = 1 
            AND s.approved = 'Approved'
            AND s.deleted_at IS NULL";

        $result = DB::select($sql_for_counting_favourite_stores);

        if ($result) {
            $current_page = $offset / Config::get('constants.offset') + 1;
            $per_page = Config::get('constants.offset');
            $total_count = $result[0]->total_count;
            $total_pages = ceil($total_count / Config::get('constants.offset'));
            $stores['metadata'] = array(
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
            $stores['metadata'] = array(
                'current_page' => $current_page,
                'per_page' => $per_page,
                'total_count' => $total_count,
                'total_pages' => $total_pages
            );
        }

        // $sql_for_fetching_favourite_stores is for fetching favourite stores.

        $sql_for_fetching_favourite_stores = "SELECT s.id AS store_id, s.name AS store_name, s.overview AS store_overview, s.address AS store_address, s.city AS store_city, s.zip_code AS store_zip_code, s.latitude AS store_latitude, s.longitude AS store_longitude, s.logo as store_logo, s.email AS store_email, s.mobile_number AS store_mobile_number, s.phone_number AS store_phone_number, 
            s.mon_open, s.mon_close, s.tue_open, s.tue_close, s.wed_open, s.wed_close, s.thu_open, s.thu_close, s.fri_open, s.fri_close, s.sat_open, s.sat_close, s.sun_open, s.sun_close, 
            s.cover_image1 AS store_cover_image1, s.cover_image2 AS store_cover_image2, s.cover_image3 AS store_cover_image3,
            sar.rating,
            ufs.user_id AS is_favourite";

        $sql_for_fetching_favourite_stores .= " FROM users_favourites_stores AS ufs
            INNER JOIN stores AS s ON s.id = ufs.store_id
            LEFT JOIN stores_average_ratings AS sar ON sar.store_id = ufs.store_id";

        $sql_for_fetching_favourite_stores .= " WHERE ufs.user_id = " . $user->id . " 
            AND s.active = 1 
            AND s.approved = 'Approved'
            AND s.deleted_at IS NULL";

        $sql_for_fetching_favourite_stores .= " ORDER BY ufs.created_at DESC LIMIT " . $offset . " , " . Config::get('constants.offset');

        $result = DB::select($sql_for_fetching_favourite_stores);

        $stores['stores'] = array();

        foreach ($result as $store) {
            $store = array(
                "id" => $store->store_id,
                "name" => $store->store_name,
                "overview" => $store->store_overview,
                "address" => $store->store_address,
                "city" => $store->store_city,
                "zip_code" => $store->store_zip_code,
                "latitude" => $store->store_latitude,
                "longitude" => $store->store_longitude,
                "logo" => $store->store_logo ? $store->store_logo : "",
                "email" => $store->store_email ? $store->store_email : "",
                "mobile_number" => $store->store_mobile_number ? $store->store_mobile_number : "",
                "phone_number" => $store->store_phone_number ? $store->store_phone_number : "",
                "mon_open" => $store->mon_open,
                "mon_close" => $store->mon_close,
                "tue_open" => $store->tue_open,
                "tue_close" => $store->tue_close,
                "wed_open" => $store->wed_open,
                "wed_close" => $store->wed_close,
                "thu_open" => $store->thu_open,
                "thu_close" => $store->thu_close,
                "fri_open" => $store->fri_open,
                "fri_close" => $store->fri_close,
                "sat_open" => $store->sat_open,
                "sat_close" => $store->sat_close,
                "sun_open" => $store->sun_open,
                "sun_close" => $store->sun_close,
                "cover_image1" => $store->store_cover_image1,
                "cover_image2" => $store->store_cover_image2 ? $store->store_cover_image2 : "",
                "cover_image3" => $store->store_cover_image3 ? $store->store_cover_image3 : "",
                "is_favourite" => $store->is_favourite ? TRUE : FALSE,
                "average_rating" => array(
                    "rating" => $store->rating ? $store->rating : 5
                )
            );

            array_push($stores['stores'], $store);
        }

        return $stores;
    }

    /**
     * Get user redeems
     *
     * @param $user
     * @param $offset
     * @return mixed
     */
    public function getRedeems($user, $offset)
    {
        $deals = array();
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        $current_day = strtolower(date("D", strtotime($current_date)));

        // $sql_for_counting_redeems is for counting total redeems.

        $sql_for_counting_redeems = "SELECT count(*) AS total_count";

        $sql_for_counting_redeems .= " FROM deals_redeems AS dr
            INNER JOIN deals AS d ON d.id = dr.deal_id
            INNER JOIN stores AS s ON s.id = d.store_id
            INNER JOIN categories AS c ON c.id = d.category_id";

        $sql_for_counting_redeems .= " WHERE dr.user_id = " . $user->id . " 
            AND s.active = 1
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

        $result = DB::select($sql_for_counting_redeems);

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

        // $sql_for_fetching_redeems is for fetching user redeems.

        $sql_for_fetching_redeems = "SELECT d.*, 
            s.name AS store_name, s.overview AS store_overview, s.address AS store_address, s.city AS store_city, s.zip_code AS store_zip_code, s.latitude AS store_latitude, s.longitude AS store_longitude, s.logo as store_logo, s.email AS store_email, s.mobile_number AS store_mobile_number, s.phone_number AS store_phone_number, 
            s.mon_open, s.mon_close, s.tue_open, s.tue_close, s.wed_open, s.wed_close, s.thu_open, s.thu_close, s.fri_open, s.fri_close, s.sat_open, s.sat_close, s.sun_open, s.sun_close, 
            s.cover_image1 AS store_cover_image1, s.cover_image2 AS store_cover_image2, s.cover_image3 AS store_cover_image3,
            dr.status AS redeem_status";

        $sql_for_fetching_redeems .= " FROM deals_redeems AS dr
            INNER JOIN deals AS d ON d.id = dr.deal_id
            INNER JOIN stores AS s ON s.id = d.store_id
            INNER JOIN categories AS c ON c.id = d.category_id";

        $sql_for_fetching_redeems .= " WHERE dr.user_id = " . $user->id . " 
            AND s.active = 1
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

        $sql_for_fetching_redeems .= " ORDER BY dr.created_at DESC LIMIT " . $offset . " , " . Config::get('constants.offset');

        $result = DB::select($sql_for_fetching_redeems);

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
                "redeem_status" => $deal->redeem_status ? $deal->redeem_status : "",
                "store" => array(
                    "id" => $deal->store_id,
                    "name" => $deal->store_name,
                    "overview" => $deal->store_overview,
                    "address" => $deal->store_address,
                    "city" => $deal->store_city,
                    "zip_code" => $deal->store_zip_code,
                    "latitude" => $deal->store_latitude,
                    "longitude" => $deal->store_longitude,
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
}