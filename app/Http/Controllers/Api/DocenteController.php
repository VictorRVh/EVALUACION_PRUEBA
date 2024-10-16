<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Models\Docente;

class DocenteController extends Controller
{
    public function index()
    {
        $teachers = Docente::all();

        if ($teachers->isEmpty()) {
            $data = [
                'message' => "No se encontraron datos",
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'teachers' => $teachers,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
    //metodo GetDocenteNombreApellidos
    public function indexName()
    {
        // Obtén los docentes asignados a una especialidad
        $assignedTeachers = Docente::join('especialidad', 'docente.dni', '=', 'especialidad.docente_id')
            ->select('docente.dni')
            ->distinct()
            ->pluck('dni');

        // Obtén los docentes que no están asignados a ninguna especialidad
        $teachers = Docente::select('dni', 'nombre', 'apellido_paterno', 'apellido_materno')
            ->whereNotIn('dni', $assignedTeachers)
            ->get();

        if ($teachers->isEmpty()) {
            $data = [
                'message' => "No se encontraron datos",
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'teachers' => $teachers,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:60',
            'apellido_paterno' => 'required|string|max:60',
            'apellido_materno' => 'required|string|max:60',
            'dni' => 'required|string|max:8|unique:docente',
            'sexo' => 'required|string|max:1',
            'celular' => 'required|string|max:9',
            'correo' => 'required|string|email|max:255|unique:docente',
            'fecha_nacimiento' => 'required|date'
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser un texto.',
            'nombre.max' => 'El campo nombre no debe tener más de :max caracteres.',
            'apellido_paterno.required' => 'El campo apellido paterno es obligatorio.',
            'apellido_paterno.string' => 'El campo apellido paterno debe ser un texto.',
            'apellido_paterno.max' => 'El campo apellido paterno no debe tener más de :max caracteres.',
            'apellido_materno.required' => 'El campo apellido materno es obligatorio.',
            'apellido_materno.string' => 'El campo apellido materno debe ser un texto.',
            'apellido_materno.max' => 'El campo apellido materno no debe tener más de :max caracteres.',
            'dni.required' => 'El campo DNI es obligatorio.',
            'dni.string' => 'El campo DNI debe ser un texto.',
            'dni.max' => 'El campo DNI no debe tener más de :max caracteres.',
            'dni.unique' => 'El DNI ya está registrado.',
            'sexo.required' => 'El campo sexo es obligatorio.',
            'sexo.string' => 'El campo sexo debe ser un texto.',
            'sexo.max' => 'El campo sexo no debe tener más de :max caracteres.',
            'celular.required' => 'El campo celular es obligatorio.',
            'celular.string' => 'El campo celular debe ser un texto.',
            'celular.max' => 'El campo celular no debe tener más de :max caracteres.',
            'correo.required' => 'El campo correo es obligatorio.',
            'correo.string' => 'El campo correo debe ser un texto.',
            'correo.email' => 'El campo correo debe ser una dirección de correo válida.',
            'correo.max' => 'El campo correo no debe tener más de :max caracteres.',
            'correo.unique' => 'El correo ya está registrado.',
            'fecha_nacimiento.required' => 'El campo fecha de nacimiento es obligatorio.',
            'fecha_nacimiento.date' => 'El campo fecha de nacimiento debe ser una fecha válida.'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        $teacher = Docente::create($request->all());

        if (!$teacher) {
            $data = [
                'message' => 'Error al crear el docente',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'teacher' => $teacher,
            'status' => 201
        ];

        return response()->json($data, 201);
    }

    public function findOneDocente($dni)
    {
        $teacher = Docente::where('dni', $dni)->first();

        if (!$teacher) {
            $data = [
                'message' => 'Docente no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'teacher' => $teacher,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, $dni)
    {
        $teacher = Docente::where('dni', $dni)->first();

        if (!$teacher) {
            $data = [
                'message' => 'Docente no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'apellido_paterno' => 'string|max:60',
            'apellido_materno' => 'string|max:60',
            'dni' => 'string|max:8|unique:docente,dni,' . $teacher->id,
            'sexo' => 'string|max:1',
            'celular' => 'string|max:9',
            'correo' => 'string|email|max:255|unique:docente,correo,' . $teacher->id
        ], [
            'nombre.string' => 'El campo nombre debe ser un texto.',
            'nombre.max' => 'El campo nombre no debe exceder los :max caracteres.',
            'apellido_paterno.string' => 'El campo apellido paterno debe ser un texto.',
            'apellido_paterno.max' => 'El campo apellido paterno no debe exceder los :max caracteres.',
            'apellido_materno.string' => 'El campo apellido materno debe ser un texto.',
            'apellido_materno.max' => 'El campo apellido materno no debe exceder los :max caracteres.',
            'dni.string' => 'El campo DNI debe ser un texto.',
            'dni.max' => 'El campo DNI no debe exceder los :max caracteres.',
            'dni.unique' => 'El DNI ya está registrado para otro docente.',
            'sexo.string' => 'El campo sexo debe ser un texto.',
            'sexo.max' => 'El campo sexo no debe exceder los :max caracteres.',
            'celular.string' => 'El campo celular debe ser un texto.',
            'celular.max' => 'El campo celular no debe exceder los :max caracteres.',
            'correo.string' => 'El campo correo debe ser un texto.',
            'correo.email' => 'El campo correo debe ser una dirección de correo válida.',
            'correo.max' => 'El campo correo no debe exceder los :max caracteres.',
            'correo.unique' => 'El correo ya está registrado para otro docente.'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        $teacher->update($request->all());

        $data = [
            'message' => 'Docente actualizado',
            'teacher' => $teacher,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function destroy($dni)
    {
        $teacher = Docente::where('dni', $dni)->first();

        if (!$teacher) {
            $data = [
                'message' => 'Docente no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $teacher->delete();

        $data = [
            'message' => 'Docente eliminado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function updateParcial(Request $request, $dni)
    {
        $teacher = Docente::where('dni', $dni)->first();

        if (!$teacher) {
            $data = [
                'message' => 'Docente no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'apellido_paterno' => 'string|max:60',
            'apellido_materno' => 'string|max:60',
            'dni' => 'string|max:8|unique:docente,dni,' . $dni,
            'sexo' => 'string|max:1',
            'celular' => 'string|max:9',
            'correo' => 'string|email|max:255|unique:docente,correo,' . $dni
        ], [
            'nombre.string' => 'El campo nombre debe ser un texto.',
            'nombre.max' => 'El campo nombre no debe exceder los :max caracteres.',
            'apellido_paterno.string' => 'El campo apellido paterno debe ser un texto.',
            'apellido_paterno.max' => 'El campo apellido paterno no debe exceder los :max caracteres.',
            'apellido_materno.string' => 'El campo apellido materno debe ser un texto.',
            'apellido_materno.max' => 'El campo apellido materno no debe exceder los :max caracteres.',
            'dni.string' => 'El campo DNI debe ser un texto.',
            'dni.max' => 'El campo DNI no debe exceder los :max caracteres.',
            'dni.unique' => 'El DNI ya está registrado para otro docente.',
            'sexo.string' => 'El campo sexo debe ser un texto.',
            'sexo.max' => 'El campo sexo no debe exceder los :max caracteres.',
            'celular.string' => 'El campo celular debe ser un texto.',
            'celular.max' => 'El campo celular no debe exceder los :max caracteres.',
            'correo.string' => 'El campo correo debe ser un texto.',
            'correo.email' => 'El campo correo debe ser una dirección de correo válida.',
            'correo.max' => 'El campo correo no debe exceder los :max caracteres.',
            'correo.unique' => 'El correo ya está registrado para otro docente.'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        if ($request->has('nombre')) {
            $teacher->nombre = $request->nombre;
        }

        if ($request->has('apellido_paterno')) {
            $teacher->apellido_paterno = $request->apellido_paterno;
        }

        if ($request->has('apellido_materno')) {
            $teacher->apellido_materno = $request->apellido_materno;
        }

        if ($request->has('dni')) {
            $teacher->dni = $request->dni;
        }

        if ($request->has('sexo')) {
            $teacher->sexo = $request->sexo;
        }

        if ($request->has('celular')) {
            $teacher->celular = $request->celular;
        }

        if ($request->has('correo')) {
            $teacher->correo = $request->correo;
        }

        $teacher->save();

        $data = [
            'message' => 'Docente actualizado parcialmente',
            'teacher' => $teacher,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
