<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use GuzzleHttp\Psr7\Response;

class RecoveryPasswordController extends Controller
{
    //
    public function sendEmail(Request $request){

        if(!$this->validateEmail($request->email)){
        return response()->json([
            "status"=> "error",
            "message"=> "Email no enocntrado"
            ]);
        }

        $this->send($request->email);
    }

    public function send($email){
        //Mail::to($email)->(new);
    }

    public function validateEmail($email)
    {
        return !!User::where('email', $email)->firstOrFail();
    }

}
 