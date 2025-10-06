<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message"=> "Cierre de sesion exitoso"
        ]);

}


}
