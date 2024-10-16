<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Models\Especialidad;
use App\Models\IndicadorLogro;
use App\Models\UnidadDidactica;

use Illuminate\Validation\Rule;


class EspecialidadController extends Controller
{
    //
    public function NameEspecialidad(){
        
    }
    public function index()
    {
        $specialties = Especialidad::with('docente')->get();

        if ($specialties->isEmpty()) {
            $data = [
                'message' => "No se encontraron datos",
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        // Mapear las especialidades para incluir el nombre completo del docente
        $specialties = $specialties->map(function ($especialidad) {
            return [
                'id' => $especialidad->id,
                'id_unidad' => $especialidad->id_unidad,
                'programa_estudio' => $especialidad->programa_estudio,
                'ciclo_formativo' => $especialidad->ciclo_formativo,
                'modalidad' => $especialidad->modalidad,
                'modulo_formativo' => $especialidad->modulo_formativo,
                'descripcion_especialidad' => $especialidad->descripcion_especialidad,
                'docente_id' => $especialidad->docente ? $especialidad->docente->nombre . ' ' . $especialidad->docente->apellido_paterno . ' ' . $especialidad->docente->apellido_materno : null,
                'periodo_academico' => $especialidad->periodo_academico,
                'hora_semanal' => $especialidad->hora_semanal,
                'seccion' => $especialidad->seccion,
               
            ];
        });

         $data = [
            'especialidades' => $specialties,
            'status' => 200
         ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'programa_estudio' => 'required|string|max:100',
            'ciclo_formativo' => 'string|max:50|nullable',
            'modalidad' => 'string|max:45|nullable',
            'modulo_formativo' => 'string|max:200|nullable',
            'descripcion_especialidad' => 'string|nullable',
            'docente_id' => 'string|max:8|nullable|exists:docente,dni',
            'periodo_academico' => 'string|max:10|nullable',
            'hora_semanal' => 'integer|nullable',
            'seccion' => 'string|max:5|nullable',
            
        ],[
            'programa_estudio.required' => 'El campo programa de estudio es obligatorio.',
            'programa_estudio.string' => 'El campo programa de estudio debe ser un texto.',
            'programa_estudio.max' => 'El campo programa de estudio no debe tener más de :max caracteres.',
            'ciclo_formativo.max' => 'El campo ciclo formativo no debe tener más de :max caracteres.',
            'modalidad.max' => 'El campo modalidad no debe tener más de :max caracteres.',
            'modulo_formativo.max' => 'El campo módulo formativo no debe tener más de :max caracteres.',
            'docente_id.exists' => 'El docente no existe en la base de datos.',
            'hora_semanal.integer' => 'El campo horas semanales debe ser un número entero.',
            'seccion.max' => 'El campo sección no debe tener más de :max caracteres.',
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            // Generar el próximo id_unidad en el formato ES00, ES01, ES02
            $lastSpecialty = Especialidad::orderBy('id_unidad', 'desc')->first();
            $nextIdNumber = 0;

            if ($lastSpecialty) {
                $lastId = $lastSpecialty->id_unidad;
                $lastIdNumber = intval(substr($lastId, 2));
                $nextIdNumber = $lastIdNumber + 1;
            }

            // Generar el siguiente id_unidad en formato ES00
            $nextId = 'ES' . str_pad($nextIdNumber, 2, '0', STR_PAD_LEFT);

            // Crear la especialidad con el nuevo id_unidad
            $especialidadData = $request->all();
            $especialidadData['id_unidad'] = $nextId;

            $specialties = Especialidad::create($especialidadData);

            return response()->json([
                'especialidad' => $especialidadData['id_unidad'],
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la especialidad',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }



    public function findOne($id)
    {
        $especialidad = Especialidad::with('docente')->find($id);

        if (!$especialidad) {
            $data = [
                'message' => 'Especialidad no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'id' => $especialidad->id,
            'id_unidad' => $especialidad->id_unidad,
            'programa_estudio' => $especialidad->programa_estudio,
            'ciclo_formativo' => $especialidad->ciclo_formativo,
            'modalidad' => $especialidad->modalidad,
            'modulo_formativo' => $especialidad->modulo_formativo,
            'descripcion_especialidad' => $especialidad->descripcion_especialidad,
            'docente_nombre_completo' => $especialidad->docente ? $especialidad->docente->nombre . ' ' . $especialidad->docente->apellido_paterno . ' ' . $especialidad->docente->apellido_materno : null,
            'periodo_academico' => $especialidad->periodo_academico,
            'hora_semanal' => $especialidad->hora_semanal,
            'seccion' => $especialidad->seccion,
            'status' => 200
        ];

        return response()->json($data, 200);
    }


    public function destroy($id)
    {
        $specialties = Especialidad::find($id);

        if (!$specialties) {
            $data = [
                'message' => 'Especialidad no encontrada',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $specialties->delete();

        $data = [
            'message' => 'Especialidad eliminada',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        $especialidad = Especialidad::find($id);

        if (!$especialidad) {
            return response()->json([
                'message' => 'Especialidad no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'programa_estudio' => [
                'required',
                'string',
                'max:100',
                Rule::unique('especialidad', 'programa_estudio')->ignore($id)
            ],
            'ciclo_formativo' => 'string|max:50|nullable',
            'modalidad' => 'string|max:45|nullable',
            'modulo_formativo' => 'string|max:200|nullable',
            'descripcion_especialidad' => 'string|nullable',
            'docente_id' => 'string|max:8|nullable|exists:docente,dni',
            'periodo_academico' => 'string|max:10|nullable',
            'hora_semanal' => 'integer|nullable',
            'seccion' => 'string|max:5|nullable'
    
        ],[
            'programa_estudio.string' => 'El campo programa de estudio debe ser un texto.',
            'programa_estudio.max' => 'El campo programa de estudio no debe tener más de :max caracteres.',
            'ciclo_formativo.max' => 'El campo ciclo formativo no debe tener más de :max caracteres.',
            'modalidad.max' => 'El campo modalidad no debe tener más de :max caracteres.',
            'modulo_formativo.max' => 'El campo módulo formativo no debe tener más de :max caracteres.',
            'docente_id.exists' => 'El docente no existe en la base de datos.',
            'hora_semanal.integer' => 'El campo horas semanales debe ser un número entero.',
            'seccion.max' => 'El campo sección no debe tener más de :max caracteres.',
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            // Actualizar la especialidad
            $especialidad->update($request->all());

            return response()->json([
                'especialidad' => $especialidad,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la especialidad',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function updateParcial(Request $request, $id)
    {
        $specialties = Especialidad::find($id);

        if (!$specialties) {
            $data = [
                'message' => 'Especialidad no encontrada',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'id_unidad' => 'string|max:4|unique:especialidad,id_unidad',
            'programa_estudio' => 'string|max:100|unique:especialidad,programa_estudio',
            'ciclo_formativo' => 'string|max:50|nullable',
            'modalidad' => 'string|max:45|nullable',
            'modulo_formativo' => 'string|max:200|nullable',
            'descripcion_especialidad' => 'string|nullable',
            'docente_id' => 'string|max:8|nullable|exists:docente,dni',
            'periodo_academico' => 'string|max:10|nullable',
            'hora_semanal' => 'integer|nullable',
            'seccion' => 'string|max:5|nullable'
        ],[
            'programa_estudio.string' => 'El campo programa de estudio debe ser un texto.',
            'programa_estudio.max' => 'El campo programa de estudio no debe tener más de :max caracteres.',
            'ciclo_formativo.max' => 'El campo ciclo formativo no debe tener más de :max caracteres.',
            'modalidad.max' => 'El campo modalidad no debe tener más de :max caracteres.',
            'modulo_formativo.max' => 'El campo módulo formativo no debe tener más de :max caracteres.',
            'docente_id.exists' => 'El docente no existe en la base de datos.',
            'hora_semanal.integer' => 'El campo horas semanales debe ser un número entero.',
            'seccion.max' => 'El campo sección no debe tener más de :max caracteres.',
            
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        if ($request->has('id_unidad')) {
            $specialties->id_unidad = $request->id_unidad;
        }

        if ($request->has('programa_estudio')) {
            $specialties->programa_estudio = $request->programa_estudio;
        }

        if ($request->has('ciclo_formativo')) {
            $specialties->ciclo_formativo = $request->ciclo_formativo;
        }

        if ($request->has('modalidad')) {
            $specialties->modalidad = $request->modalidad;
        }

        if ($request->has('modulo_formativo')) {
            $specialties->modulo_formativo = $request->modulo_formativo;
        }

        if ($request->has('descripcion_especialidad')) {
            $specialties->descripcion_especialidad = $request->descripcion_especialidad;
        }

        if ($request->has('docente_id')) {
            $specialties->docente_id = $request->docente_id;
        }

        if ($request->has('periodo_academico')) {
            $specialties->periodo_academico = $request->periodo_academico;
        }

        if ($request->has('hora_semanal')) {
            $specialties->hora_semanal = $request->hora_semanal;
        }

        if ($request->has('seccion')) {
            $specialties->seccion = $request->seccion;
        }

        $specialties->save();

        $data = [
            'message' => 'Especialidad actualizada parcialmente',
            'especialidad' => $specialties,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
