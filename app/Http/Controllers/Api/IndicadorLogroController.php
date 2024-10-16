<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\IndicadorLogro;

class IndicadorLogroController extends Controller
{
    //
    public function index()
    {
        $indicadores = IndicadorLogro::all();

        if ($indicadores->isEmpty()) {
            return response()->json([
                'message' => "No se encontraron datos",
                'status' => 404
            ], 404);
        }

        return response()->json([
            'indicadores' => $indicadores,
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:300',
            'unidad_didactica_id' => 'required|string|max:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $indicador = IndicadorLogro::create($request->all());

        return response()->json([
            'indicador' => $indicador,
            'message' => 'Indicador de logro creado correctamente',
            'status' => 201
        ], 201);
    }

    public function findOne($id)
    {
        $indicador = IndicadorLogro::find($id);

        if (!$indicador) {
            return response()->json([
                'message' => 'Indicador de logro no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'indicador' => $indicador,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $indicador = IndicadorLogro::find($id);

        if (!$indicador) {
            return response()->json([
                'message' => 'Indicador de logro no encontrado',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'string|max:300|nullable',
            'unidad_didactica_id' => 'string|max:4|exists:unidad_didactica,id_indicador'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $indicador->update($request->all());

        return response()->json([
            'message' => 'Indicador de logro actualizado',
            'indicador' => $indicador,
            'status' => 200
        ], 200);
    }
    public function updateParcial(Request $request, $id)
    {
        $indicadorLogro = IndicadorLogro::find($id);

        if (!$indicadorLogro) {
            $data = [
                'message' => 'Indicador de logro no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'string|max:300',
            'unidad_didactica_id' => 'string|max:4|exists:unidad_didactica,id_indicador'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        if ($request->has('descripcion')) {
            $indicadorLogro->descripcion = $request->descripcion;
        }

        if ($request->has('unidad_didactica_id')) {
            $indicadorLogro->unidad_didactica_id = $request->unidad_didactica_id;
        }

        $indicadorLogro->save();

        $data = [
            'message' => 'Indicador de logro actualizado parcialmente',
            'indicador_logro' => $indicadorLogro,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function destroy($id)
    {
        $indicador = IndicadorLogro::find($id);

        if (!$indicador) {
            return response()->json([
                'message' => 'Indicador de logro no encontrado',
                'status' => 404
            ], 404);
        }

        $indicador->delete();

        return response()->json([
            'message' => 'Indicador de logro eliminado',
            'status' => 200
        ], 200);
    }
}
