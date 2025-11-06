<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Service\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
//use Illuminate\Support\Facades\Log;

class UserController extends Controller
{


    public static $rules = [
        'cliente'   => 'required|string|max:5|unique:users,cliente',
        'name'      => 'nullable|sometimes|string|max:255',
        'last_name' => 'nullable|sometimes|string|max:255',
        'phone'     => 'nullable|sometimes|string|max:10|regex:/^\d+$/',
        'email'     => 'required|string|max:255|unique:users,email',
        'password'  => 'required|string|min:8',
    ];

    public static $rulesUpdate = [
        'name'      => 'sometimes|string|max:255',
        'last_name' => 'sometimes|string|max:255',
        'phone'     => 'sometimes|string|max:10|regex:/^\d+$/',
        'email'     => 'sometimes|required|email|max:255',
        'password'  => 'sometimes|string|min:8',

    ];

    public function index()
    {
        //
        return User::all();
    }

    public function store(Request $request)
    {
        // $this->getClienteData
        $cliente = $request->cliente;

        //crear peticion con cabecera
        $peticion = Http::withHeaders([
            'Accept' => 'application/json',
            'x-web-key' => 'web_9825f8agd35dfd4bg15fsd3a94c947a28896d5fd58gjh0f251a38912a'
        ])
            ->withoutVerifying()
            ->get('https://dev.emenet.mx/api/clientesV2/' . $cliente);

        //si ya existe
        $existeUsuario = User::where('cliente', $cliente)->exists();
        if ($existeUsuario) {
            return response()->json([
                'message' => 'Este cliente ya tiene una cuenta registrada.'
            ], 409);
        }
        

        //en caso de fallar la peticion
        if ($peticion->failed()) {
            if ($peticion->status() === 404) {
                return response()->json([
                    'message' => 'El número de cliente no existe.',
                    'detalles' => $peticion->body()
                ], 404);
            }
        }

        $clienteData = $peticion->json();

        $data = $request->validate(self::$rules);
        $data['password'] = Hash::make($data['password']);


        $user = DB::transaction(fn() => User::create($data));

        return response()->json([
            'mensaje' => 'Registro Creado',
            'data'    => $user,
            'cliente' => $clienteData,
        ], 201);
    }



    public function getClienteData($cliente)
    {
        //
        //$user = User::findOrFail($id);

        $servicios = [
            'estadoCuenta' => [
                ['VENTA' => '240042', 'fechaEmision' => '06-08-2025', 'importe' => '500.0', 'mensualidad' => 'AGO 2025'],
                ['VENTA' => '246117', 'fechaEmision' => '05-09-2025', 'importe' => '600.0', 'mensualidad' => 'SEP 2025'],
                ['VENTA' => '243517', 'fechaEmision' => '05-10-2025', 'importe' => '600.0', 'mensualidad' => 'OCT 2025'],
                ['VENTA' => '248513', 'fechaEmision' => '05-11-2025', 'importe' => '600.0', 'mensualidad' => 'NOV 2025'],
            ],
            'internet' => ['precio' => 400],
            'camaras'  => ['canServicios' => 1, 'precio' => 50],
            'telefonia'  => ['lineas' => 1, 'precio' => 150],

        ];

        $fecha = Carbon::now();
        $resultadoDeuda = UserService::calcularAdeudo($servicios, $fecha);

        Log::info(' recibidos:', $resultadoDeuda);


        return [
            'status' => 'success',
            'cliente' => [
                'cliente'         => '01804',
                //'nombre'          => strtoupper($user->name).' '.strtoupper($user->last_name ?? ''),
                'nombre'          => 'Marcos Cid Flores',
                'direccion'       => 'JOSE VALENTIN DAVILA #401',
                'pais'            => 'Mexico',
                'estado'          => 'MEXICO',
                'municipio'       => 'JOCOTITLAN',
                'colonia'         => 'JOCOTITLAN',
                'correo'          => 'mcid653@gmail',
                'telefono'        => '7121748293',
                'coordenadas'     => '19.569008, -99.756175',
                'planInternet'    => 'PLAN200',
                'nombrePlan'      => 'Plan 200 Megas Fibra',
                'clasificacion'   => 'IFO',
                'desClasificacion' => 'FIBRA ÓPTICA',
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
        ];
    }

    public function show($cliente)
    {
        $data = $this->getClienteData($cliente);

        if (!$data) return response()->json(['message' => 'Cliente no encontrado'], 404);

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);
        //$user = $request->user();

        $validated = $request->validate(array_merge(self::$rulesUpdate, [
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
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
