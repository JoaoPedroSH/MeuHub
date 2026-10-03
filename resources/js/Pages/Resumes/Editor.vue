<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    resume: {
        type: Object,
        required: true,
    },
});

// Setup reactive form state prefilled from database
const form = useForm({
    title: props.resume.title || 'Meu Currículo',
    personal_info: {
        full_name: props.resume.personal_info?.full_name || '',
        email: props.resume.personal_info?.email || '',
        phone: props.resume.personal_info?.phone || '',
        city: props.resume.personal_info?.city || '',
        linkedin: props.resume.personal_info?.linkedin || '',
        github: props.resume.personal_info?.github || '',
        website: props.resume.personal_info?.website || '',
    },
    summary: props.resume.summary || '',
    additional_info: props.resume.additional_info || '',
    experiences: props.resume.experiences?.map(exp => ({ ...exp })) || [],
    education: props.resume.education?.map(edu => ({ ...edu })) || [],
    courses: props.resume.courses?.map(crs => ({ ...crs })) || [],
    certifications: props.resume.certifications?.map(crt => ({ ...crt })) || [],
    skills: props.resume.skills?.map(sk => (typeof sk === 'string' ? { name: sk } : { ...sk })) || [],
    languages: props.resume.languages?.map(lg => ({ ...lg })) || [],
});

const isDirty = ref(false);
const saveStatus = ref('saved'); // 'saved', 'unsaved', 'saving'
const newSkillInput = ref('');

// Watch for form changes to indicate dirty state
watch(
    () => [
        form.title,
        form.personal_info,
        form.summary,
        form.additional_info,
        form.experiences,
        form.education,
        form.courses,
        form.certifications,
        form.skills,
        form.languages,
    ],
    () => {
        isDirty.value = true;
        saveStatus.value = 'unsaved';
    },
    { deep: true }
);

// Save document function
const saveResume = () => {
    saveStatus.value = 'saving';
    form.put(route('resumes.update', props.resume.id), {
        preserveScroll: true,
        onSuccess: () => {
            isDirty.value = false;
            saveStatus.value = 'saved';
        },
        onError: () => {
            saveStatus.value = 'error';
        },
    });
};

// --- Experiences Helpers ---
const addExperience = () => {
    form.experiences.push({
        company: '',
        position: '',
        location: '',
        start_date: '',
        end_date: '',
        is_current: false,
        description: '',
    });
};

const removeExperience = (index) => {
    form.experiences.splice(index, 1);
};

const moveExperience = (index, direction) => {
    const targetIndex = index + direction;
    if (targetIndex < 0 || targetIndex >= form.experiences.length) return;
    const item = form.experiences.splice(index, 1)[0];
    form.experiences.splice(targetIndex, 0, item);
};

// --- Education Helpers ---
const addEducation = () => {
    form.education.push({
        institution: '',
        course: '',
        degree: '',
        start_date: '',
        end_date: '',
        description: '',
    });
};

const removeEducation = (index) => {
    form.education.splice(index, 1);
};

const moveEducation = (index, direction) => {
    const targetIndex = index + direction;
    if (targetIndex < 0 || targetIndex >= form.education.length) return;
    const item = form.education.splice(index, 1)[0];
    form.education.splice(targetIndex, 0, item);
};

// --- Courses Helpers ---
const addCourse = () => {
    form.courses.push({
        name: '',
        institution: '',
        date: '',
        description: '',
    });
};

const removeCourse = (index) => {
    form.courses.splice(index, 1);
};

// --- Certifications Helpers ---
const addCertification = () => {
    form.certifications.push({
        name: '',
        institution: '',
        date: '',
        expiration_date: '',
        code: '',
        url: '',
    });
};

const removeCertification = (index) => {
    form.certifications.splice(index, 1);
};

// --- Skills Helpers ---
const addSkill = () => {
    const val = newSkillInput.value.trim();
    if (!val) return;
    form.skills.push({ name: val, level: '' });
    newSkillInput.value = '';
};

const removeSkill = (index) => {
    form.skills.splice(index, 1);
};

// --- Languages Helpers ---
const addLanguage = () => {
    form.languages.push({
        language: '',
        level: 'Intermediário',
    });
};

