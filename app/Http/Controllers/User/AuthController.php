<?php

namespace App\Http\Controllers\User;

use Mail;
use JWTAuth;
use App\User;
use App\Http\Requests;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as IlluminateResponse;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|max:255',
                'password' => 'required'
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

        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => FALSE,
                    'error' => [
                        'code' => 11,
                        'messages' => ['Invalid credentials']
                    ]
                ], IlluminateResponse::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'success' => FALSE,
                'error' => [
                    'code' => 12,
                    'messages' => ['Could not create token']
                ]
            ], IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = JWTAuth::toUser($token);
        $user->token = $token;

        if (!$user->active) {
            return response()->json([
                'success' => FALSE,
                'error' => [
                    'code' => 13,
                    'messages' => ['Your account is not verified. Please verify your e-mail address to activate your account.']
                ]
            ], IlluminateResponse::HTTP_FORBIDDEN);
        }

        if ($request->input('device_type') && $request->input('device_token')) {
            //create an end point on AWS SNS
        }

        $user = Helper::unsetKeys($user);

        // all good so return the user with token
        return response()->json(['success' => TRUE, 'data' => ['user' => $user]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a sign up request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return IlluminateResponse
     */
    public function postSignup(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required',
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

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'date_of_birth' => $request->get('date_of_birth'),
            'gender' => $request->get('gender'),
            'active' => FALSE,
            'email_verification_code' => Helper::generateRandomString(30)
        ]);

        Mail::send('auth.emails.confirm', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Account Activation');
        });

        return response()->json(['success' => TRUE, 'data' => ["message" => "Your account has been successfully created. Verification e-mail has been sent to verify your e-mail address."]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a check facebook id request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckFacebookId(Request $request)
    {
        $validator = Validator::make($request->all(), ['facebook_id' => 'required']);

        if ($validator->fails()) {
            return response()->json(['success' => FALSE, 'error' => ['code' => 10, 'messages' => $validator->errors()->all()]], IlluminateResponse::HTTP_BAD_REQUEST);
        }

        $user = User::where('facebook_id', '=', $request->get('facebook_id'))->first();

        if (!$user) {
            return response()->json(['success' => TRUE, 'data' => ['user' => array('id' => '', 'name' => '', 'email' => '', 'mobile_number' => '', 'profile_picture' => '', 'date_of_birth' => '', 'gender' => '', 'facebook_id' => $request->get('facebook_id'), 'google_id' => '', 'token' => '')]], IlluminateResponse::HTTP_OK);
        }

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'success' => FALSE,
                    'error' => [
                        'code' => 11,
                        'messages' => ['Invalid credentials']
                    ]
                ], IlluminateResponse::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'success' => FALSE,
                'error' => [
                    'code' => 12,
                    'messages' => ['Could not create token']
                ]
            ], IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = JWTAuth::toUser($token);
        $user->token = $token;

        if ($request->input('device_type') && $request->input('device_token')) {
            //create an end point on AWS SNS
        }

        $user = Helper::unsetKeys($user);

        // all good so return the user with token
        return response()->json(['success' => TRUE, 'data' => ['user' => $user]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a login with facebook request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postFacebook(Request $request)
    {
        $validator = Validator::make($request->all(), ['facebook_id' => 'required']);

        if ($validator->fails()) {
            return response()->json(['success' => FALSE, 'error' => ['code' => 10, 'messages' => $validator->errors()->all()]], IlluminateResponse::HTTP_BAD_REQUEST);
        }

        $user = User::where('facebook_id', '=', $request->get('facebook_id'))->first();

        if (!$user) {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|min:2|max:255',
                    'email' => 'required|email|max:255|unique:users',
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

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'profile_picture' => $request->get('profile_picture') ? $request->get('profile_picture') : null,
                'date_of_birth' => $request->get('date_of_birth'),
                'gender' => $request->get('gender'),
                'facebook_id' => $request->get('facebook_id'),
                'active' => TRUE
            ]);
        }

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'success' => FALSE,
                    'error' => [
                        'code' => 11,
                        'messages' => ['Invalid credentials']
                    ]
                ], IlluminateResponse::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'success' => FALSE,
                'error' => [
                    'code' => 12,
                    'messages' => ['Could not create token']
                ]
            ], IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = JWTAuth::toUser($token);
        $user->token = $token;

        if ($request->input('device_type') && $request->input('device_token')) {
            //create an end point on AWS SNS
        }

        $user = Helper::unsetKeys($user);

        // all good so return the user with token
        return response()->json(['success' => TRUE, 'data' => ['user' => $user]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a check google id request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckGoogleId(Request $request)
    {
        $validator = Validator::make($request->all(), ['google_id' => 'required']);

        if ($validator->fails()) {
            return response()->json(['success' => FALSE, 'error' => ['code' => 10, 'messages' => $validator->errors()->all()]], IlluminateResponse::HTTP_BAD_REQUEST);
        }

        $user = User::where('google_id', '=', $request->get('google_id'))->first();

        if (!$user) {
            return response()->json(['success' => TRUE, 'data' => ['user' => array('id' => '', 'name' => '', 'email' => '', 'mobile_number' => '', 'profile_picture' => '', 'date_of_birth' => '', 'gender' => '', 'facebook_id' => '', 'google_id' => $request->get('google_id'), 'token' => '')]], IlluminateResponse::HTTP_OK);
        }

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'success' => FALSE,
                    'error' => [
                        'code' => 11,
                        'messages' => ['Invalid credentials']
                    ]
                ], IlluminateResponse::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'success' => FALSE,
                'error' => [
                    'code' => 12,
                    'messages' => ['Could not create token']
                ]
            ], IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = JWTAuth::toUser($token);
        $user->token = $token;

        if ($request->input('device_type') && $request->input('device_token')) {
            //create an end point on AWS SNS
        }

        $user = Helper::unsetKeys($user);

        // all good so return the user with token
        return response()->json(['success' => TRUE, 'data' => ['user' => $user]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a login with google request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postGoogle(Request $request)
    {
        $validator = Validator::make($request->all(), ['google_id' => 'required']);

        if ($validator->fails()) {
            return response()->json(['success' => FALSE, 'error' => ['code' => 10, 'messages' => $validator->errors()->all()]], IlluminateResponse::HTTP_BAD_REQUEST);
        }

        $user = User::where('google_id', '=', $request->get('google_id'))->first();

        if (!$user) {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|min:2|max:255',
                    'email' => 'required|email|max:255|unique:users',
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

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'profile_picture' => $request->get('profile_picture') ? $request->get('profile_picture') : null,
                'date_of_birth' => $request->get('date_of_birth'),
                'gender' => $request->get('gender'),
                'google_id' => $request->get('google_id'),
                'active' => TRUE
            ]);
        }

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'success' => FALSE,
                    'error' => [
                        'code' => 11,
                        'messages' => ['Invalid credentials']
                    ]
                ], IlluminateResponse::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'success' => FALSE,
                'error' => [
                    'code' => 12,
                    'messages' => ['Could not create token']
                ]
            ], IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = JWTAuth::toUser($token);
        $user->token = $token;

        if ($request->input('device_type') && $request->input('device_token')) {
            //create an end point on AWS SNS
        }

        $user = Helper::unsetKeys($user);

        // all good so return the user with token
        return response()->json(['success' => TRUE, 'data' => ['user' => $user]], IlluminateResponse::HTTP_OK);
    }

    /**
     * Handle a token refresh
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRefreshToken(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'token' => 'required',
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
                ], IlluminateResponse::HTTP_NOT_ACCEPTABLE
            );
        }

        try {
            $refreshed_token = JWTAuth::refresh($request->input('token'));
        } catch (Exceptions\TokenBlacklistedException $e) {
            return response()->json(
                [
                    'success' => FALSE,
                    'error' => [
                        'code' => 15,
                        'messages' => ['Blacklisted token']
                    ]
                ], $e->getStatusCode()
            );
        } catch (Exceptions\TokenInvalidException $e) {
            return response()->json(
                [
                    'success' => FALSE,
                    'error' => [
                        'code' => 16,
                        'messages' => ['Invalid token']
                    ]
                ], $e->getStatusCode()
            );
        } catch (Exceptions\TokenExpiredException $e) {
            return response()->json(
                [
                    'success' => FALSE,
                    'error' => [
                        'code' => 17,
                        'messages' => ['Expired token']
                    ]
                ], $e->getStatusCode()
            );
        }

        return response()->json(['success' => TRUE, 'data' => ['token' => $refreshed_token]], IlluminateResponse::HTTP_OK);
    }
}
