<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_resume_stats(): void
    {
        $user = User::factory()->create();
        $resume = Resume::factory()->create([
            'user_id' => $user->id,
            'title' => 'Meu Currículo Principal',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('resumesCount', 1)
            ->where('latestResume.title', 'Meu Currículo Principal')
        );
    }

    public function test_user_can_view_resumes_index(): void
    {
        $user = User::factory()->create();
        Resume::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('resumes.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Resumes/Index')
            ->has('resumes', 3)
        );
    }

    public function test_user_can_create_a_resume(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('resumes.store'), [
            'title' => 'Currículo Desenvolvedor Fullstack',
        ]);

        $resume = Resume::where('user_id', $user->id)->first();
        $this->assertNotNull($resume);
        $this->assertEquals('Currículo Desenvolvedor Fullstack', $resume->title);
        $this->assertEquals($user->name, $resume->personal_info['full_name']);

        $response->assertRedirect(route('resumes.edit', $resume));
    }

    public function test_user_can_update_resume_and_relations(): void
    {
        $user = User::factory()->create();
        $resume = Resume::factory()->create(['user_id' => $user->id]);

        $payload = [
            'title' => 'Currículo Atualizado',
            'summary' => 'Profissional com mais de 5 anos de experiência.',
            'additional_info' => 'Disponível para trabalho remoto.',
            'personal_info' => [
                'full_name' => 'João Silva',
                'email' => 'joao@exemplo.com',
                'phone' => '(11) 98765-4321',
                'city' => 'São Paulo, SP',
                'linkedin' => 'linkedin.com/in/joaosilva',
                'github' => 'github.com/joaosilva',
                'website' => 'https://joaosilva.dev',
            ],
            'experiences' => [
                [
                    'company' => 'Empresa Alfa',
                    'position' => 'Engenheiro de Software',
                    'location' => 'Remoto',
                    'start_date' => '2022',
                    'end_date' => null,
                    'is_current' => true,
                    'description' => 'Desenvolvimento de APIs robustas.',
                ],
            ],
            'education' => [
                [
                    'institution' => 'USP',
                    'course' => 'Ciência da Computação',
                    'degree' => 'Bacharelado',
                    'start_date' => '2018',
                    'end_date' => '2022',
                    'description' => 'Formação sólida em algoritmos.',
                ],
            ],
            'skills' => [
                ['name' => 'PHP'],
                ['name' => 'Laravel'],
                ['name' => 'Vue.js'],
                ['name' => 'PostgreSQL'],
            ],
            'languages' => [
                ['language' => 'Português', 'level' => 'Nativo'],
                ['language' => 'Inglês', 'level' => 'Avançado'],
            ],
            'courses' => [
                [
                    'name' => 'Docker para Desenvolvedores',
                    'institution' => 'Alura',
                    'date' => '2023',
                    'description' => 'Containers e orquestração.',
                ],
            ],
            'certifications' => [
                [
                    'name' => 'AWS Certified Developer',
                    'institution' => 'Amazon Web Services',
                    'date' => '2024',
                    'code' => 'AWS-12345',
                ],
            ],
        ];

        $response = $this->actingAs($user)->put(route('resumes.update', $resume), $payload);

        $response->assertSessionHas('success');

        $resume->refresh();
        $this->assertEquals('Currículo Atualizado', $resume->title);
        $this->assertEquals('Profissional com mais de 5 anos de experiência.', $resume->summary);
        $this->assertEquals('João Silva', $resume->personal_info['full_name']);

        $this->assertCount(1, $resume->experiences);
        $this->assertEquals('Empresa Alfa', $resume->experiences->first()->company);
        $this->assertTrue($resume->experiences->first()->is_current);

        $this->assertCount(1, $resume->education);
        $this->assertEquals('USP', $resume->education->first()->institution);

        $this->assertCount(4, $resume->skills);
        $this->assertCount(2, $resume->languages);
        $this->assertCount(1, $resume->courses);
        $this->assertCount(1, $resume->certifications);
    }

    public function test_user_can_duplicate_resume(): void
    {
        $user = User::factory()->create();
        $resume = Resume::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original',
        ]);
        $resume->skills()->create(['name' => 'Laravel']);

        $response = $this->actingAs($user)->post(route('resumes.duplicate', $resume));

        $response->assertRedirect(route('resumes.index'));

        $this->assertEquals(2, $user->resumes()->count());
        $duplicated = Resume::where('title', 'Original (Cópia)')->first();
        $this->assertNotNull($duplicated);
        $this->assertCount(1, $duplicated->skills);
        $this->assertEquals('Laravel', $duplicated->skills->first()->name);
    }

    public function test_user_can_export_resume_to_pdf(): void
    {
        $user = User::factory()->create();
        $resume = Resume::factory()->create([
            'user_id' => $user->id,
            'title' => 'Curriculo Teste PDF',
            'personal_info' => ['full_name' => 'Maria Silva'],
        ]);

        $response = $this->actingAs($user)->get(route('resumes.pdf', $resume));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_user_can_delete_resume(): void
    {
        $user = User::factory()->create();
        $resume = Resume::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('resumes.destroy', $resume));

        $response->assertRedirect(route('resumes.index'));
        $this->assertDatabaseMissing('resumes', ['id' => $resume->id]);
    }

    public function test_user_cannot_access_or_modify_another_users_resume(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $resumeA = Resume::factory()->create([
            'user_id' => $userA->id,
            'title' => 'Privado do Usuário A',
        ]);

        // Attempt view
        $this->actingAs($userB)->get(route('resumes.show', $resumeA))->assertForbidden();

        // Attempt edit
        $this->actingAs($userB)->get(route('resumes.edit', $resumeA))->assertForbidden();

        // Attempt update
        $this->actingAs($userB)->put(route('resumes.update', $resumeA), [
            'title' => 'Hacked',
        ])->assertForbidden();

        // Attempt duplicate
        $this->actingAs($userB)->post(route('resumes.duplicate', $resumeA))->assertForbidden();

        // Attempt PDF
        $this->actingAs($userB)->get(route('resumes.pdf', $resumeA))->assertForbidden();

        // Attempt delete
        $this->actingAs($userB)->delete(route('resumes.destroy', $resumeA))->assertForbidden();
    }
}