const removeLanguage = (index) => {
    form.languages.splice(index, 1);
};

const languageLevels = [
    'Nativo',
    'Fluente',
    'Avançado',
    'Intermediário',
    'Básico',
];
</script>

<template>
    <Head :title="`Editor - ${form.title}`" />

    <div class="min-h-screen bg-slate-100/70 text-slate-800 flex flex-col">
        <!-- Sticky Editor Toolbar -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 px-4 sm:px-8 py-3">
            <div class="max-w-6xl mx-auto flex items-center justify-between gap-4">
                <!-- Left: Back & Title -->
                <div class="flex items-center gap-3 min-w-0">
                    <Link
                        :href="route('resumes.index')"
                        class="p-2 -ml-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition shrink-0"
                        title="Voltar aos currículos"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"/>
                            <polyline points="12 19 5 12 12 5"/>
                        </svg>
                    </Link>

                    <div class="min-w-0">
                        <input
                            v-model="form.title"
                            type="text"
                            class="font-semibold text-base sm:text-lg text-slate-900 bg-transparent hover:bg-slate-50 focus:bg-white focus:ring-1 focus:ring-indigo-600 rounded-md px-2 py-0.5 border border-transparent hover:border-slate-300 transition truncate max-w-xs sm:max-w-md outline-none"
                            placeholder="Nome do Currículo"
                            title="Clique para editar o título deste currículo"
                        />
                    </div>
                </div>

                <!-- Right: Save Status & Actions -->
                <div class="flex items-center gap-3 shrink-0">
                    <!-- Status feedback -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs text-slate-500 font-medium mr-2">
                        <span
                            :class="[
                                saveStatus === 'saved' ? 'bg-emerald-500' : (saveStatus === 'saving' ? 'bg-amber-500 animate-pulse' : 'bg-slate-400'),
                                'w-2 h-2 rounded-full'
                            ]"
                        ></span>
                        <span v-if="saveStatus === 'saved'">Salvo</span>
                        <span v-else-if="saveStatus === 'saving'">Salvando...</span>
                        <span v-else>Alterações pendentes</span>
                    </div>

                    <a
                        :href="route('resumes.pdf', resume.id)"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
                        title="Baixar em PDF"
                    >
                        <svg class="w-4 h-4 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        <span class="hidden sm:inline">Exportar PDF</span>
                    </a>

                    <Link
                        :href="route('resumes.show', resume.id)"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
                    >
                        <svg class="w-4 h-4 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <span class="hidden sm:inline">Visualizar</span>
                    </Link>

                    <button
                        @click="saveResume"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl shadow-xs transition disabled:opacity-50"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Salvar
                    </button>
                </div>
            </div>
        </header>

        <!-- Document Workspace Area -->
        <main class="flex-1 py-8 px-4 sm:px-6">
            <!-- The A4 Sheet Simulator -->
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-12 space-y-10">
                
                <!-- SECTION 1: DADOS PESSOAIS -->
                <section class="border-b border-slate-100 pb-8">
                    <div class="mb-4">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nome Completo</label>
                        <input
                            v-model="form.personal_info.full_name"
                            type="text"
                            placeholder="Seu Nome Completo"
                            class="w-full text-2xl sm:text-3xl font-bold text-slate-900 placeholder:text-slate-300 border-0 border-b border-transparent hover:border-slate-200 focus:border-indigo-600 focus:ring-0 p-0 py-1 transition outline-none"
                        />
                    </div>

                    <!-- Contact and links grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400">E-mail</label>
                            <input
                                v-model="form.personal_info.email"
                                type="email"
                                placeholder="email@exemplo.com"
                                class="w-full text-sm text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                            />
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400">Telefone</label>
                            <input
                                v-model="form.personal_info.phone"
                                type="text"
                                placeholder="(11) 99999-9999"
                                class="w-full text-sm text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                            />
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400">Cidade / Estado</label>
                            <input
                                v-model="form.personal_info.city"
                                type="text"
                                placeholder="São Paulo, SP"
                                class="w-full text-sm text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                            />
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400">LinkedIn</label>
                            <input
                                v-model="form.personal_info.linkedin"
                                type="text"
                                placeholder="linkedin.com/in/usuario"
                                class="w-full text-sm text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                            />
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400">GitHub</label>
                            <input
                                v-model="form.personal_info.github"
                                type="text"
                                placeholder="github.com/usuario"
                                class="w-full text-sm text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                            />
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400">Portfólio / Site</label>
                            <input
                                v-model="form.personal_info.website"
                                type="text"
                                placeholder="https://meusite.com"
                                class="w-full text-sm text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                            />
                        </div>
                    </div>
                </section>

                <!-- SECTION 2: RESUMO PROFISSIONAL -->
                <section>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Resumo Profissional</h2>
                        <div class="h-px bg-slate-200 flex-1"></div>
                    </div>
                    <textarea
                        v-model="form.summary"
                        rows="3"
                        placeholder="Apresente um resumo claro e objetivo da sua trajetória, áreas de domínio e principais competências..."
                        class="w-full text-sm text-slate-700 rounded-xl border border-slate-200 p-3.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none transition leading-relaxed"
                    ></textarea>
                </section>

                <!-- SECTION 3: EXPERIÊNCIA PROFISSIONAL -->
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1 mr-4">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Experiência Profissional</h2>
                            <div class="h-px bg-slate-200 flex-1"></div>
                        </div>
                        <button
                            type="button"
                            @click="addExperience"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition"
                        >
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Adicionar experiência
                        </button>
                    </div>

                    <div v-if="form.experiences.length === 0" class="text-xs text-slate-400 italic py-2">
                        Nenhuma experiência cadastrada. Clique em "Adicionar experiência" acima.
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(exp, index) in form.experiences"
                            :key="index"
                            class="p-4 sm:p-5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition relative group"
                        >
                            <!-- Controls Top Right -->
                            <div class="flex items-center justify-end gap-1 mb-3">
                                <button
                                    type="button"
                                    @click="moveExperience(index, -1)"
                                    :disabled="index === 0"
                                    class="p-1 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded"
                                    title="Mover para cima"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="18 15 12 9 6 15"/>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    @click="moveExperience(index, 1)"
                                    :disabled="index === form.experiences.length - 1"
                                    class="p-1 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded"
                                    title="Mover para baixo"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    @click="removeExperience(index)"
                                    class="p-1 text-slate-400 hover:text-rose-600 rounded ml-1"
                                    title="Remover"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Cargo</label>
                                    <input
                                        v-model="exp.position"
                                        type="text"
                                        placeholder="Ex: Desenvolvedor Fullstack Sênior"
                                        class="w-full text-sm font-semibold text-slate-900 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Empresa</label>
                                    <input
                                        v-model="exp.company"
                                        type="text"
                                        placeholder="Ex: Tech Solutions"
                                        class="w-full text-sm text-slate-800 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Localização (opcional)</label>
                                    <input
                                        v-model="exp.location"
                                        type="text"
                                        placeholder="Ex: Remoto ou São Paulo, SP"
                                        class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Data de Início</label>
                                    <input
                                        v-model="exp.start_date"
                                        type="text"
                                        placeholder="Ex: Jan 2021 ou 2021"
                                        class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Data de Término</label>
                                    <div class="space-y-1">
                                        <input
                                            v-if="!exp.is_current"
                                            v-model="exp.end_date"
                                            type="text"
                                            placeholder="Ex: Dez 2023 ou 2023"
                                            class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                        />
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-slate-600 mt-1">
                                            <input
                                                v-model="exp.is_current"
                                                type="checkbox"
                                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <span>Trabalho atual</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="text-[11px] font-semibold text-slate-500">Descrição das realizações</label>
                                <textarea
                                    v-model="exp.description"
                                    rows="3"
                                    placeholder="Descreva as responsabilidades, projetos entregues, tecnologias utilizadas e impacto..."
                                    class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 p-3 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none leading-relaxed"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SECTION 4: FORMAÇÃO ACADÊMICA -->
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1 mr-4">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Formação Acadêmica</h2>
                            <div class="h-px bg-slate-200 flex-1"></div>
                        </div>
                        <button
                            type="button"
                            @click="addEducation"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition"
                        >
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Adicionar formação
                        </button>
                    </div>

                    <div v-if="form.education.length === 0" class="text-xs text-slate-400 italic py-2">
                        Nenhuma formação cadastrada. Clique em "Adicionar formação" acima.
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(edu, index) in form.education"
                            :key="index"
                            class="p-4 sm:p-5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition relative group"
                        >
                            <div class="flex items-center justify-end gap-1 mb-3">
                                <button
                                    type="button"
                                    @click="moveEducation(index, -1)"
                                    :disabled="index === 0"
                                    class="p-1 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded"
                                    title="Mover para cima"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="18 15 12 9 6 15"/>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    @click="moveEducation(index, 1)"
                                    :disabled="index === form.education.length - 1"
                                    class="p-1 text-slate-400 hover:text-slate-700 disabled:opacity-30 rounded"
                                    title="Mover para baixo"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    @click="removeEducation(index)"
                                    class="p-1 text-slate-400 hover:text-rose-600 rounded ml-1"
                                    title="Remover"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Curso / Graduação</label>
                                    <input
                                        v-model="edu.course"
                                        type="text"
                                        placeholder="Ex: Engenharia de Software ou Administração"
                                        class="w-full text-sm font-semibold text-slate-900 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Instituição</label>
                                    <input
                                        v-model="edu.institution"
                                        type="text"
                                        placeholder="Ex: Universidade de São Paulo"
                                        class="w-full text-sm text-slate-800 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Grau / Tipo</label>
                                    <input
                                        v-model="edu.degree"
                                        type="text"
                                        placeholder="Ex: Bacharelado, Pós-graduação..."
                                        class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Ano de Início</label>
                                    <input
                                        v-model="edu.start_date"
                                        type="text"
                                        placeholder="Ex: 2018"
                                        class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Ano de Término</label>
                                    <input
                                        v-model="edu.end_date"
                                        type="text"
                                        placeholder="Ex: 2022 ou Em andamento"
                                        class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-2 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SECTION 5: HABILIDADES -->
                <section>
                    <div class="flex items-center gap-3 mb-3">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Habilidades</h2>
                        <div class="h-px bg-slate-200 flex-1"></div>
                    </div>

                    <!-- Add Skill Form -->
                    <div class="flex gap-2 mb-4">
                        <input
                            v-model="newSkillInput"
                            @keydown.enter.prevent="addSkill"
                            type="text"
                            placeholder="Digite uma habilidade e pressione Enter (ex: Vue.js, Laravel, Docker, PostgreSQL...)"
                            class="flex-1 text-sm rounded-xl border border-slate-200 px-3.5 py-2 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none bg-white"
                        />
                        <button
                            type="button"
                            @click="addSkill"
                            class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl shadow-xs transition"
                        >
                            Adicionar
                        </button>
                    </div>

                    <!-- Skills Badges List -->
                    <div class="flex flex-wrap gap-2">
                        <div
                            v-for="(skill, index) in form.skills"
                            :key="index"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-800 text-xs font-medium group"
                        >
                            <span>{{ skill.name }}</span>
                            <button
                                type="button"
                                @click="removeSkill(index)"
                                class="text-slate-400 hover:text-rose-600 transition"
                            >
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>
                        <span v-if="form.skills.length === 0" class="text-xs text-slate-400 italic">
                            Nenhuma habilidade adicionada ainda.
                        </span>
                    </div>
                </section>

                <!-- SECTION 6: IDIOMAS -->
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1 mr-4">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Idiomas</h2>
                            <div class="h-px bg-slate-200 flex-1"></div>
                        </div>
                        <button
                            type="button"
                            @click="addLanguage"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition"
                        >
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Adicionar idioma
                        </button>
                    </div>

                    <div v-if="form.languages.length === 0" class="text-xs text-slate-400 italic py-2">
                        Nenhum idioma cadastrado.
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div
                            v-for="(lang, index) in form.languages"
                            :key="index"
                            class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50"
                        >
                            <input
                                v-model="lang.language"
                                type="text"
                                placeholder="Idioma (ex: Inglês)"
                                class="flex-1 text-xs font-medium text-slate-800 rounded-lg border border-slate-200 px-2.5 py-1.5 bg-white outline-none focus:border-indigo-600"
                            />
                            <select
                                v-model="lang.level"
                                class="text-xs text-slate-700 rounded-lg border border-slate-200 px-2.5 py-1.5 bg-white outline-none focus:border-indigo-600"
                            >
                                <option v-for="lvl in languageLevels" :key="lvl" :value="lvl">{{ lvl }}</option>
                            </select>
                            <button
                                type="button"
                                @click="removeLanguage(index)"
                                class="p-1.5 text-slate-400 hover:text-rose-600 transition"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>

                <!-- SECTION 7: CURSOS & CERTIFICAÇÕES -->
                <section>
                    <!-- Cursos -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1 mr-4">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Cursos</h2>
                            <div class="h-px bg-slate-200 flex-1"></div>
                        </div>
                        <button
                            type="button"
                            @click="addCourse"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition"
                        >
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Adicionar curso
                        </button>
                    </div>

                    <div v-if="form.courses.length > 0" class="space-y-3 mb-8">
                        <div
                            v-for="(course, index) in form.courses"
                            :key="index"
                            class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <input
                                    v-model="course.name"
                                    type="text"
                                    placeholder="Nome do Curso"
                                    class="flex-1 text-sm font-semibold text-slate-900 rounded-lg border border-slate-200 px-3 py-1.5 bg-white outline-none focus:border-indigo-600"
                                />
                                <button
                                    type="button"
                                    @click="removeCourse(index)"
                                    class="p-1 text-slate-400 hover:text-rose-600"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <input
                                    v-model="course.institution"
                                    type="text"
                                    placeholder="Instituição (opcional)"
                                    class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 bg-white outline-none focus:border-indigo-600"
                                />
                                <input
                                    v-model="course.date"
                                    type="text"
                                    placeholder="Ano / Carga Horária (ex: 2023 - 40h)"
                                    class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 bg-white outline-none focus:border-indigo-600"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Certificações -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1 mr-4">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Certificações</h2>
                            <div class="h-px bg-slate-200 flex-1"></div>
                        </div>
                        <button
                            type="button"
                            @click="addCertification"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition"
                        >
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Adicionar certificação
                        </button>
                    </div>

                    <div v-if="form.certifications.length > 0" class="space-y-3">
                        <div
                            v-for="(cert, index) in form.certifications"
                            :key="index"
                            class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <input
                                    v-model="cert.name"
                                    type="text"
                                    placeholder="Nome da Certificação (ex: AWS Certified Solutions Architect)"
                                    class="flex-1 text-sm font-semibold text-slate-900 rounded-lg border border-slate-200 px-3 py-1.5 bg-white outline-none focus:border-indigo-600"
                                />
                                <button
                                    type="button"
                                    @click="removeCertification(index)"
                                    class="p-1 text-slate-400 hover:text-rose-600"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <input
                                    v-model="cert.institution"
                                    type="text"
                                    placeholder="Órgão emissor (ex: Amazon Web Services)"
                                    class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 bg-white outline-none focus:border-indigo-600"
                                />
                                <input
                                    v-model="cert.date"
                                    type="text"
                                    placeholder="Data de emissão (ex: 2023)"
                                    class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 bg-white outline-none focus:border-indigo-600"
                                />
                                <input
                                    v-model="cert.code"
                                    type="text"
                                    placeholder="Código da credencial (opcional)"
                                    class="w-full text-xs text-slate-700 rounded-lg border border-slate-200 px-3 py-1.5 bg-white outline-none focus:border-indigo-600"
                                />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SECTION 8: INFORMAÇÕES ADICIONAIS -->
                <section>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Informações Adicionais</h2>
                        <div class="h-px bg-slate-200 flex-1"></div>
                    </div>
                    <textarea
                        v-model="form.additional_info"
                        rows="3"
                        placeholder="Trabalhos voluntários, projetos open-source, disponibilidade para viagens/mudança, prêmios ou outras informações relevantes..."
                        class="w-full text-sm text-slate-700 rounded-xl border border-slate-200 p-3.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none transition leading-relaxed"
                    ></textarea>
                </section>

                <!-- Bottom Document Save Bar -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400">
                        {{ isDirty ? 'Você tem alterações não salvas.' : 'Todas as alterações estão salvas no banco.' }}
                    </span>
                    <button
                        @click="saveResume"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl shadow-xs transition"
                    >
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </main>
    </div>
</template>
