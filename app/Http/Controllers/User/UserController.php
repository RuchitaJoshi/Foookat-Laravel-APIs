<?php

namespace App\Http\Controllers\User;

use JWTAuth;
use App\Http\Requests;
use App\Helpers\Helper;
use App\Helpers\Clickatell;
use Illuminate\Http\Request;
use App\UserVerificationCode;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as IlluminateResponse;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param UserRepository $dealRepository
     */
    public function __construct(UserRepository $dealRepository)
    {
        $this->userRepository = $dealRepository;
    }

    /**
     * Handle user preferences request.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPreferences()
    {
        $user = Auth::User();

        $user = Helper::unsetKeys($user);

        return response()->json(['success' => TRUE, 'data' => ['profile' => $user]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a user profile request.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        $user = Auth::User();

        $user = Helper::unsetKeys($user);

        return response()->json(['success' => TRUE, 'data' => ['user' => $user]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a request to update a profile.
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postProfile(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2|max:255',
                'date_of_birth' => 'required|date|date_format:Y-m-d',
                'gender' => 'required'
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

        $user->update(['name' => $request->get('name'),
            'date_of_birth' => $request->get('date_of_birth'),
            'gender' => $request->get('gender'),
            'profile_picture' => $request->get('profile_picture') ? $request->get('profile_picture') : null
        ]);

        return response()->json(['success' => TRUE, 'data' => ['message' => 'Your profile has been successfully updated.']], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a request to send verification code.
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postSendVerificationCode(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mobile_number' => 'required'
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

        $code = Helper::generateVerificationCode();

        if ($user->verificationCode) {
            $verificationCode = $user->verificationCode;
            $verificationCode->mobile_number = $request->get('mobile_number');
            $verificationCode->verification_code = $code;
            $user->verificationCode()->save($verificationCode);
        } else {
            $verificationCode = new UserVerificationCode;
            $verificationCode->mobile_number = $request->get('mobile_number');
            $verificationCode->verification_code = $code;
            $user->verificationCode()->save($verificationCode);
        }

        $message = "Your Foookat verification code is " . Helper::generateVerificationCode();

        $clickatell = new Clickatell;

        $clickatell->to($request->get('mobile_number'))
            ->message($message)
            ->send();

        return response()->json(['success' => TRUE, 'data' => ['message' => 'Verification code has been successfully sent.']], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a request to verify code.
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postVerifyCode(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'verification_code' => 'required|numeric'
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

        if ($user->verificationCode) {
            $verificationCode = $user->verificationCode;

            if ($verificationCode->verification_code == $request->get('verification_code')) {
                $user->update(['mobile_number' => $verificationCode->mobile_number]);

                $user->verificationCode()->delete();

                return response()->json(['success' => TRUE, 'data' => ['message' => 'Your mobile number has been successfully verified.']], IlluminateResponse::HTTP_OK);
            } else {
                return response()->json(['success' => FALSE, 'data' => ['message' => 'Invalid verification code. Please try again.']], IlluminateResponse::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json(['success' => FALSE, 'data' => ['message' => 'Sorry! Unable to find verification code. Please try verifying your mobile number again.']], IlluminateResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Handle user favourite deals request
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function getFavouriteDeals(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
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

        $favouriteDeals = $this->userRepository->getFavouriteDeals($user, $request->get('offset'));

        return response()->json(['success' => TRUE, 'data' => ["_metadata" => $favouriteDeals['metadata'], "deals" => $favouriteDeals['deals']]], IlluminateResponse::HTTP_OK);
    }


    /**
     * Handle user favourite stores request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getFavouriteStores(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
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

        $favouriteStores = $this->userRepository->getFavouriteStores($user, $request->get('offset'));

        return response()->json(['success' => TRUE, 'data' => ["_metadata" => $favouriteStores['metadata'], "stores" => $favouriteStores['stores']]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle user redeems request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getRedeems(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
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

        $redeems = $this->userRepository->getRedeems($user, $request->get('offset'));

        return response()->json(['success' => TRUE, 'data' => ["_metadata" => $redeems['metadata'], "deals" => $redeems['deals']]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle change password request
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postChangePassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'old_password' => 'required',
                'new_password' => 'required'
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

        if(Hash::check($request->get('old_password'),$user->password)) {
            if(Hash::check($request->get('new_password'),$user->password)){
                return response()->json(['success' => FALSE, 'data' => ['message' => 'Your new password is same as an old password. Please try again with a different password.']], IlluminateResponse::HTTP_BAD_REQUEST);
            }
            $user->update(['password' => $request->get('new_password')]);
            return response()->json(['success' => TRUE, 'data' => ["message" => "Your password has been successfully changed."]], IlluminateResponse::HTTP_OK);
        } else {
            return response()->json(['success' => FALSE, 'data' => ['message' => 'Passwords do not match. Please try again with the correct old password.']], IlluminateResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Handle logout request
     *
     * @param Request $request
     * @return IlluminateResponse
     */
    public function postLogout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        if ($request->input('device_type') && $request->input('device_token')) {
            //remove an end point on AWS SNS
        }
        
        return response()->json(['success' => TRUE, 'data' => ["message" => "You have been successfully logged out."]], IlluminateResponse::HTTP_OK);
    }
}

