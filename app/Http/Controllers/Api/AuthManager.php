<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerficationCodes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client as Twilio;

class AuthManager extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'country_code' => 'required|numeric',
            'otp' => 'required|numeric|digits:6',
            'phone' => 'required|numeric|unique:users,phone'
        ]);
        $data = $this->VerifyOTP($request->phone, $request->otp);
        if ($data) {
            $temp = json_decode($data->temp);

            $new_user = User::create([
                'name' => $temp->name,
                'phone' => $request->phone,
                'country_code' => $request->country_code,
            ]);

            $token = $new_user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'OTP Verified  Successfully (Signup)',
                'token' => $token,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Your OTP is invalid'
            ]);
        }
    }
    public function signup_otp(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|numeric|unique:users,phone',
            'country_code' => 'required|numeric'
        ]);
        $temp = ['name' => $request->name, 'country_code' => $request->country_code];
        $this->genarateotp($request->phone, $temp);
        return response()->json([
            'status' => true,
            'message' => 'OTP Send Successfully',
        ]);
    }

    public function login(Request $request)
    {

        $request->validate([
            'country_code' => 'required|numeric',
            'otp' => 'required|numeric|digits:6',
            'phone' => 'required|numeric|exists:users,phone|digits:13',
        ]);
        //  return  $this->VerifyOTP($request->phone, $request->otp);
        if ($this->VerifyOTP($request->phone, $request->otp)) {
            $checkphone = User::where('phone', $request->phone)->first();
            if ($checkphone) {
                $checkphone->tokens()->delete();
                $token = $checkphone->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'OTP Verified  Successfully (Login)',
                    'token' => $token,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Mobile Has Not Registered',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Your OTP is invalid'
            ]);
        }
    }
    private function VerifyOTP($phone, $otp)
    {
        //this for test otp
        if ($otp == "913432") {
            $checkotp = VerficationCodes::where('phone', $phone)
                ->latest()->first();
            VerficationCodes::where('phone', $phone)->delete();
            return $checkotp;
        }
        //end for test otp
        $checkotp = VerficationCodes::where('phone', $phone)
            ->where('otp', $otp)->latest()->first();
        $now = Carbon::now();
        if (!$checkotp) {
            return 0;
        } elseif ($checkotp && $now->isAfter($checkotp->expire_at)) {

            return 0;
        } else {
            $device = 'Auth_Token';
            VerficationCodes::where('phone', $phone)->delete();
            return $checkotp;
        }
    }
    public function SendOTP(Request $request)
    {
        $request->validate([
            'country_code' => 'required|numeric',
            'phone' => 'required|numeric|exists:users,phone|digits:13',
        ], [
            'phone.exists' => 'Phone Number Has Not Registered',
        ]);
        $temp = ['country_code' => $request->country_code];
        if ($this->genarateotp($request->phone, $temp)) {
            return response()->json([
                'status' => true,
                'message' => 'OTP send successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'OTP Send UnsuccessFully Or Limit Exeeded Try Again Later',
            ]);
        }
    }
    public function resend(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|digits:13',
        ]);
        $phone = $request->phone;

        if ($this->genarateotp($phone)) {
            return response()->json([
                'status' => true,
                'message' => 'Sms Sent Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Sms Could Not Be Sent',
            ]);
        }
    }
    private function genarateotp($number, $temp = [])
    {
        $otpmodel = VerficationCodes::where('phone', $number);

        if ($otpmodel->count() > 10) {
            return false;
        }
        $checkotp = $otpmodel->latest()->first();
        $now = Carbon::now();

        if ($checkotp && $now->isBefore($checkotp->expire_at)) {

            $otp = $checkotp->otp;
            $checkotp->update([
                'temp' => json_encode($temp),
            ]);
        } else {
            $otp = rand('100000', '999999');
            //$otp = 123456;
            VerficationCodes::create([
                'temp' => json_encode($temp),
                'phone' => $number,
                'otp' => $otp,
                'expire_at' => Carbon::now()->addMinute(10)
            ]);
        };
        $receiverNumber = '+' . $temp['country_code'] . $number;
        $message = "Hello\nTrapp Verification OTP is " . $otp;

        try {

            $account_sid = env("TWILIO_SID");
            $auth_token = env("TWILIO_TOKEN");
            $twilio_number = env("TWILIO_FROM");

            $client = new Twilio($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number,
                'body' => $message
            ]);

            return true;
        } catch (Exception $e) {
            dd("Error: " . $e->getMessage());
        }
    }
}
