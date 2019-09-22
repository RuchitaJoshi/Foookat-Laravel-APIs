<?php

namespace App\Http\Controllers\Seller;

use App\Business;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Repositories\SellerRepository;
use Illuminate\Http\Response as IlluminateResponse;

class SellerController extends Controller
{
    /**
     * @var SellerRepository
     */
    private $sellerRepository;

    /**
     * Create a new controller instance.
     *
     * @param SellerRepository $sellerRepository
     */
    public function __construct(SellerRepository $sellerRepository)
    {
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * Handle seller preferences request.
     *
     * @return IlluminateResponse
     */
    public function getPreferences()
    {
        $user = Auth::User();

        $businesses = array();

        foreach ($user->businesses as $business) {
            $business = Business::find($business->id);

            if ($user->is($business->id, 'Business Owner')) {
                foreach ($business->stores as $store) {
                    $store->averageRating;

                    $stats = $this->sellerRepository->getStoreStats($store->id);

                    $store->stats = $stats;
                }
            }

            array_push($businesses, $business);
        }

        return response()->json(['success' => TRUE, 'data' => ['businesses' => $businesses]], IlluminateResponse::HTTP_OK);
    }
}
