<?php

namespace App\Http\Controllers\Seller;

use Mail;
use App\Business;
use App\Http\Requests;
use App\BusinessInquiry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\SellerRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as IlluminateResponse;

class BusinessController extends Controller
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
     * Handle a business signup inquiry request.
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postSignupInquiry(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2|max:255',
                'address' => 'required',
                'city' => 'required',
                'zip_code' => 'required|numeric',
                'contact_number' => 'required'
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

        $businessInquiry = new BusinessInquiry;
        $businessInquiry->name = $request->get('name');
        $businessInquiry->address = $request->get('address');
        $businessInquiry->city = $request->get('city');
        $businessInquiry->zip_code = $request->get('zip_code');
        $businessInquiry->contact_number = $request->get('contact_number');

        $user->businessInquiries()->save($businessInquiry);

        Mail::send('emails.inquiry', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('The Foookat Team');
        });

        return response()->json(['success' => TRUE, 'data' => ["message" => "Your inquiry has been successfully submitted. Our sales team will contact you shortly."]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a business request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function getBusiness(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
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
            return response()->json(['success' => TRUE, 'data' => ["business" => $business]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! You don't have permission to access this business."]], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Handle stores request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function getStores(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
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
            foreach ($business->stores as $store) {
                $store->averageRating;

                $stats = $this->sellerRepository->getStoreStats($store->id);

                $store->stats = $stats;
            }

            return response()->json(['success' => TRUE, 'data' => ["stores" => $business->stores]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! You don't have permission to access the stores of this business."]], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
    }
}
