<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ExperienciaFormativa;

class ExperienciaFormativaController extends Controller
{
    public function index()
    {
        $experienciaFormativa = ExperienciaFormativa::all();

        if ($experienciaFormativa->isEmpty()) {
            $data = [
                'message' => "No se encontraron datos",
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'experienciaFormativa' => $experienciaFormativa,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'especialidad_id' => 'required|exists:especialidad,id',
            'componente' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date',
            'creditos' => 'required|integer',
            'dias' => 'required|integer',
            'horas' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $experiencia = ExperienciaFormativa::create($request->all());

        if (!$experiencia) {
            return response()->json([
                'message' => 'Error al crear la experiencia formativa',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'experienciaFormativa' => $experiencia,
            'status' => 201
        ], 201);
    }


    public function findOne($id)
    {
        $experienciaFormativa = ExperienciaFormativa::find($id);

        if (!$experienciaFormativa) {
            $data = [
                'message' => 'Experiencia Formativa no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'experiencia_formativa' => $experienciaFormativa,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'especialidad_id' => 'nullable|exists:especialidad,id',
            'componente' => 'nullable|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_termino' => 'nullable|date',
            'creditos' => 'nullable|integer',
            'dias' => 'nullable|integer',
            'horas' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $experiencia = ExperienciaFormativa::find($id);

        if (!$experiencia) {
            return response()->json([
                'message' => 'Experiencia Formativa no encontrada',
                'status' => 404
            ], 404);
        }

        $experiencia->update($request->all());

        return response()->json([
            'message' => 'Experiencia Formativa actualizada correctamente',
            'experienciaFormativa' => $experiencia,
            'status' => 200
        ], 200);
    }


    public function destroy($id)
    {
        $experiencia = ExperienciaFormativa::find($id);

        if (!$experiencia) {
            return response()->json([
                'message' => 'Experiencia Formativa no encontrada',
                'status' => 404
            ], 404);
        }

        $experiencia->delete();

        return response()->json([
            'message' => 'Experiencia Formativa eliminada correctamente',
            'status' => 200
        ], 200);
    }
}
