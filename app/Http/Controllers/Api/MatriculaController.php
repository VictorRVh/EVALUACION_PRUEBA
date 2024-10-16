<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Matricula;

use Illuminate\Support\Facades\Validator;



class MatriculaController extends Controller
{
    public function index()
    {
        $matriculas = Matricula::all();

        if ($matriculas->isEmpty()) {
            $data = [
                'message' => "No se encontraron datos",
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'matriculas' => $matriculas,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_estudiante_id' => 'required|string|max:8|exists:estudiante,dni',
            'turno' => 'required|string|max:1',
            'condicion' => 'required|string|max:1',
            'programa_estudio_id' => 'required|string|max:100|exists:especialidad,programa_estudio',
            'numero_recibo' => 'required|string|max:10|unique:matricula',
        ], [
            'codigo_estudiante_id.required' => 'El campo código de estudiante es obligatorio.',
            'codigo_estudiante_id.string' => 'El campo código de estudiante debe ser un texto.',
            'codigo_estudiante_id.max' => 'El campo código de estudiante no debe exceder los :max caracteres.',
            'codigo_estudiante_id.exists' => 'El código de estudiante no existe en la base de datos.',
            'turno.required' => 'El campo turno es obligatorio.',
            'turno.string' => 'El campo turno debe ser un texto.',
            'turno.max' => 'El campo turno no debe exceder los :max caracteres.',
            'condicion.required' => 'El campo condición es obligatorio.',
            'condicion.string' => 'El campo condición debe ser un texto.',
            'condicion.max' => 'El campo condición no debe exceder los :max caracteres.',
            'programa_estudio_id.required' => 'El campo programa de estudio es obligatorio.',
            'programa_estudio_id.string' => 'El campo programa de estudio debe ser un texto.',
            'programa_estudio_id.max' => 'El campo programa de estudio no debe exceder los :max caracteres.',
            'programa_estudio_id.exists' => 'El programa de estudio no existe en la base de datos.',
            'numero_recibo.required' => 'El campo número de recibo es obligatorio.',
            'numero_recibo.string' => 'El campo número de recibo debe ser un texto.',
            'numero_recibo.max' => 'El campo número de recibo no debe exceder los :max caracteres.',
            'numero_recibo.unique' => 'El número de recibo ya está registrado.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Verifica que el estudiante no esté matriculado en la misma especialidad
        $existingMatricula = Matricula::where('codigo_estudiante_id', $request->codigo_estudiante_id)
            ->where('programa_estudio_id', $request->programa_estudio_id)
            ->first();

        if ($existingMatricula) {
            return response()->json([
                'message' => 'El estudiante ya está matriculado en esta especialidad',
                'status' => 400
            ], 400);
        }

        // Verificar cuántas especialidades tiene ya el estudiante
        $especialidadesCount = Matricula::where('codigo_estudiante_id', $request->codigo_estudiante_id)->count();

        if ($especialidadesCount >= 2) {
            return response()->json([
                'message' => 'El estudiante no puede matricularse en más de dos especialidades',
                'status' => 400
            ], 400);
        }

        // Verificación del turno
        $mananaMatricula = Matricula::where('codigo_estudiante_id', $request->codigo_estudiante_id)
            ->where('turno', $request->turno)
            ->first();

        if ($mananaMatricula) {
            return response()->json([
                'message' => 'El estudiante no puede matricularse en el turno ' . ($request->turno === 'M' ? 'mañana' : 'tarde'),
                'status' => 400
            ], 400);
        }

        $matricula = Matricula::create($request->all());

        if (!$matricula) {
            return response()->json([
                'message' => 'Error al crear la matrícula',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'matricula' => $matricula,
            'status' => 201
        ], 201);
    }

    public function findOne($id)
    {
        $matricula = Matricula::find($id);

        if (!$matricula) {
            $data = [
                'message' => 'Matrícula no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'matricula' => $matricula,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        $matricula = Matricula::find($id);

        if (!$matricula) {
            $data = [
                'message' => 'Matrícula no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo_estudiante_id' => 'string|max:6|exists:estudiante,dni|unique:matricula',
            'turno' => 'string|max:1',
            'condicion' => 'string|max:1',
            'programa_estudio_id' => 'string|max:100|exists:especialidad,programa_estudio',
            'numero_recibo' => 'string|max:10|unique:matricula',
        ], [
            'codigo_estudiante_id.string' => 'El código de estudiante debe ser un texto.',
            'codigo_estudiante_id.max' => 'El código de estudiante no debe exceder los :max caracteres.',
            'codigo_estudiante_id.exists' => 'El código de estudiante no existe en la base de datos.',
            'codigo_estudiante_id.unique' => 'El código de estudiante ya está registrado en una matrícula.',
            'turno.string' => 'El turno debe ser un texto.',
            'turno.max' => 'El turno no debe exceder los :max caracteres.',
            'condicion.string' => 'La condición debe ser un texto.',
            'condicion.max' => 'La condición no debe exceder los :max caracteres.',
            'programa_estudio_id.string' => 'El programa de estudio debe ser un texto.',
            'programa_estudio_id.max' => 'El programa de estudio no debe exceder los :max caracteres.',
            'programa_estudio_id.exists' => 'El programa de estudio no existe en la base de datos.',
            'numero_recibo.string' => 'El número de recibo debe ser un texto.',
            'numero_recibo.max' => 'El número de recibo no debe exceder los :max caracteres.',
            'numero_recibo.unique' => 'El número de recibo ya está registrado en una matrícula.'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $matricula->update($request->all());

        $data = [
            'message' => 'Matrícula actualizada',
            'matricula' => $matricula,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
    public function updateParcial(Request $request, $id)
    {
        $matricula = Matricula::find($id);

        if (!$matricula) {
            $data = [
                'message' => 'Matrícula no encontrada',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo_estudiante_id' => 'string|max:6|exists:estudiante,dni|unique:matricula',
            'turno' => 'string|max:1',
            'condicion' => 'string|max:1',
            'programa_estudio_id' => 'string|max:100|exists:especialidad,programa_estudio',
            'numero_recibo' => 'string|max:10|unique:matricula',
        ], [
            'codigo_estudiante_id.string' => 'El código de estudiante debe ser un texto.',
            'codigo_estudiante_id.max' => 'El código de estudiante no debe exceder los :max caracteres.',
            'codigo_estudiante_id.exists' => 'El código de estudiante no existe en la base de datos.',
            'codigo_estudiante_id.unique' => 'El código de estudiante ya está registrado en una matrícula.',
            'turno.string' => 'El turno debe ser un texto.',
            'turno.max' => 'El turno no debe exceder los :max caracteres.',
            'condicion.string' => 'La condición debe ser un texto.',
            'condicion.max' => 'La condición no debe exceder los :max caracteres.',
            'programa_estudio_id.string' => 'El programa de estudio debe ser un texto.',
            'programa_estudio_id.max' => 'El programa de estudio no debe exceder los :max caracteres.',
            'programa_estudio_id.exists' => 'El programa de estudio no existe en la base de datos.',
            'numero_recibo.string' => 'El número de recibo debe ser un texto.',
            'numero_recibo.max' => 'El número de recibo no debe exceder los :max caracteres.',
            'numero_recibo.unique' => 'El número de recibo ya está registrado en una matrícula.'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        if ($request->has('codigo_estudiante_id')) {
            $matricula->codigo_estudiante_id = $request->codigo_estudiante_id;
        }

        if ($request->has('turno')) {
            $matricula->turno = $request->turno;
        }

        if ($request->has('condicion')) {
            $matricula->condicion = $request->condicion;
        }

        if ($request->has('programa_estudio_id')) {
            $matricula->programa_estudio_id = $request->programa_estudio_id;
        }

        if ($request->has('numero_recibo')) {
            $matricula->numero_recibo = $request->numero_recibo;
        }

        $matricula->save();

        $data = [
            'message' => 'Matrícula actualizada parcialmente',
            'matricula' => $matricula,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
    public function destroy($id)
    {
        $matricula = Matricula::find($id);

        if (!$matricula) {
            $data = [
                'message' => 'Matrícula no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $matricula->delete();

        $data = [
            'message' => 'Matrícula eliminada',
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
