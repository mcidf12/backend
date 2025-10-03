<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{


    public static $rules = [
            'name'      => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email'     => 'required|string|max:255|unique:users,email',
            'password'  => 'required|string|min:8',
        ];

    public function index()
    {
        //
        return User::all();
    }

    public function store(Request $request)
    {
        //
        $data=$request->validate(self::$rules);
        $data['password'] = Hash::make($data['password']);

        $user = DB::transaction(fn() => User::create($data));

         return response()->json([
            'mensaje' => 'Registro Creado',
            'data'    => $user,
        ], 201);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);
        $validated = $request->validate(array_merge(self::$rules, [
            'email' => ['sometimes','required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
        ]));

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        DB::transaction(fn() => $user->update($validated));
    
        return response()->json([
            'mensaje' => 'Registro Actualizado',
            'data'    => $user->fresh(), 
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
