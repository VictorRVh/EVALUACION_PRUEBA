<?php

namespace Tests\Feature;

use App\Models\Docente;
use App\Models\Especialidad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EspecialidadApiTest extends TestCase
{
    use RefreshDatabase; // Asegura que la base de datos se reinicie después de cada prueba

    public function testPuedeCrearEspecialidad()
    {
        // Crear un docente que será referenciado por la especialidad
        $docente = Docente::factory()->create();

        // Datos de prueba para la especialidad
        $especialidadData = [
            'programa_estudio' => 'Ingeniería de Sistemas',
            'ciclo_formativo' => 'Ciclo Básico',
            'modalidad' => 'Presencial',
            'modulo_formativo' => 'Módulo de Programación',
            'descripcion_especialidad' => 'Descripción de prueba para la especialidad.',
            'docente_id' => $docente->dni, // Referencia al docente creado
            'periodo_academico' => '2024-1',
            'hora_semanal' => 30,
            'seccion' => 'A1',
        ];

        // Simular la solicitud POST para crear una especialidad
        $response = $this->postJson('/api/especialidad', $especialidadData);

        // Verificar que la respuesta sea exitosa y que se cree la especialidad
        $response->assertStatus(201)
            ->assertJsonStructure([
                'especialidad' => [
                    'id',
                    'id_unidad',
                    'programa_estudio',
                    'ciclo_formativo',
                    'modalidad',
                    'modulo_formativo',
                    'descripcion_especialidad',
                    'docente_id',
                    'periodo_academico',
                    'hora_semanal',
                    'seccion',
                    'created_at',
                    'updated_at'
                ]
            ]);

        // Asegurarse de que la especialidad se creó correctamente en la base de datos
        $this->assertDatabaseHas('especialidad', [
            'programa_estudio' => 'Ingeniería de Sistemas',
            'docente_id' => $docente->dni
        ]);
    }

    public function testPuedeListarEspecialidadesCuandoHayDatos()
    {
        // Crea un docente para asociar con la especialidad
        $docente = Docente::factory()->create();

        Especialidad::factory()->create([
            'docente_id' => $docente->dni
        ]);

        // Realiza la solicitud para listar especialidades
        $response = $this->getJson('/api/especialidad');

        // Verifica que la respuesta tenga el código de estado 200 (OK)
        $response->assertStatus(200)
            ->assertJsonStructure([
                'especialidades' => [
                    '*' => [
                        'id_unidad',
                        'programa_estudio',
                        'ciclo_formativo',
                        'modalidad',
                        'modulo_formativo',
                        'descripcion_especialidad',
                        'docente_id',
                    ],
                ],
                'status',
            ]);
    }

    public function testNoPuedeListarEspecialidadesSiNoHay()
    {
        // Asegúrate de que no haya especialidades en la base de datos
        // Realiza la solicitud para listar especialidades
        $response = $this->getJson('/api/especialidad');

        // Verifica que la respuesta tenga el código de estado 404 (No encontrado)
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No se encontraron datos',
                'status' => 404,
            ]);
    }

    public function testPuedeActualizarEspecialidad()
    {
        // Crear un docente para asociar con la especialidad
        $docente = Docente::factory()->create();

        // Crear una especialidad
        $especialidad = Especialidad::factory()->create([
            'docente_id' => $docente->dni
        ]);

        // Datos de ejemplo para actualizar la especialidad
        $data = [
            'id_unidad' => '1234',
            'programa_estudio' => 'Desarrollo de Software',
            'ciclo_formativo' => '2',
            'modalidad' => 'Virtual',
            'modulo_formativo' => 'Backend Development',
            'descripcion_especialidad' => 'Descripción actualizada de la especialidad en desarrollo de software',
            'docente_id' => $docente->dni,
            'periodo_academico' => '2024-2025',
            'hora_semanal' => 25,
            'seccion' => 'B1',
        ];

        // Realizar una solicitud PUT para actualizar la especialidad
        $response = $this->putJson("/api/especialidad/{$especialidad->id}", $data);

        // Verificar que la respuesta tiene el código de estado 200 (OK)
        $response->assertStatus(200)
            ->assertJson([
                'especialidad' => [
                    'id_unidad' => '1234',
                    'programa_estudio' => 'Desarrollo de Software',
                    'ciclo_formativo' => '2',
                    'modalidad' => 'Virtual',
                    'modulo_formativo' => 'Backend Development',
                    'descripcion_especialidad' => 'Descripción actualizada de la especialidad en desarrollo de software',
                    'docente_id' => $docente->dni,
                ],
                'status' => 200,
            ]);
    }

    public function testPuedeEliminarEspecialidad()
    {
        // Crear un docente para asociar con la especialidad
        $docente = Docente::factory()->create();

        // Crear una especialidad
        $especialidad = Especialidad::factory()->create([
            'docente_id' => $docente->dni
        ]);

        // Realizar una solicitud DELETE para eliminar la especialidad
        $response = $this->deleteJson("/api/especialidad/{$especialidad->id}");

        // Verificar que la respuesta tiene el código de estado 204 (No Content)
        $response->assertStatus(204);

        // Verificar que la especialidad ha sido eliminada
        $this->assertDatabaseMissing('especialidad', [
            'id' => $especialidad->id,
        ]);
    }
}

