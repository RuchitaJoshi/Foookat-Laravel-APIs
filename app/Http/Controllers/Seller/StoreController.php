<?php

namespace App\Http\Controllers\Seller;

use App\City;
use App\Store;
use App\Business;
use App\Http\Requests;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\SellerRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as IlluminateResponse;

class StoreController extends Controller
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
     * Handle a create store request.
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postCreateStore(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2|max:255',
                'overview' => 'required',
                'address' => 'required',
                'city_id' => 'required|exists:cities,id,deleted_at,NULL',
                'zip_code' => 'required|numeric',
                'mon_open' => 'required',
                'mon_close' => 'required',
                'tue_open' => 'required',
                'tue_close' => 'required',
                'wed_open' => 'required',
                'wed_close' => 'required',
                'thu_open' => 'required',
                'thu_close' => 'required',
                'fri_open' => 'required',
                'fri_close' => 'required',
                'sat_open' => 'required',
                'sat_close' => 'required',
                'sun_open' => 'required',
                'sun_close' => 'required',
                'cover_image1' => 'required',
                'league_id' => 'required|exists:leagues,id,deleted_at,NULL',
                'business_id' => 'required|exists:businesses,id,deleted_at,NULL'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => FALSE,
                    'error' => [
                        'code' => 10,
                        'messages' => $validator->errors()->all()
                    ]
                ], IlluminateResponse::HTTP_BAD_REQUEST
            );
        }

        $user = Auth::User();

        $business = Business::find($request->get('business_id'));

        if ($user->is($business->id, 'Business Owner')) {
            $city = City::find($request->get('city_id'));

            $geocode = Helper::getGeocode($request->get('address'), $request->get('zip_code'));

            Store::create(['name' => $request->get('name'),
                'overview' => $request->get('overview'),
                'address' => $request->get('address'),
                'city' => $city->name,
                'zip_code' => $request->get('zip_code'),
                'latitude' => $geocode['latitude'],
                'longitude' => $geocode['longitude'],
                'logo' => $request->get('logo') ? $request->get('logo') : null,
                'email' => $request->get('email') ? $request->get('email') : null,
                'mobile_number' => $request->get('mobile_number') ? $request->get('mobile_number') : null,
                'phone_number' => $request->get('phone_number') ? $request->get('phone_number') : null,
                'mon_open' => $request->get('mon_open'),
                'mon_close' => $request->get('mon_close'),
                'tue_open' => $request->get('tue_open'),
                'tue_close' => $request->get('tue_close'),
                'wed_open' => $request->get('wed_open'),
                'wed_close' => $request->get('wed_close'),
                'thu_open' => $request->get('thu_open'),
                'thu_close' => $request->get('thu_close'),
                'fri_open' => $request->get('fri_open'),
                'fri_close' => $request->get('fri_close'),
                'sat_open' => $request->get('sat_open'),
                'sat_close' => $request->get('sat_close'),
                'sun_open' => $request->get('sun_open'),
                'sun_close' => $request->get('sun_close'),
                'cover_image1' => $request->get('cover_image1'),
                'cover_image2' => $request->get('cover_image2') ? $request->get('cover_image2') : null,
                'cover_image3' => $request->get('cover_image3') ? $request->get('cover_image3') : null,
                'league_id' => $request->get('league_id'),
                'city_id' => $city->id,
                'business_id' => $request->get('business_id'),
            ]);

            return response()->json(['success' => TRUE, 'data' => ["message" => "Thank you for adding a store. We will approve it asap."]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! You don't have permission to add a store."]], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Handle an edit store request.
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postEditStore(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2|max:255',
                'overview' => 'required',
                'address' => 'required',
                'city_id' => 'required|exists:cities,id,deleted_at,NULL',
                'zip_code' => 'required|numeric',
                'mon_open' => 'required',
                'mon_close' => 'required',
                'tue_open' => 'required',
                'tue_close' => 'required',
                'wed_open' => 'required',
                'wed_close' => 'required',
                'thu_open' => 'required',
                'thu_close' => 'required',
                'fri_open' => 'required',
                'fri_close' => 'required',
                'sat_open' => 'required',
                'sat_close' => 'required',
                'sun_open' => 'required',
                'sun_close' => 'required',
                'cover_image1' => 'required',
                'league_id' => 'required|exists:leagues,id,deleted_at,NULL',
                'store_id' => 'required|exists:stores,id,deleted_at,NULL'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => FALSE,
                    'error' => [
                        'code' => 10,
                        'messages' => $validator->errors()->all()
                    ]
                ], IlluminateResponse::HTTP_BAD_REQUEST
            );
        }

        $user = Auth::User();

        $store = Store::find($request->get('store_id'));

        if ($user->is($store->business_id, 'Business Owner')) {
            $city = City::find($request->get('city_id'));

            $geocode = Helper::getGeocode($request->get('address'), $request->get('zip_code'));

            $store->update(['name' => $request->get('name'),
                'overview' => $request->get('overview'),
                'address' => $request->get('address'),
                'city' => $city->name,
                'zip_code' => $request->get('zip_code'),
                'latitude' => $geocode['latitude'],
                'longitude' => $geocode['longitude'],
                'logo' => $request->get('logo') ? $request->get('logo') : null,
                'email' => $request->get('email') ? $request->get('email') : null,
                'mobile_number' => $request->get('mobile_number') ? $request->get('mobile_number') : null,
                'phone_number' => $request->get('phone_number') ? $request->get('phone_number') : null,
                'mon_open' => $request->get('mon_open'),
                'mon_close' => $request->get('mon_close'),
                'tue_open' => $request->get('tue_open'),
                'tue_close' => $request->get('tue_close'),
                'wed_open' => $request->get('wed_open'),
                'wed_close' => $request->get('wed_close'),
                'thu_open' => $request->get('thu_open'),
                'thu_close' => $request->get('thu_close'),
                'fri_open' => $request->get('fri_open'),
                'fri_close' => $request->get('fri_close'),
                'sat_open' => $request->get('sat_open'),
                'sat_close' => $request->get('sat_close'),
                'sun_open' => $request->get('sun_open'),
                'sun_close' => $request->get('sun_close'),
                'cover_image1' => $request->get('cover_image1'),
                'cover_image2' => $request->get('cover_image2') ? $request->get('cover_image2') : null,
                'cover_image3' => $request->get('cover_image3') ? $request->get('cover_image3') : null,
                'league_id' => $request->get('league_id'),
                'city_id' => $city->id
            ]);

            return response()->json(['success' => TRUE, 'data' => ["message" => "Your store has been successfully updated."]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! You don't have permission to update this store."]], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Handle a store request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function getStore(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'store_id' => 'required|exists:stores,id,deleted_at,NULL'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => FALSE,
                    'error' => [
                        'code' => 10,
                        'messages' => $validator->errors()->all()
                    ]
                ], IlluminateResponse::HTTP_BAD_REQUEST
            );
        }

        $user = Auth::User();

        $store = Store::find($request->get('store_id'));

        if ($user->is($store->business_id, 'Business Owner')) {
            $store->averageRating;

            $stats = $this->sellerRepository->getStoreStats($store->id);

            $store->stats = $stats;

            return response()->json(['success' => TRUE, 'data' => ["store" => $store]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! You don't have permission to access this store."]], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Handle a request to fetch store's deals
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function getDeals(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'store_id' => 'required|exists:stores,id,deleted_at,NULL'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => FALSE,
                    'error' => [
                        'code' => 10,
                        'messages' => $validator->errors()->all()
                    ]
                ], IlluminateResponse::HTTP_BAD_REQUEST
            );
        }

        $user = Auth::User();

        $store = Store::find($request->get('store_id'));

        if ($user->is($store->business_id, 'Business Owner')) {
            $deals = array();

            foreach ($store->deals as $deal) {
                $stats = $this->sellerRepository->getDealStats($deal->id);

                $deal->stats = $stats;

                array_push($deals, $deal);
            }

            return response()->json(['success' => TRUE, 'data' => ["deals" => $deals]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! You don't have permission to access the deals."]], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
    }
}
