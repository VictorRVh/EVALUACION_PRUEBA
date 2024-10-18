<?php

namespace Tests\Feature;

use App\Models\Matricula;
use App\Models\Estudiante;
use App\Models\Especialidad;
use App\Models\Docente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatriculaApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test para obtener todas las matrículas.
     */
    public function testPuedeListarMatriculasCuandoHayDatos(): void
    {
        // Crear datos necesarios
        $docente = Docente::factory()->create();
        $especialidad = Especialidad::factory()->create(['docente_id' => $docente->dni]);
        $estudiante = Estudiante::factory()->create();

        // Crear una matrícula asociada a un estudiante y especialidad
        Matricula::factory()->create([
            'codigo_estudiante_id' => $estudiante->dni,  // Asegúrate de que este campo sea correcto según tu base de datos
            'programa_estudio_id' => $especialidad->programa_estudio  // O el campo correspondiente
        ]);

        // Realiza la solicitud para listar las matrículas
        $response = $this->getJson('/api/matricula');

        // Verifica que la respuesta tenga el código de estado 200 (OK)
        $response->assertStatus(200)
            ->assertJsonStructure([
                'matriculas' => [
                    '*' => [
                        'id',  // Asegúrate de que este campo exista en el JSON de respuesta
                        'codigo_estudiante_id',
                        'programa_estudio_id',
                        'turno',
                        'condicion',
                        'numero_recibo',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'status',
            ]);
    }


    /**
     * Test para obtener una matrícula por su ID.
     */
    public function test_can_get_one_matricula(): void
    {
        // Crear datos necesarios
        $docente = Docente::factory()->create();
        $especialidad = Especialidad::factory()->create(['docente_id' => $docente->dni]);
        $estudiante = Estudiante::factory()->create();

        // Crear una matrícula asociada
        $matricula = Matricula::factory()->create([
            'codigo_estudiante_id' => $estudiante->dni,
            'programa_estudio_id' => $especialidad->programa_estudio_id
        ]);

        // Hacer la solicitud para obtener la matrícula
        $response = $this->getJson("/api/matricula/{$matricula->id}");

        // Verificar que la respuesta sea correcta
        $response->assertStatus(200)
            ->assertJson([
                'matricula' => [
                    'id' => $matricula->id,
                    'codigo_estudiante_id' => $matricula->codigo_estudiante_id,
                    'programa_estudio_id' => $matricula->programa_estudio_id,
                    'turno' => $matricula->turno,
                    'condicion' => $matricula->condicion,
                    'numero_recibo' => $matricula->numero_recibo,
                    // Agrega aquí más campos si es necesario
                ]
            ]);
    }

    /**
     * Test para crear una matrícula.
     */
    public function test_can_create_matricula(): void
    {
        // Crear el estudiante y especialidad relacionados
        $docente = Docente::factory()->create();
        $especialidad = Especialidad::factory()->create(['docente_id' => $docente->dni]);
        $estudiante = Estudiante::factory()->create();


        // Datos para la nueva matrícula
        $data = [
            'codigo_estudiante_id' => $estudiante->dni,
            'turno' => 'M', // Mañana
            'condicion' => 'A', // Activo
            'programa_estudio_id' => $especialidad->programa_estudio,
            'numero_recibo' => '1234567890',
        ];

        // Crear la matrícula
        $response = $this->postJson('/api/matricula', $data);

        $response->assertStatus(201)
            ->assertJson([
                'matricula' => [
                    'codigo_estudiante_id' => $estudiante->dni,
                    'programa_estudio_id' => $especialidad->programa_estudio,
                ],
            ]);
    }

    /**
     * Test para actualizar una matrícula.
     */
    public function test_can_update_matricula(): void
    {
        // Crear datos necesarios
        $docente = Docente::factory()->create();
        $especialidad = Especialidad::factory()->create(['docente_id' => $docente->dni]);
        $estudiante = Estudiante::factory()->create();

        // Crear la matrícula inicial
        $matricula = Matricula::factory()->create([
            'codigo_estudiante_id' => $estudiante->dni,
            'programa_estudio_id' => $especialidad->programa_estudio_id
        ]);

        // Datos actualizados
        $data = [
            'turno' => 'T', // Tarde
            'condicion' => 'I', // Inactivo
            'numero_recibo' => '9876543210',
        ];

        // Actualizar la matrícula
        $response = $this->putJson("/api/matricula/{$matricula->id}", $data);

        // Verificar que la actualización fue exitosa
        $response->assertStatus(200)
            ->assertJson([
                'matricula' => [
                    'id' => $matricula->id,
                    'turno' => 'T',
                    'condicion' => 'I',
                    'numero_recibo' => '9876543210',
                ],
            ]);

        // Verificar que los datos en la base de datos también se han actualizado correctamente
        $this->assertDatabaseHas('matricula', [
            'id' => $matricula->id,
            'turno' => 'T',
            'condicion' => 'I',
            'numero_recibo' => '9876543210',
        ]);
    }


    /**
     * Test para eliminar una matrícula.
     */
    public function test_can_delete_matricula(): void
    {
        // Crear datos necesarios
        $docente = Docente::factory()->create();
        $especialidad = Especialidad::factory()->create(['docente_id' => $docente->dni]);
        $estudiante = Estudiante::factory()->create();

        // Crear la matrícula inicial
        $matricula = Matricula::factory()->create([
            'codigo_estudiante_id' => $estudiante->dni,  // Usar el campo correcto relacionado
            'programa_estudio_id' => $especialidad->programa_estudio_id
        ]);

        // Eliminar la matrícula
        $response = $this->deleteJson("/api/matricula/{$matricula->id}");

        // Verificar que la respuesta tiene el código de estado 204 (No Content)
        $response->assertStatus(204);

        // Verificar que la matrícula ya no está presente en la base de datos
        $this->assertDatabaseMissing('matricula', ['id' => $matricula->id]);
    }

}
