<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class SellerRepository
{
    /**
     * Get stats of a deal
     *
     * @param $deal_id
     * @return array
     */
    public function getDealStats($deal_id)
    {
        $current_date = date('Y-m-d');
        $day = date('w');
        $week_start = date('Y-m-d', strtotime('-' . $day . ' days'));

        // $sql_for_today_total_claims is for counting today's total claims.
        $sql_for_today_total_claims = "SELECT count(d.id) AS today_claims";
        $sql_for_today_total_claims .= " FROM deals AS d INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.id = " . $deal_id . " AND dr.status = 'Claimed' AND dr.updated_at >= '" . $current_date . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_today_total_claims);
        $today_claims = $result[0]->today_claims ? $result[0]->today_claims : 0;

        // $sql_for_weekly_total_claims is for counting weekly total claims.
        $sql_for_weekly_total_claims = "SELECT count(d.id) AS weekly_claims";
        $sql_for_weekly_total_claims .= " FROM deals AS d INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.id = " . $deal_id . " AND dr.status = 'Claimed' AND dr.updated_at >= '" . $week_start . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_weekly_total_claims);
        $weekly_claims = $result[0]->weekly_claims ? $result[0]->weekly_claims : 0;

        // $sql_for_total_claims is for counting total claims.
        $sql_for_total_claims = "SELECT count(d.id) AS total_claims";
        $sql_for_total_claims .= " FROM deals AS d INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.id = " . $deal_id . " AND dr.status = 'Claimed' AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_total_claims);
        $total_claims = $result[0]->total_claims ? $result[0]->total_claims : 0;

        // $sql_for_today_total_redeems is for counting today's total redeems.
        $sql_for_today_total_redeems = "SELECT count(d.id) AS today_redeems";
        $sql_for_today_total_redeems .= " FROM deals AS d INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.id = " . $deal_id . " AND dr.status = 'Redeemed' AND dr.updated_at >= '" . $current_date . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_today_total_redeems);
        $today_redeems = $result[0]->today_redeems ? $result[0]->today_redeems : 0;

        // $sql_for_weekly_total_redeems is for counting weekly total redeems.
        $sql_for_weekly_total_redeems = "SELECT count(d.id) AS weekly_redeems";
        $sql_for_weekly_total_redeems .= " FROM deals AS d INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.id = " . $deal_id . " AND dr.status = 'Redeemed' AND dr.updated_at >= '" . $week_start . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_weekly_total_redeems);
        $weekly_redeems = $result[0]->weekly_redeems ? $result[0]->weekly_redeems : 0;

        // $sql_for_total_claims is for counting total redeems.
        $sql_for_total_redeems = "SELECT count(d.id) AS total_redeems";
        $sql_for_total_redeems .= " FROM deals AS d INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.id = " . $deal_id . " AND dr.status = 'Redeemed' AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_total_redeems);
        $total_redeems = $result[0]->total_redeems ? $result[0]->total_redeems : 0;

        // $sql_for_today_total_revenue is for counting today's total revenue.
        $sql_for_today_total_revenue = "SELECT sum(d.new_price) AS today_revenue";
        $sql_for_today_total_revenue .= " FROM deals AS d INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.id = " . $deal_id . " AND dr.status = 'Redeemed' AND dr.updated_at >= '" . $current_date . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_today_total_revenue);
        $today_revenue = $result[0]->today_revenue ? $result[0]->today_revenue : 0;

        // $sql_for_weekly_total_revenue is for counting weekly total revenue.
        $sql_for_weekly_total_revenue = "SELECT sum(d.new_price) AS weekly_revenue";
        $sql_for_weekly_total_revenue .= " FROM deals AS d INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.id = " . $deal_id . " AND dr.status = 'Redeemed' AND dr.updated_at >= '" . $week_start . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_weekly_total_revenue);
        $weekly_revenue = $result[0]->weekly_revenue ? $result[0]->weekly_revenue : 0;

        // $sql_for_total_revenue is for counting total revenue.
        $sql_for_total_revenue = "SELECT sum(d.new_price) AS total_revenue";
        $sql_for_total_revenue .= " FROM deals AS d INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.id = " . $deal_id . " AND dr.status = 'Redeemed' AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_total_revenue);
        $total_revenue = $result[0]->total_revenue ? $result[0]->total_revenue : 0;

        return array('today' => array('claims' => $today_claims, 'redeems' => $today_redeems, 'revenue' => $today_revenue), 'this_week' => array('claims' => $weekly_claims, 'redeems' => $weekly_redeems, 'revenue' => $weekly_revenue), 'total' => array('claims' => $total_claims, 'redeems' => $total_redeems, 'revenue' => $total_revenue));
    }

    /**
     * Get stats of a store
     *
     * @param $store_id
     * @return array
     */
    public function getStoreStats($store_id)
    {
        $current_date = date('Y-m-d');
        $day = date('w');
        $week_start = date('Y-m-d', strtotime('-' . $day . ' days'));

        // $sql_for_today_total_claims is for counting today's total claims.
        $sql_for_today_total_claims = "SELECT count(d.id) AS today_claims";
        $sql_for_today_total_claims .= " FROM deals AS d INNER JOIN stores AS s ON d.store_id = s.id INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.store_id = " . $store_id . " AND dr.status = 'Claimed' AND dr.updated_at >= '" . $current_date . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_today_total_claims);
        $today_claims = $result[0]->today_claims ? $result[0]->today_claims : 0;

        // $sql_for_weekly_total_claims is for counting weekly total claims.
        $sql_for_weekly_total_claims = "SELECT count(d.id) AS weekly_claims";
        $sql_for_weekly_total_claims .= " FROM deals AS d INNER JOIN stores AS s ON d.store_id = s.id INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.store_id = " . $store_id . " AND dr.status = 'Claimed' AND dr.updated_at >= '" . $week_start . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_weekly_total_claims);
        $weekly_claims = $result[0]->weekly_claims ? $result[0]->weekly_claims : 0;

        // $sql_for_total_claims is for counting total claims.
        $sql_for_total_claims = "SELECT count(d.id) AS total_claims";
        $sql_for_total_claims .= " FROM deals AS d INNER JOIN stores AS s ON d.store_id = s.id INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.store_id = " . $store_id . " AND dr.status = 'Claimed' AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_total_claims);
        $total_claims = $result[0]->total_claims ? $result[0]->total_claims : 0;

        // $sql_for_today_total_redeems is for counting today's total redeems.
        $sql_for_today_total_redeems = "SELECT count(d.id) AS today_redeems";
        $sql_for_today_total_redeems .= " FROM deals AS d INNER JOIN stores AS s ON d.store_id = s.id INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.store_id = " . $store_id . " AND dr.status = 'Redeemed' AND dr.updated_at >= '" . $current_date . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_today_total_redeems);
        $today_redeems = $result[0]->today_redeems ? $result[0]->today_redeems : 0;

        // $sql_for_weekly_total_redeems is for counting weekly total redeems.
        $sql_for_weekly_total_redeems = "SELECT count(d.id) AS weekly_redeems";
        $sql_for_weekly_total_redeems .= " FROM deals AS d INNER JOIN stores AS s ON d.store_id = s.id INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.store_id = " . $store_id . " AND dr.status = 'Redeemed' AND dr.updated_at >= '" . $week_start . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_weekly_total_redeems);
        $weekly_redeems = $result[0]->weekly_redeems ? $result[0]->weekly_redeems : 0;

        // $sql_for_total_claims is for counting total redeems.
        $sql_for_total_redeems = "SELECT count(d.id) AS total_redeems";
        $sql_for_total_redeems .= " FROM deals AS d INNER JOIN stores AS s ON d.store_id = s.id INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.store_id = " . $store_id . " AND dr.status = 'Redeemed' AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_total_redeems);
        $total_redeems = $result[0]->total_redeems ? $result[0]->total_redeems : 0;

        // $sql_for_today_total_revenue is for counting today's total revenue.
        $sql_for_today_total_revenue = "SELECT sum(d.new_price) AS today_revenue";
        $sql_for_today_total_revenue .= " FROM deals AS d INNER JOIN stores AS s ON d.store_id = s.id INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.store_id = " . $store_id . " AND dr.status = 'Redeemed' AND dr.updated_at >= '" . $current_date . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_today_total_revenue);
        $today_revenue = $result[0]->today_revenue ? $result[0]->today_revenue : 0;

        // $sql_for_weekly_total_revenue is for counting weekly total revenue.
        $sql_for_weekly_total_revenue = "SELECT sum(d.new_price) AS weekly_revenue";
        $sql_for_weekly_total_revenue .= " FROM deals AS d INNER JOIN stores AS s ON d.store_id = s.id INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.store_id = " . $store_id . " AND dr.status = 'Redeemed' AND dr.updated_at >= '" . $week_start . "'AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_weekly_total_revenue);
        $weekly_revenue = $result[0]->weekly_revenue ? $result[0]->weekly_revenue : 0;

        // $sql_for_total_revenue is for counting total revenue.
        $sql_for_total_revenue = "SELECT sum(d.new_price) AS total_revenue";
        $sql_for_total_revenue .= " FROM deals AS d INNER JOIN stores AS s ON d.store_id = s.id INNER JOIN deals_redeems AS dr ON dr.deal_id = d.id WHERE d.store_id = " . $store_id . " AND dr.status = 'Redeemed' AND d.deleted_at IS NULL";
        $result = DB::select($sql_for_total_revenue);
        $total_revenue = $result[0]->total_revenue ? $result[0]->total_revenue : 0;

        return array('today' => array('claims' => $today_claims, 'redeems' => $today_redeems, 'revenue' => $today_revenue), 'this_week' => array('claims' => $weekly_claims, 'redeems' => $weekly_redeems, 'revenue' => $weekly_revenue), 'total' => array('claims' => $total_claims, 'redeems' => $total_redeems, 'revenue' => $total_revenue));
    }
}