<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class StoreRepository
{
    /**
     * Get a store
     *
     * @param $user
     * @param $store_id
     * @return array : store
     */
    public function getStore($user, $store_id)
    {
        $sql = "SELECT s.id, s.name AS store_name, s.overview AS store_overview, s.address AS store_address, s.city AS store_city, s.zip_code AS store_zip_code, s.latitude AS store_latitude, s.longitude AS store_longitude, s.logo as store_logo, s.email AS store_email, s.mobile_number AS store_mobile_number, s.phone_number AS store_phone_number,
            s.mon_open, s.mon_close, s.tue_open, s.tue_close, s.wed_open, s.wed_close, s.thu_open, s.thu_close, s.fri_open, s.fri_close, s.sat_open, s.sat_close, s.sun_open, s.sun_close,
            s.cover_image1 AS store_cover_image1, s.cover_image2 AS store_cover_image2, s.cover_image3 AS store_cover_image3,
            sar.rating,
            ufs.user_id AS is_favourite";

        $sql .= " FROM stores AS s 
            LEFT JOIN stores_average_ratings AS sar ON sar.store_id = s.id 
            LEFT JOIN users_favourites_stores AS ufs ON ufs.store_id = s.id AND ufs.user_id = " . $user->id;

        $sql .= " WHERE s.id = " . $store_id . " 
            AND s.active = 1
            AND s.approved = 'Approved' 
            AND s.deleted_at IS NULL";

        $result = DB::select($sql);

        if ($result) {
            $store = $result[0];
            //get store reviews
            $reviews = $this->getStoreReviews($store_id);
            //get available deals
            $availableDeals = $this->getAvailableDeals($user, $store_id);
            $response = array(
                "id" => $store->id,
                "name" => $store->store_name,
                "overview" => $store->store_overview,
                "address" => $store->store_address,
                "city" => $store->store_city,
                "zip_code" => $store->store_zip_code,
                "latitude" => (double)$store->store_latitude,
                "longitude" => (double)$store->store_longitude,
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
                ),
                "reviews" => $reviews,
                "deals" => $availableDeals
            );
            return $response;
        } else {
            return "";
        }
    }

    /**
     * Get latest 3 store reviews
     *
     * @param $store_id
     * @return mixed
     */
    public function getStoreReviews($store_id)
    {
        $reviews = DB::table('stores_reviews as sr')
            ->join('users as u', 'sr.user_id', '=', 'u.id')
            ->where('sr.store_id', '=', $store_id)
            ->where('u.deleted_at', '=', NULL)
            ->orderby('sr.created_at', 'DESC')
            ->select('u.name', 'u.profile_picture', 'sr.user_id', 'sr.rating', 'sr.review', 'sr.created_at')
            ->limit(3)
            ->get();

        foreach ($reviews as $review) {
            $review->user = array('name' => $review->name, 'profile_picture' => $review->profile_picture);
            unset($review->user_id);
            unset($review->name);
            unset($review->profile_picture);
        }

        return $reviews;
    }

    /**
     * Get available running or starting soon deals
     *
     * @param $user
     * @param $store_id
     * @return mixed
     */
    public function getAvailableDeals($user, $store_id)
    {
        $deals = array();
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        $current_day = strtolower(date("D", strtotime($current_date)));

        // $sql_for_fetching_running_live_deals is for fetching running live deals.

        $sql_for_fetching_running_live_deals = "SELECT d.*, 
            dr.status AS redeem_status";

        $sql_for_fetching_running_live_deals .= " FROM stores AS s 
            INNER JOIN deals AS d ON d.store_id = s.id 
            INNER JOIN categories AS c ON c.id = d.category_id 
            LEFT JOIN deals_redeems AS dr ON dr.deal_id = d.id AND dr.user_id = " . $user->id . " AND dr.created_at = (SELECT MAX(created_at) FROM deals_redeems where deal_id = d.id)";

        $sql_for_fetching_running_live_deals .= " WHERE s.id = " . $store_id . " 
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
            AND c.deleted_at IS NULL
            ORDER BY end_time ASC";

        // $sql_for_fetching_starting_soon_deals is for fetching starting soon deals.

        $sql_for_fetching_starting_soon_deals = "SELECT d.*, 
            dr.status AS redeem_status";

        $sql_for_fetching_starting_soon_deals .= " FROM stores AS s 
            INNER JOIN deals AS d ON d.store_id = s.id 
            INNER JOIN categories AS c ON c.id = d.category_id 
            LEFT JOIN deals_redeems AS dr ON dr.deal_id = d.id AND dr.user_id = " . $user->id . " AND dr.created_at = (SELECT MAX(created_at) FROM deals_redeems where deal_id = d.id)";

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
            AND c.deleted_at IS NULL
            ORDER BY start_time ASC";

        $result = DB::select("( " . $sql_for_fetching_running_live_deals . " ) UNION ( " . $sql_for_fetching_starting_soon_deals . ")");

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
                "redeem_status" => $deal->redeem_status ? $deal->redeem_status : ""
            );
            array_push($deals, $deal);
        }

        return $deals;
    }

}