<?php

namespace App\Http\Controllers\Seller;

use App\Deal;
use App\Role;
use App\Store;
use App\Http\Requests;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\SellerRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as IlluminateResponse;

class DealController extends Controller
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
     * Handle a create deal request.
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postCreateDeal(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2|max:255',
                'details' => 'required',
                'overview' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'mon' => 'required|boolean',
                'tue' => 'required|boolean',
                'wed' => 'required|boolean',
                'thu' => 'required|boolean',
                'fri' => 'required|boolean',
                'sat' => 'required|boolean',
                'sun' => 'required|boolean',
                'redeem_code' => 'required|max:8',
                'used_once' => 'required|boolean',
                'cover_image1' => 'required',
                'category_id' => 'required|exists:categories,id,deleted_at,NULL',
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
            Deal::create(['name' => $request->get('name'),
                'details' => $request->get('details'),
                'overview' => $request->get('overview'),
                'original_price' => $request->get('original_price') ? $request->get('original_price') : null,
                'percentage_off' => $request->get('percentage_off') ? $request->get('percentage_off') : null,
                'amount_off' => $request->get('amount_off') ? $request->get('amount_off') : null,
                'new_price' => $request->get('new_price') ? $request->get('new_price') : null,
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'start_time' => $request->get('start_time'),
                'end_time' => $request->get('end_time'),
                'mon' => $request->get('mon'),
                'tue' => $request->get('tue'),
                'wed' => $request->get('wed'),
                'thu' => $request->get('thu'),
                'fri' => $request->get('fri'),
                'sat' => $request->get('sat'),
                'sun' => $request->get('sun'),
                'redeem_code' => $request->get('redeem_code'),
                'used_once' => $request->get('used_once'),
                'cover_image1' => $request->get('cover_image1'),
                'cover_image2' => $request->get('cover_image2') ? $request->get('cover_image2') : null,
                'cover_image3' => $request->get('cover_image3') ? $request->get('cover_image3') : null,
                'category_id' => $request->get('category_id'),
                'store_id' => $store->id
            ]);

            return response()->json(['success' => TRUE, 'data' => ["message" => "Thank you for adding a deal. We will approve it asap."]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! You don't have permission to add a deal."]], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Handle an edit deal request.
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postEditDeal(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2|max:255',
                'details' => 'required',
                'overview' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'mon' => 'required|boolean',
                'tue' => 'required|boolean',
                'wed' => 'required|boolean',
                'thu' => 'required|boolean',
                'fri' => 'required|boolean',
                'sat' => 'required|boolean',
                'sun' => 'required|boolean',
                'redeem_code' => 'required|max:8',
                'used_once' => 'required|boolean',
                'cover_image1' => 'required',
                'category_id' => 'required|exists:categories,id,deleted_at,NULL',
                'deal_id' => 'required|exists:deals,id,deleted_at,NULL'
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

        $deal = Deal::find($request->get('deal_id'));

        $store = Store::find($deal->store_id);

        if ($user->is($store->business_id, 'Business Owner')) {
            $deal->update(['name' => $request->get('name'),
                'details' => $request->get('details'),
                'overview' => $request->get('overview'),
                'original_price' => $request->get('original_price') ? $request->get('original_price') : null,
                'percentage_off' => $request->get('percentage_off') ? $request->get('percentage_off') : null,
                'amount_off' => $request->get('amount_off') ? $request->get('amount_off') : null,
                'new_price' => $request->get('new_price') ? $request->get('new_price') : null,
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'start_time' => $request->get('start_time'),
                'end_time' => $request->get('end_time'),
                'mon' => $request->get('mon'),
                'tue' => $request->get('tue'),
                'wed' => $request->get('wed'),
                'thu' => $request->get('thu'),
                'fri' => $request->get('fri'),
                'sat' => $request->get('sat'),
                'sun' => $request->get('sun'),
                'redeem_code' => $request->get('redeem_code'),
                'used_once' => $request->get('used_once'),
                'cover_image1' => $request->get('cover_image1'),
                'cover_image2' => $request->get('cover_image2') ? $request->get('cover_image2') : null,
                'cover_image3' => $request->get('cover_image3') ? $request->get('cover_image3') : null,
                'category_id' => $request->get('category_id')
            ]);

            return response()->json(['success' => TRUE, 'data' => ["message" => "Your deal has been successfully updated."]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! You don't have permission to update this deal."]], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Handle a deal request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function getDeal(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'deal_id' => 'required|exists:deals,id,deleted_at,NULL'
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

        $deal = Deal::find($request->get('deal_id'));

        $store = Store::find($deal->store_id);

        if ($user->is($store->business_id, 'Business Owner')) {
            $stats = $this->sellerRepository->getDealStats($deal->id);

            $deal->stats = $stats;

            $deal = Helper::unsetKeys($deal);

            return response()->json(['success' => TRUE, 'data' => ["deal" => $deal]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! You don't have permission to access this deal."]], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
    }
}
