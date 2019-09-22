<?php

namespace App\Http\Controllers\User;

use App\Deal;
use App\Http\Requests;
use App\UserGeolocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DealRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as IlluminateResponse;

class DealController extends Controller
{
    /**
     * @var DealRepository
     */
    private $dealRepository;

    /**
     * Create a new controller instance.
     *
     * @param DealRepository $dealRepository
     */
    public function __construct(DealRepository $dealRepository)
    {
        $this->dealRepository = $dealRepository;
    }

    /**
     * Handle running live deals request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postRunningLiveDeals(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'latitude' => 'required',
                'longitude' => 'required',
                'offset' => 'required'
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

        if($request->get('offset') == 0) {
            $geolocation = new UserGeolocation;
            $geolocation->latitude = $request->get('latitude');
            $geolocation->longitude = $request->get('longitude');;
            $user->geolocations()->save($geolocation);
        }

        $runningLiveDeals = $this->dealRepository->getRunningLiveDeals($user, $request->get('latitude'), $request->get('longitude'), $request->get('offset'), $request->get('filters'));

        return response()->json(['success' => TRUE, 'data' => ["_metadata" => $runningLiveDeals['metadata'], "deals" => $runningLiveDeals['deals']]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle starting soon deals request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postStartingSoonDeals(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'latitude' => 'required',
                'longitude' => 'required',
                'offset' => 'required'
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

        $startingSoonDeals = $this->dealRepository->getStartingSoonDeals($user, $request->get('latitude'), $request->get('longitude'), $request->get('offset'), $request->get('filters'));

        return response()->json(['success' => TRUE, 'data' => ["_metadata" => $startingSoonDeals['metadata'], "deals" => $startingSoonDeals['deals']]], IlluminateResponse::HTTP_OK);
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
                'deal_id' => 'required|exists:deals,id'
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

        $deal = $this->dealRepository->getDeal($user, $request->get('deal_id'));

        return response()->json(['success' => TRUE, 'data' => ["deal" => $deal ? $deal : "Sorry! Unable to find this deal. Please try again."]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a claim deal request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postClaimDeal(Request $request)
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

        if ($deal->used_once) {
            if ($user->redeems()->where(array('deal_id' => $deal->id, 'status' => 'Claimed'))->exists()) {
                return response()->json(['success' => TRUE, 'data' => ["message" => "You have already claimed this foookat."]], IlluminateResponse::HTTP_OK);
            } else if ($user->redeems()->where(array('deal_id' => $deal->id, 'status' => 'Redeemed'))->exists()) {
                return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! This foookat can be used once per user and you have already redeemed it."]], IlluminateResponse::HTTP_BAD_REQUEST);
            } else {
                $user->redeems()->attach($deal->id, ['status' => 'Claimed']);

                return response()->json(['success' => TRUE, 'data' => ["message" => "Foookat has been successfully claimed."]], IlluminateResponse::HTTP_OK);
            }
        } else {
            if ($user->redeems()->where(array('deal_id' => $deal->id, 'status' => 'Claimed'))->exists()) {
                return response()->json(['success' => TRUE, 'data' => ["message" => "You have already claimed this foookat."]], IlluminateResponse::HTTP_OK);
            } else {
                $user->redeems()->attach($deal->id, ['status' => 'Claimed']);

                return response()->json(['success' => TRUE, 'data' => ["message" => "Foookat has been successfully claimed."]], IlluminateResponse::HTTP_OK);
            }
        }
    }

    /**
     * Handle a redeem deal request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRedeemDeal(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'deal_id' => 'required|exists:deals,id,deleted_at,NULL',
                'redeem_code' => 'required'
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

        if ($user->redeems()->where(array('deal_id' => $deal->id, 'status' => 'Claimed'))->exists()) {
            if ($request->get('redeem_code') == $deal->redeem_code) {
                $user->redeems()->wherePivot('status', '=', 'Claimed')->updateExistingPivot($deal->id, ['status' => 'Redeemed']);
                return response()->json(['success' => TRUE, 'data' => ["message" => "Foookat has been successfully redeemed."]], IlluminateResponse::HTTP_OK);
            } else {
                return response()->json(['success' => FALSE, 'data' => ["message" => "Invalid redeem code. Please try again."]], IlluminateResponse::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json(['success' => FALSE, 'data' => ["message" => "Sorry! Unable to find this foookat. Please try again."]], IlluminateResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Handle a request to mark a deal as a favourite
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postFavouriteDeal(Request $request)
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

        if (!$user->favouriteDeals()->where(array('deal_id' => $deal->id))->exists()) {
            $user->favouriteDeals()->attach($deal->id);
        }

        return response()->json(['success' => TRUE, 'data' => ["message" => "Foookat has been successfully added to your favourites."]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a request to remove a deal as a favourite
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRemoveFavouriteDeal(Request $request)
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

        if ($user->favouriteDeals()->where(array('deal_id' => $deal->id))->exists()) {
            $user->favouriteDeals()->detach($deal->id);
        }

        return response()->json(['success' => TRUE, 'data' => ["message" => "Foookat has been successfully removed from your favourites."]], IlluminateResponse::HTTP_OK);
    }

    public function postReportDealError(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'deal_id' => 'required|exists:deals,id,deleted_at,NULL',
                'errors' => 'required'
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

        $user->errorReports()->attach($deal->id, ['errors' => implode(",", $request->get('errors')), 'additional_information' => $request->get('additional_information') ? $request->get('additional_information') : NULL]);

        return response()->json(['success' => TRUE, 'data' => ["message" => "Thanks! We'll get to this as soon as possible."]], IlluminateResponse::HTTP_OK);
    }
}
