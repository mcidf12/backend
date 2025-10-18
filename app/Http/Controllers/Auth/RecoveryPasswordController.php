<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RecoverPasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\DB;

class RecoveryPasswordController extends Controller
{
    //
    public function sendEmail(Request $request){
        $email = $request->input('email');

        if(!$this->validateEmail($email)){
        return response()->json([
            "status"=> "error",
            "message"=> "Email no enocntrado"
            ]);
        }

        $this->send($request->email);

        return response()->json([
        "status" => "success",
        "message" => "Correo enviado correctamente",
        "email" => $email
        ],200);

        
    }

    public function send($email){

        $token = $this->createToken($email);
        Mail::to($email)->send(new RecoverPasswordMail($token));
        
    }

    public function createToken($email){
        $oldToken = DB::table('password_resets')->where('email', $email)->first();
        if($oldToken){
            return $oldToken;
        }

        $token = Str::random(60);
        $this->saveToken($token, $email);
        return $token;
    }

    public function saveToken($token, $email){
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    public function validateEmail($email)
    {
        return User::where('email', $email)->exists(); //->exists()
    }

}
 