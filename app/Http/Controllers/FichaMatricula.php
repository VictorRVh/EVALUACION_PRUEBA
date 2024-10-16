<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\Especialidad;
use App\Models\Matricula;

class FichaMatricula extends Controller
{

    private function formatName($apellidoPaterno, $apellidoMaterno, $nombres)
    {
        // Formatear apellidos en mayúsculas
        $apellidos = strtoupper($apellidoPaterno) . ' ' . strtoupper($apellidoMaterno);

        // Formatear nombres con solo la inicial en mayúscula
        $nombresFormateados = collect(explode(' ', $nombres))
            ->map(fn($nombre) => ucfirst(strtolower($nombre)))
            ->implode(' ');

        // Concatenar apellidos y nombres
        return "{$apellidos}, {$nombresFormateados}";
    }

    public function getEstudianteWithEspecialidadAndUnidades($codigoEstudiante)
    {
        // Obtener todas las matrículas del estudiante
        $matriculas = Matricula::where('codigo_estudiante_id', $codigoEstudiante)
            ->with(['especialidad.unidadesDidacticas'])
            ->get();

        // Si no se encuentra ninguna matrícula, retornar un error
        if ($matriculas->isEmpty()) {
            return response()->json(['error' => 'Estudiante no encontrado o sin matrículas'], 404);
        }

        // Obtener la matrícula más reciente
        $matriculaReciente = $matriculas->sortByDesc('created_at')->first();

        // Obtener la especialidad y unidades didácticas de la matrícula más reciente
        $especialidad = $matriculaReciente->especialidad;
        $unidadesDidacticas = $especialidad->unidadesDidacticas;

        // Obtener el nombre de las unidades didácticas en un solo string
        $nombresUnidades = $unidadesDidacticas->pluck('nombre_unidad')->implode(', ');

        // Obtener la fecha de inicio de la primera unidad y la fecha de fin de la última unidad
        $fechaInicio = $unidadesDidacticas->min('fecha_inicio');
        $fechaFin = $unidadesDidacticas->max('fecha_final');

        // Calcular el total de créditos y las horas por unidad
        $totalCreditos = $unidadesDidacticas->sum('credito_unidad');
        $totalHoras = $unidadesDidacticas->sum('hora');

        // Formatear el nombre completo del estudiante
        $nombreEstudiante = $this->formatName(
            $matriculaReciente->estudiante->apellido_paterno,
            $matriculaReciente->estudiante->apellido_materno,
            $matriculaReciente->estudiante->nombre
        );

        // Formatear el nombre completo del docente
        $nombreDocente = $especialidad->docente
            ? $this->formatName(
                $especialidad->docente->apellido_paterno,
                $especialidad->docente->apellido_materno,
                $especialidad->docente->nombre
            )
            : 'No disponible';

        // Mapea la información que deseas enviar en la respuesta
        $response = [
            'codigo_estudiante' => $matriculaReciente->codigo_estudiante_id,
            'nombre_completo' => $nombreEstudiante,
            'correo' => $matriculaReciente->estudiante->correo,
            'dni' => $matriculaReciente->estudiante->dni,
            'especialidad' => [
                'nombre' => $especialidad->programa_estudio,
                'docente' => $nombreDocente,
                'modalidad' => $especialidad->modalidad,
                'ciclo_formativo' => $especialidad->ciclo_formativo,
                'modulo_formativo' => $especialidad->modulo_formativo,
                'periodo_academico' => $especialidad->periodo_academico,
                'hora_semanal' => $especialidad->hora_semanal,
                'seccion' => $especialidad->seccion,
                'turno' => $especialidad->turno,
            ],
            'unidades_didacticas' => [
                'nombres_unidades' => $nombresUnidades,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'total_creditos' => $totalCreditos,
                'total_horas' => $totalHoras,
                'detalles' => $unidadesDidacticas->map(function ($unidad) {
                    return [
                        'nombre_unidad' => $unidad->nombre_unidad,
                        'credito_unidad' => $unidad->credito_unidad,
                        'hora' => $unidad->hora,
                        'dia' => $unidad->dia,
                        'fecha_inicio' => $unidad->fecha_inicio,
                        'fecha_final' => $unidad->fecha_final,
                    ];
                })
            ]
        ];

        return response()->json($response);
    }

