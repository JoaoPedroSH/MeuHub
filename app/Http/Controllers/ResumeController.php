<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ResumeController extends Controller
{
    /**
     * Display a listing of the user's resumes.
     */
    public function index(): Response
    {
        $resumes = auth()->user()->resumes()
            ->withCount(['experiences', 'education', 'skills'])
            ->get()
            ->map(fn ($resume) => [
                'id' => $resume->id,
                'title' => $resume->title,
                'summary' => $resume->summary,
                'personal_info' => $resume->personal_info,
                'experiences_count' => $resume->experiences_count,
                'education_count' => $resume->education_count,
                'skills_count' => $resume->skills_count,
                'updated_at' => $resume->updated_at->toISOString(),
                'updated_at_formatted' => $resume->updated_at->diffForHumans(),
            ]);

        return Inertia::render('Resumes/Index', [
            'resumes' => $resumes,
        ]);
    }

    /**
     * Store a newly created resume in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        $resume = $user->resumes()->create([
            'title' => $validated['title'],
            'personal_info' => [
                'full_name' => $user->name,
                'email' => $user->email,
                'phone' => '',
                'city' => '',
                'linkedin' => '',
                'github' => '',
                'website' => '',
            ],
            'summary' => '',
            'additional_info' => '',
        ]);

        return redirect()->route('resumes.edit', $resume)
            ->with('success', 'Currículo criado com sucesso.');
    }

    /**
     * Display the specified resume in preview mode.
     */
    public function show(Resume $resume): Response
    {
        Gate::authorize('view', $resume);

        $resume->load([
            'experiences',
            'education',
            'courses',
            'certifications',
            'skills',
            'languages',
        ]);

        return Inertia::render('Resumes/Show', [
            'resume' => $resume,
        ]);
    }

    /**
     * Show the document editor for the specified resume.
     */
    public function edit(Resume $resume): Response
    {
        Gate::authorize('update', $resume);

        $resume->load([
            'experiences',
            'education',
            'courses',
            'certifications',
            'skills',
            'languages',
        ]);

        return Inertia::render('Resumes/Editor', [
            'resume' => $resume,
        ]);
    }

    /**
     * Update the specified resume in storage.
     */
    public function update(Request $request, Resume $resume): RedirectResponse
    {
        Gate::authorize('update', $resume);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'personal_info' => 'nullable|array',
            'summary' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'experiences' => 'nullable|array',
            'education' => 'nullable|array',
            'courses' => 'nullable|array',
            'certifications' => 'nullable|array',
            'skills' => 'nullable|array',
            'languages' => 'nullable|array',
        ]);

        DB::transaction(function () use ($resume, $validated) {
            $resume->update([
                'title' => $validated['title'],
                'personal_info' => $validated['personal_info'] ?? [],
                'summary' => $validated['summary'] ?? '',
                'additional_info' => $validated['additional_info'] ?? '',
            ]);

            // Sync experiences
            $resume->experiences()->delete();
            if (!empty($validated['experiences'])) {
                foreach ($validated['experiences'] as $index => $item) {
                    if (!empty($item['company']) || !empty($item['position'])) {
                        $resume->experiences()->create([
                            'company' => $item['company'] ?? '',
                            'position' => $item['position'] ?? '',
                            'location' => $item['location'] ?? null,
                            'start_date' => $item['start_date'] ?? null,
                            'end_date' => $item['end_date'] ?? null,
                            'is_current' => !empty($item['is_current']),
                            'description' => $item['description'] ?? '',
                            'order_index' => $index,
                        ]);
                    }
                }
            }

            // Sync education
            $resume->education()->delete();
            if (!empty($validated['education'])) {
                foreach ($validated['education'] as $index => $item) {
                    if (!empty($item['institution']) || !empty($item['course'])) {
                        $resume->education()->create([
                            'institution' => $item['institution'] ?? '',
                            'course' => $item['course'] ?? '',
                            'degree' => $item['degree'] ?? null,
                            'start_date' => $item['start_date'] ?? null,
                            'end_date' => $item['end_date'] ?? null,
                            'description' => $item['description'] ?? '',
                            'order_index' => $index,
                        ]);
                    }
                }
            }

            // Sync courses
            $resume->courses()->delete();
            if (!empty($validated['courses'])) {
                foreach ($validated['courses'] as $index => $item) {
                    if (!empty($item['name'])) {
                        $resume->courses()->create([
                            'name' => $item['name'],
                            'institution' => $item['institution'] ?? null,
                            'date' => $item['date'] ?? null,
                            'description' => $item['description'] ?? '',
                            'order_index' => $index,
                        ]);
                    }
                }
            }

            // Sync certifications
            $resume->certifications()->delete();
            if (!empty($validated['certifications'])) {
                foreach ($validated['certifications'] as $index => $item) {
                    if (!empty($item['name'])) {
                        $resume->certifications()->create([
                            'name' => $item['name'],
                            'institution' => $item['institution'] ?? null,
                            'date' => $item['date'] ?? null,
                            'expiration_date' => $item['expiration_date'] ?? null,
                            'code' => $item['code'] ?? null,
                            'url' => $item['url'] ?? null,
                            'order_index' => $index,
                        ]);
                    }
                }
            }

            // Sync skills
            $resume->skills()->delete();
            if (!empty($validated['skills'])) {
                foreach ($validated['skills'] as $index => $item) {
                    $name = is_array($item) ? ($item['name'] ?? '') : (string) $item;
                    if (trim($name) !== '') {
                        $resume->skills()->create([
                            'name' => trim($name),
                            'level' => is_array($item) ? ($item['level'] ?? null) : null,
                            'order_index' => $index,
                        ]);
                    }
                }
            }

            // Sync languages
            $resume->languages()->delete();
            if (!empty($validated['languages'])) {
                foreach ($validated['languages'] as $index => $item) {
                    if (!empty($item['language'])) {
                        $resume->languages()->create([
                            'language' => $item['language'],
                            'level' => $item['level'] ?? 'Básico',
                            'order_index' => $index,
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Currículo salvo com sucesso.');
    }

    /**
     * Duplicate the specified resume.
     */
    public function duplicate(Resume $resume): RedirectResponse
    {
        Gate::authorize('duplicate', $resume);

        $newResume = $resume->duplicate();

        return redirect()->route('resumes.index')
            ->with('success', "Currículo duplicado com sucesso: '{$newResume->title}'.");
    }

    /**
     * Remove the specified resume from storage.
     */
    public function destroy(Resume $resume): RedirectResponse
    {
        Gate::authorize('delete', $resume);

        $resume->delete();

        return redirect()->route('resumes.index')
            ->with('success', 'Currículo excluído com sucesso.');
    }

    /**
     * Export the resume to PDF.
     */
    public function pdf(Resume $resume)
    {
        Gate::authorize('view', $resume);

        $resume->load([
            'experiences',
            'education',
            'courses',
            'certifications',
            'skills',
            'languages',
        ]);

        $pdf = Pdf::loadView('pdf.resume', [
            'resume' => $resume,
        ])
        ->setPaper('a4')
        ->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        $safeTitle = \Illuminate\Support\Str::slug($resume->title ?: 'curriculo');

        return $pdf->stream("{$safeTitle}.pdf");
    }
}
