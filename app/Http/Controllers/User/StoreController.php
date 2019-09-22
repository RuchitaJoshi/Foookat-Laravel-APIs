<?php

namespace App\Http\Controllers\User;

use App\Store;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\StoreRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as IlluminateResponse;

class StoreController extends Controller
{
    /**
     * @var StoreRepository
     */
    private $storeRepository;

    /**
     * Create a new controller instance.
     *
     * @param StoreRepository $storeRepository
     */
    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
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

        $store = $this->storeRepository->getStore($user, $request->get('store_id'));

        return response()->json(['success' => TRUE, 'data' => ["store" => $store ? $store : "Sorry! Unable to find this store. Please try again."]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a request to fetch store's reviews
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function getReviews(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'store_id' => 'required|exists:stores,id,deleted_at,NULL',
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

        $store = Store::find($request->get('store_id'));

        $current_page = $request->get('offset') / Config::get('constants.offset') + 1;
        $per_page = Config::get('constants.offset');
        $total_count = $store->reviewedBy()->count();
        $total_pages = ceil($total_count / Config::get('constants.offset'));
        $metadata = array(
            'current_page' => $current_page,
            'per_page' => $per_page,
            'total_count' => $total_count,
            'total_pages' => $total_pages
        );

        $reviews = array();

        foreach ($store->reviewedBy()->orderBy('pivot_created_at', 'desc')->skip($request->get('offset'))->take(Config::get('constants.offset'))->get() as $review) {
            $user = array('name' => $review->name, 'profile_picture' => $review->profile_picture ? $review->profile_picture : "");
            array_push($reviews, array('rating' => $review->pivot->rating, 'review' => $review->pivot->review ? $review->pivot->review : "", 'created_at' => Carbon::parse($review->pivot->created_at)->format('Y-m-d H:i:s'), 'user' => $user));
        }

        return response()->json(['success' => TRUE, 'data' => ["_metadata" => $metadata, "reviews" => $reviews]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a post review request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReview(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'store_id' => 'required|exists:stores,id,deleted_at,NULL',
                'rating' => 'required'
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

        $user->reviews()->attach($store->id, ['rating' => $request->get('rating'), 'review' => $request->get('review') ? $request->get('review') : NULL]);

        return response()->json(['success' => TRUE, 'data' => ["message" => "Your review has been successfully submitted."]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a request to fetch store's news
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function getNews(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'store_id' => 'required|exists:stores,id,deleted_at,NULL',
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

        $store = Store::find($request->get('store_id'));

        $current_page = $request->get('offset') / Config::get('constants.offset') + 1;
        $per_page = Config::get('constants.offset');
        $total_count = $store->news()->count();
        $total_pages = ceil($total_count / Config::get('constants.offset'));
        $metadata = array(
            'current_page' => $current_page,
            'per_page' => $per_page,
            'total_count' => $total_count,
            'total_pages' => $total_pages
        );

        $news = array();

        foreach ($store->news()->orderBy('created_at', 'desc')->skip($request->get('offset'))->take(Config::get('constants.offset'))->get() as $record) {
            array_push($news, array('title' => $record->title, 'news' => $record->news, 'image' => $record->image ? $record->image : "", 'created_at' => Carbon::parse($record->created_at)->format('Y-m-d H:i:s')));
        }

        return response()->json(['success' => TRUE, 'data' => ["_metadata" => $metadata, "news" => $news]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a request to mark a store as a favourite
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postFavouriteStore(Request $request)
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

        if (!$user->favouriteStores()->where(array('store_id' => $store->id))->exists()) {
            $user->favouriteStores()->attach($request->get('store_id'));
        }

        return response()->json(['success' => TRUE, 'data' => ["message" => "Store has been successfully added to your favourites."]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a request to remove a store as a favourite
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRemoveFavouriteStore(Request $request)
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

        if ($user->favouriteStores()->where(array('store_id' => $store->id))->exists()) {
            $user->favouriteStores()->detach($request->get('store_id'));
        }

        return response()->json(['success' => TRUE, 'data' => ["message" => "Store has been successfully removed from your favourites."]], IlluminateResponse::HTTP_OK);
    }
}
