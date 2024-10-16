<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Estudiante;

class EstudianteController extends Controller
{
    public function index()
    {
        $Estudiantes = Estudiante::all();

        if ($Estudiantes->isEmpty()) {
            $data = [
                'message' => "No se encontraron datos",
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'Estudiantes' => $Estudiantes,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        // Obtener el año actual en formato YY (últimos dos dígitos)
        $year = date('y');

        // Obtener el siguiente ID disponible en la tabla 'estudiante'
        $nextId = Estudiante::max('id') + 1;

        // Generar el 'codigo_estudiante' combinando el año y el siguiente ID, rellenando con ceros a la izquierda
        $codigo_estudiante = $year . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // Validación de datos (sin el campo codigo_estudiante como requerido)
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:45',
            'apellido_paterno' => 'required|string|max:45',
            'apellido_materno' => 'required|string|max:45',
            'dni' => 'required|string|max:8|unique:estudiante,dni',
            'sexo' => 'required|string|max:1',
            'celular' => 'required|string|max:9',
            'correo' => 'required|string|email|max:60|unique:estudiante,correo',
            'fecha_nacimiento' => 'required|date'
        ],[
            'dni.unique' => 'El DNI ya está registrado.',
            'correo.unique' => 'El correo electrónico ya está registrado.',
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser un texto.',
            'max' => 'El campo :attribute no debe tener más de :max caracteres.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'date' => 'El campo :attribute debe ser una fecha válida.'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        // Crear el nuevo estudiante con el 'codigo_estudiante' generado
        $Estudiante = Estudiante::create([
            "codigo_estudiante" => $codigo_estudiante, // Aquí se usa el código generado
            "nombre" => $request->nombre,
            "apellido_paterno" => $request->apellido_paterno,
            "apellido_materno" => $request->apellido_materno,
            "sexo" => $request->sexo,
            "dni" => $request->dni,
            "celular" => $request->celular,
            "correo" => $request->correo,
            "fecha_nacimiento" => $request->fecha_nacimiento
        ]);

        if (!$Estudiante) {
            $data = [
                'message' => 'Error al crear el estudiante',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'Estudiante' => $Estudiante,
            'status' => 201
        ];

        return response()->json($data, 201);
    }



    public function findOneEstudent($dni)
    {
        // Busca al estudiante por su DNI en lugar de ID
        $Estudiante = Estudiante::where('dni', $dni)->first();

        // Verifica si se encontró al estudiante
        if (!$Estudiante) {
            $data = [
                'message' => 'Estudiante no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'Estudiante' => $Estudiante,
            'status' => 200
        ];

        return response()->json($data, 200);
    }


    public function destroy($dni)
    {
        $estudiante = Estudiante::where('dni', $dni)->first();

        if (!$estudiante) {
            $data = [
                'message' => 'Estudiante no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $estudiante->delete();

        $data = [
            'message' => 'Estudiante eliminado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, $dni)
    {
        $estudiante = Estudiante::where('dni', $dni)->first();

        if (!$estudiante) {
            $data = [
                'message' => 'Estudiante no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo_estudiante' => 'string|max:6|unique:estudiante,codigo_estudiante,' . $estudiante->id,
            'nombre' => 'string|max:45',
            'apellido_paterno' => 'string|max:45',
            'apellido_materno' => 'string|max:45',
            'dni' => 'string|max:8|unique:estudiante,dni,' . $estudiante->id,
            'sexo' => 'string|max:1',
            'celular' => 'string|max:9',
            'correo' => 'string|email|max:60|unique:estudiante,correo,' . $estudiante->id,
            'fecha_nacimiento' => 'date'
        ], [
            'codigo_estudiante.unique' => 'El código de estudiante ya está registrado.',
            'dni.unique' => 'El DNI ya está registrado.',
            'correo.unique' => 'El correo electrónico ya está registrado.',
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser un texto.',
            'max' => 'El campo :attribute no debe tener más de :max caracteres.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'date' => 'El campo :attribute debe ser una fecha válida.'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        $estudiante->update($request->all());

        $data = [
            'message' => 'Estudiante actualizado',
            'estudiante' => $estudiante,
            'status' => 200
        ];

        return response()->json($data, 200);
    }



    public function updateParcial(Request $request, $dni)
    {
        $Estudiante = Estudiante::where('dni', $dni)->first();

        if (!$Estudiante) {
            $data = [
                'message' => 'Estudiante no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo_estudiante' => 'string|max:6|unique:estudiante,codigo_estudiante',
            'nombre' => 'string|max:45',
            'apellido_paterno' => 'string|max:45',
            'apellido_materno' => 'string|max:45',
            'dni' => 'string|max:8|unique:estudiante,dni',
            'sexo' => 'string|max:1',
            'celular' => 'string|max:9',
            'correo' => 'string|email|max:60|unique:estudiante,correo',
            'fecha_nacimiento' => 'date'
        ], [
            'codigo_estudiante.unique' => 'El código de estudiante ya está registrado.',
            'dni.unique' => 'El DNI ya está registrado.',
            'correo.unique' => 'El correo electrónico ya está registrado.',
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser un texto.',
            'max' => 'El campo :attribute no debe tener más de :max caracteres.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'date' => 'El campo :attribute debe ser una fecha válida.'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        if ($request->has('codigo_estudiante')) {
            $Estudiante->codigo_estudiante = $request->codigo_estudiante;
        }

        if ($request->has('nombre')) {
            $Estudiante->nombre = $request->nombre;
        }

        if ($request->has('apellido_paterno')) {
            $Estudiante->apellido_paterno = $request->apellido_paterno;
        }

        if ($request->has('apellido_materno')) {
            $Estudiante->apellido_materno = $request->apellido_materno;
        }

        if ($request->has('dni')) {
            $Estudiante->dni = $request->dni;
        }
        if ($request->has('sexo')) {
            $Estudiante->sexo = $request->sexo;
        }

        if ($request->has('celular')) {
            $Estudiante->celular = $request->celular;
        }

        if ($request->has('correo')) {
            $Estudiante->correo = $request->correo;
        }

        if ($request->has('fecha_nacimiento')) {
            $Estudiante->fecha_nacimiento = $request->fecha_nacimiento;
        }

        $Estudiante->save();

        $data = [
            'message' => 'Estudiante actualizado parcialmente',
            'Estudiante' => $Estudiante,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
