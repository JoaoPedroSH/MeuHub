<?php

namespace Database\Seeders;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'demo@meuhub.local'],
            [
                'name' => 'João Pedro',
                'password' => Hash::make('senha123'),
                'email_verified_at' => now(),
            ]
        );

        // Create initial demo resume
        if ($user->resumes()->count() === 0) {
            $resume = $user->resumes()->create([
                'title' => 'Currículo Desenvolvedor Full Stack',
                'summary' => "Engenheiro de Software com experiência sólida em ecossistemas modernos baseados em Laravel, Vue.js e arquiteturas orientadas a microsserviços e monólitos modulares. Apaixonado por código limpo, automação e interfaces refinadas focadas na experiência do usuário.",
                'additional_info' => "Disponível para atuação remota ou híbrida. Entusiasta de arquitetura de software, boas práticas de segurança e cultura DevOps.",
                'personal_info' => [
                    'full_name' => 'João Pedro',
                    'email' => 'joao@meuhub.local',
                    'phone' => '(11) 98765-4321',
                    'city' => 'São Paulo, SP',
                    'linkedin' => 'linkedin.com/in/joaopedro',
                    'github' => 'github.com/joaopedro',
                    'website' => 'https://joaopedro.dev',
                ],
            ]);

            // Experiences
            $resume->experiences()->create([
                'company' => 'Acme Tech Solutions',
                'position' => 'Engenheiro de Software Sênior',
                'location' => 'São Paulo, SP (Remoto)',
                'start_date' => 'Jan 2022',
                'end_date' => null,
                'is_current' => true,
                'description' => "• Liderança técnica no desenvolvimento de aplicações com Laravel e Vue.js.\n• Implementação de pipelines CI/CD e ambientes containerizados com Docker.\n• Otimização de consultas em bancos relacionais PostgreSQL com redução de 40% na latência.",
                'order_index' => 0,
            ]);

            $resume->experiences()->create([
                'company' => 'Inovação Digital',
                'position' => 'Desenvolvedor Full Stack Pleno',
                'location' => 'Curitiba, PR',
                'start_date' => 'Mar 2019',
                'end_date' => 'Dez 2021',
                'is_current' => false,
                'description' => "• Construção de APIs RESTful integradas a SPAs em Vue.js.\n• Modelagem de dados e implementação de autenticação segura e autorização granular.",
                'order_index' => 1,
            ]);

            // Education
            $resume->education()->create([
                'institution' => 'Universidade de São Paulo (USP)',
                'course' => 'Ciência da Computação',
                'degree' => 'Bacharelado',
                'start_date' => '2015',
                'end_date' => '2019',
                'description' => 'Foco em engenharia de software e banco de dados.',
                'order_index' => 0,
            ]);

            // Skills
            $skills = [
                'PHP', 'Laravel', 'Vue.js', 'Inertia.js', 'PostgreSQL',
                'Docker', 'Docker Compose', 'Tailwind CSS', 'Git', 'Linux',
            ];
            foreach ($skills as $index => $skillName) {
                $resume->skills()->create([
                    'name' => $skillName,
                    'order_index' => $index,
                ]);
            }

            // Languages
            $resume->languages()->create([
                'language' => 'Português',
                'level' => 'Nativo',
                'order_index' => 0,
            ]);

            $resume->languages()->create([
                'language' => 'Inglês',
                'level' => 'Avançado',
                'order_index' => 1,
            ]);

            // Courses & Certifications
            $resume->courses()->create([
                'name' => 'Arquitetura de Software Monolítica e Modular',
                'institution' => 'Coursera',
                'date' => '2023',
                'description' => 'Boas práticas em Laravel e DDD.',
                'order_index' => 0,
            ]);

            $resume->certifications()->create([
                'name' => 'AWS Certified Developer - Associate',
                'institution' => 'Amazon Web Services',
                'date' => '2023',
                'expiration_date' => '2026',
                'code' => 'AWS-DEV-98124',
                'order_index' => 0,
            ]);
        }
    }
}
