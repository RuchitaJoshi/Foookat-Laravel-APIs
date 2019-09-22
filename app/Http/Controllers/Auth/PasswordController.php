<?php

namespace App\Http\Controllers\Auth;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ['email' => 'required|email|max:255']
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

        $broker = $this->getBroker();

        $response = Password::broker($broker)->sendResetLink(
            $this->getSendResetLinkEmailCredentials($request),
            $this->resetEmailBuilder()
        );

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return response()->json(['data' => array('success' => TRUE, "message"=>"Password reset details have been e-mailed to you.")], IlluminateResponse::HTTP_OK);

            case Password::INVALID_USER:
                return response()->json(['data' => array('success' => FALSE, "message"=>"Try entering your e-mail again. We don't recognize this one.")], IlluminateResponse::HTTP_BAD_REQUEST);

            default:
                return response()->json(['data' => array('success' => FALSE, "message"=>"Sorry! We are unable to process this request. Please try again later.")], IlluminateResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Reset the given user's password.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->getResetValidationRules());

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $broker = $this->getBroker();

        $response = Password::broker($broker)->reset($credentials, function ($user, $password) {
            $user->password = $password;

            $user->save();

            Auth::guard($this->getGuard())->login($user);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return view('auth.success', ['message' => 'Your password has been successfully reset.']);

            default:
                return view('auth.success', ['message' => 'Sorry! We are unable to process this request. Please try again later.']);
        }
    }
}
