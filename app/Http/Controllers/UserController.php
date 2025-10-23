<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Service\UserService;
use Carbon\Carbon;
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

    public static $rulesUpdate = [
        'name'      => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email'     => 'required|string|max:255|unique:users,email',
        'password'  => 'string|min:8',

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
         $user = User::findOrFail($id);

         $servicios = [
                'estadoCuenta' => [
                    ['VENTA' => '240042','fechaEmision' => '06-08-2025','importe' => '500.0','mensualidad' => 'AGO 2025'],
                    ['VENTA' => '246117','fechaEmision' => '05-09-2025','importe' => '600.0','mensualidad' => 'SEP 2025'],
                    ['VENTA' => '243417','fechaEmision' => '05-10-2025','importe' => '100.0','mensualidad' => 'OCT 2025'],
                ],
                'internet' => ['precio' => 400],
                'camaras'  => ['canServicios' => 1, 'precio' => 50],
                'telefonia'  => ['lineas' => 1, 'precio' => 150],

            ];

        $fecha = Carbon::now();
        $resultadoDeuda = UserService::calcularAdeudo($servicios, $fecha);
        
        return response()->json([
            'status' => 'success',
            'cliente' => [
                'cliente'         => '01804',
                'nombre'          => strtoupper($user->name).' '.strtoupper($user->last_name ?? ''),
                'direccion'       => 'JOSE VALENTIN DAVILA #401',
                'pais'            => 'Mexico',
                'estado'          => 'MEXICO',
                'municipio'       => 'JOCOTITLAN',
                'colonia'         => 'JOCOTITLAN',
                'correo'          => $user->email,
                'telefono'        => '7121748293',
                'coordenadas'     => '19.569008, -99.756175',
                'planInternet'    => 'PLAN200',
                'nombrePlan'      => 'Plan 200 Megas Fibra',
                'clasificacion'   => 'IFO',
                'desClasificacion'=> 'FIBRA ÓPTICA',
                'router'          => '1',
                'infoRed' => [
                    'router'      => 'IXTLAHUACA',
                    'address'     => '172.16.31.123',
                    'estado'      => 'Activo',
                    'estadoFibra' => 'bound'
                ],
                'deuda' => $resultadoDeuda,
            ],
            'servicios' => $servicios,
        ]);
    }
    

    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);
        //$user = $request->user();

        $validated = $request->validate(array_merge(self::$rulesUpdate, [
            'email' => ['sometimes','required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
        ]));

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
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