    public function getEstudiantesPorEspecialidad($especialidadId, $turno)
    {
        $especialidad = Especialidad::with(['matriculas.estudiante', 'unidadesDidacticas'])
            ->where('programa_estudio', $especialidadId)
            ->first();

        if (!$especialidad) {
            return response()->json(['error' => 'Especialidad no encontrada'], 404);
        }

        // Filtrar las matrículas por turno
        $matriculas = $especialidad->matriculas->filter(function ($matricula) use ($turno) {
            return $matricula->turno === $turno;
        });

        // Calcular el total de unidades didácticas y la suma de créditos
        $totalUnidadesDidacticas = $especialidad->unidadesDidacticas->count();
        $sumaCreditos = $especialidad->unidadesDidacticas->sum('credito_unidad');

        // Obtener las fechas de inicio y fin
        $fechaInicio = $especialidad->unidadesDidacticas->min('fecha_inicio');
        $fechaFin = $especialidad->unidadesDidacticas->max('fecha_final');

        // Mapear los estudiantes y aplicar el formato a los nombres
        $estudiantes = $matriculas->map(function ($matricula) {
            $nombreFormateado = $this->formatName(
                $matricula->estudiante->apellido_paterno,
                $matricula->estudiante->apellido_materno,
                $matricula->estudiante->nombre
            );

            return [
                'codigo_matricula' => $matricula->codigo_estudiante_id,
                'condicion' => $matricula->condicion,
                'turno' => $matricula->turno,
                'apellidos_nombres' => $nombreFormateado,
                'sexo' => $matricula->estudiante->sexo,
                'fecha_nacimiento' => $matricula->estudiante->fecha_nacimiento,
            ];
        })
            ->sortBy('apellidos_nombres') // Ordenar por apellidos_nombres, que incluye apellido_paterno
            ->values() // Asegurar que se mantenga el índice secuencial después de ordenar
            ->toArray(); // Convertir a arreglo

        // Contar el número de hombres y mujeres
        $sexoCount = collect($estudiantes)->countBy('sexo');

        // Contar el número de estudiantes por condición
        $condicionCount = collect($estudiantes)->countBy('condicion');

        $response = [
            'nombre_especialidad' => $especialidad->programa_estudio,
            'modulo_formativo' => $especialidad->modulo_formativo,
            'turno' => $turno,
            'seccion' => $especialidad->seccion,
            'total_unidades_didacticas' => $totalUnidadesDidacticas,
            'suma_creditos' => $sumaCreditos,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'numero_hombres' => $sexoCount->get('M', 0),
            'numero_mujeres' => $sexoCount->get('F', 0),
            'condicion_g' => $condicionCount->get('G', 0),
            'condicion_p' => $condicionCount->get('P', 0),
            'condicion_b' => $condicionCount->get('B', 0),
            'estudiantes' => $estudiantes
        ];

        return response()->json($response);
    }



    public function getRegistroMatriculaPorNombre($nombreEspecialidad)
    {
        $especialidad = Especialidad::with(['matriculas.estudiante', 'unidadesDidacticas'])
            ->where('programa_estudio', $nombreEspecialidad)
            ->first();

        if (!$especialidad) {
            return response()->json(['error' => 'Especialidad no encontrada'], 404);
        }

        $estudiantesOrdenados = $especialidad->matriculas->sortBy(function ($matricula) {
            return strtolower($matricula->estudiante->apellido_paterno);
        })->values(); // Reindexa la colección después de ordenarla

        $response = [
            'nombre_especialidad' => $especialidad->programa_estudio,
            'modulo_formativo' => $especialidad->modulo_formativo,
            'estudiantes' => $estudiantesOrdenados->map(function ($matricula) {
                return [
                    'codigo_estudiante' => $matricula->estudiante->codigo_estudiante,
                    'dni' => $matricula->estudiante->dni,
                    'apellido_paterno' => $matricula->estudiante->apellido_paterno,
                    'apellido_materno' => $matricula->estudiante->apellido_materno,
                    'nombre' => $matricula->estudiante->nombre,
                    'sexo' => $matricula->estudiante->sexo,
                    'fecha_nacimiento' => $matricula->estudiante->fecha_nacimiento,
                ];
            })
        ];

        return response()->json($response);
    }

    public function getEstudiantesPorEspecialidadYTurno($especialidadId, $turno)
    {
        // Encontrar la especialidad por ID
        $especialidad = Especialidad::where('programa_estudio', $especialidadId)
            ->first();

        if (!$especialidad) {
            return response()->json(['error' => 'Especialidad no encontrada'], 404);
        }

        // Obtener las matrículas que coinciden con la especialidad y el turno
        $estudiantes = Matricula::with('estudiante')
            ->where('programa_estudio_id', $especialidadId)
            ->where('turno', $turno)
            ->get()
            ->map(function ($matricula) {
                return [
                    'codigo_estudiante' => $matricula->estudiante->codigo_estudiante,
                    'dni' => $matricula->estudiante->dni,
                    'apellido_paterno' => $matricula->estudiante->apellido_paterno,
                    'apellido_materno' => $matricula->estudiante->apellido_materno,
                    'nombre' => $matricula->estudiante->nombre,
                    'sexo' => $matricula->estudiante->sexo,
                    'fecha_nacimiento' => $matricula->estudiante->fecha_nacimiento,
                    'celular' => $matricula->estudiante->celular,
                    'correo' => $matricula->estudiante->correo,
                ];
            })->toArray(); // Convertir a arreglo

        return response()->json([
            'especialidad' => $especialidad->programa_estudio,
            'turno' => $turno,
            'estudiantes' => $estudiantes
        ]);
    }
}
