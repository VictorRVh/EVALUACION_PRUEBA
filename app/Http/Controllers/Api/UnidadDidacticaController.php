<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UnidadDidactica;

class UnidadDidacticaController extends Controller
{
    public function index()
    {
        $unidadDidactica = UnidadDidactica::all();

        if ($unidadDidactica->isEmpty()) {
            $data = [
                'message' => "No se encontraron datos",
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'unidad_didactica' => $unidadDidactica,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'especialidad_id' => 'nullable|string|max:4|exists:especialidad,id_unidad',
            'nombre_unidad' => 'nullable|string|max:130',
            'fecha_inicio' => 'nullable|date',
            'fecha_final' => 'nullable|date',
            'credito_unidad' => 'nullable|integer',
            'hora' => 'nullable|integer',
            'dia' => 'nullable|integer',
            'descripcion_capacidad' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Obtener el último id_indicador usado para la especialidad dada
        $especialidadId = $request->input('especialidad_id');
        $lastUnidadDidactica = UnidadDidactica::where('especialidad_id', $especialidadId)
            ->orderBy('id_indicador', 'desc')
            ->first();

        $nextNumber = 0;
        if ($lastUnidadDidactica) {
            // Extraer el último número usado y sumar 1
            $lastIdIndicador = $lastUnidadDidactica->id_indicador;
            $nextNumber = intval(substr($lastIdIndicador, -2)) + 1;
        }

        // Formatear el próximo número
        $nextNumberFormatted = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        $idIndicador = $especialidadId . 'UD' . $nextNumberFormatted;

        // Crear la unidad didáctica con el nuevo id_indicador
        $unidadDidactica = UnidadDidactica::create(array_merge($request->all(), ['id_indicador' => $idIndicador]));

        if (!$unidadDidactica) {
            return response()->json([
                'message' => 'Error al crear la unidad didáctica',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'unidad_didactica' => $idIndicador,
            'status' => 201
        ], 201);
    }



    public function findOne($id)
    {
        $unidadDidactica = UnidadDidactica::find($id);

        if (!$unidadDidactica) {
            $data = [
                'message' => 'Unidad didáctica no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'unidad_didactica' => $unidadDidactica,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        $unidadDidactica = UnidadDidactica::find($id);

        if (!$unidadDidactica) {
            $data = [
                'message' => 'Unidad didáctica no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'id_indicador' => 'string|max:4|unique:unidad_didactica,id_indicador,' . $id,
            'especialidad_id' => 'nullable|string|max:4|exists:especialidad,id_unidad',
            'nombre_unidad' => 'nullable|string|max:130',
            'fecha_inicio' => 'nullable|date',
            'fecha_final' => 'nullable|date',
            'credito_unidad' => 'nullable|integer',
            'hora' => 'nullable|integer',
            'dia' => 'nullable|integer',
            'descripcion_capacidad' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $unidadDidactica->update($request->all());

        $data = [
            'message' => 'Unidad didáctica actualizada',
            'unidad_didactica' => $unidadDidactica,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
    public function updateParcial(Request $request, $id)
    {
        $unidadDidactica = UnidadDidactica::find($id);

        if (!$unidadDidactica) {
            $data = [
                'message' => 'Unidad Didáctica no encontrada',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'id_indicador' => 'string|max:4|unique:unidad_didactica,id_indicador',
            'especialidad_id' => 'string|max:4|nullable|exists:especialidad,id_unidad',
            'nombre_unidad' => 'string|max:130|nullable',
            'fecha_inicio' => 'date|nullable',
            'fecha_final' => 'date|nullable',
            'credito_unidad' => 'integer|nullable',
            'hora' => 'integer|nullable',
            'dia' => 'integer|nullable',
            'descripcion_capacidad' => 'string|max:500|nullable',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        if ($request->has('id_indicador')) {
            $unidadDidactica->id_indicador = $request->id_indicador;
        }

        if ($request->has('especialidad_id')) {
            $unidadDidactica->especialidad_id = $request->especialidad_id;
        }

        if ($request->has('nombre_unidad')) {
            $unidadDidactica->nombre_unidad = $request->nombre_unidad;
        }

        if ($request->has('fecha_inicio')) {
            $unidadDidactica->fecha_inicio = $request->fecha_inicio;
        }

        if ($request->has('fecha_final')) {
            $unidadDidactica->fecha_final = $request->fecha_final;
        }

        if ($request->has('credito_unidad')) {
            $unidadDidactica->credito_unidad = $request->credito_unidad;
        }

        if ($request->has('hora')) {
            $unidadDidactica->hora = $request->hora;
        }

        if ($request->has('dia')) {
            $unidadDidactica->dia = $request->dia;
        }

        if ($request->has('descripcion_capacidad')) {
            $unidadDidactica->descripcion_capacidad = $request->descripcion_capacidad;
        }

        $unidadDidactica->save();

        $data = [
            'message' => 'Unidad Didáctica actualizada parcialmente',
            'unidad_didactica' => $unidadDidactica,
            'status' => 200
        ];

        return response()->json($data, 200);
    }


    public function destroy($id)
    {
        $unidadDidactica = UnidadDidactica::find($id);

        if (!$unidadDidactica) {
            $data = [
                'message' => 'Unidad didáctica no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $unidadDidactica->delete();

        $data = [
            'message' => 'Unidad didáctica eliminada',
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
