<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    resumes: {
        type: Array,
        default: () => [],
    },
});

const isCreateModalOpen = ref(false);
const resumeToDelete = ref(null);

const createForm = useForm({
    title: '',
});

const submitCreate = () => {
    createForm.post(route('resumes.store'), {
        onSuccess: () => {
            isCreateModalOpen.value = false;
            createForm.reset();
        },
    });
};

const duplicateResume = (id) => {
    router.post(route('resumes.duplicate', id));
};

const confirmDelete = (resume) => {
    resumeToDelete.value = resume;
};

const executeDelete = () => {
    if (!resumeToDelete.value) return;
    router.delete(route('resumes.destroy', resumeToDelete.value.id), {
        onSuccess: () => {
            resumeToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Meus Currículos - Profissional" />

    <AuthenticatedLayout>
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium uppercase tracking-wider mb-1">
                    <span>Profissional</span>
                    <span>/</span>
                    <span class="text-slate-600">Currículos</span>
                </nav>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Meus Currículos
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Gerencie, duplique e personalize suas diferentes versões profissionais.
                </p>
            </div>

            <button
                @click="isCreateModalOpen = true"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-xl shadow-sm transition"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Novo currículo
            </button>
        </div>

        <!-- Resumes Grid -->
        <div v-if="resumes.length > 0" class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="resume in resumes"
                :key="resume.id"
                class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md hover:border-slate-300 transition duration-200 flex flex-col justify-between"
            >
                <div>
                    <!-- Card Top -->
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                        </div>

                        <!-- Dropdown or quick actions -->
                        <div class="flex items-center gap-1">
                            <button
                                @click="duplicateResume(resume.id)"
                                class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition"
                                title="Duplicar currículo"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="13" height="13" x="9" y="9" rx="2" ry="2"/>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                </svg>
                            </button>
                            <button
                                @click="confirmDelete(resume)"
                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                title="Excluir currículo"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Title & Summary -->
                    <div class="mt-4">
                        <Link :href="route('resumes.edit', resume.id)" class="text-base font-semibold text-slate-900 hover:text-indigo-600 transition">
                            {{ resume.title }}
                        </Link>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                            {{ resume.summary || 'Nenhum resumo adicionado ainda.' }}
                        </p>
                    </div>

                    <!-- Tags / Counts -->
                    <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-50 border border-slate-100">
                            {{ resume.experiences_count }} exp.
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-50 border border-slate-100">
                            {{ resume.education_count }} formações
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-50 border border-slate-100">
                            {{ resume.skills_count }} habilidades
                        </span>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">
                        Atualizado {{ resume.updated_at_formatted }}
                    </span>

                    <div class="flex items-center gap-2">
                        <a
                            :href="route('resumes.pdf', resume.id)"
                            target="_blank"
                            class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition"
                            title="Exportar PDF"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                        </a>
                        <Link
                            :href="route('resumes.show', resume.id)"
                            class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition"
                            title="Visualizar"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </Link>
                        <Link
                            :href="route('resumes.edit', resume.id)"
                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition"
                        >
                            Editar
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="mt-12 text-center py-16 px-4 bg-white rounded-3xl border border-dashed border-slate-300 max-w-lg mx-auto">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center mb-4">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-900">Nenhum currículo cadastrado</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Crie seu primeiro currículo profissional para começar a gerenciar suas experiências e exportar em PDF.
            </p>
            <div class="mt-6">
                <button
                    @click="isCreateModalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-xl shadow-sm transition"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Criar meu primeiro currículo
                </button>
            </div>
        </div>

        <!-- Create Modal -->
        <div v-if="isCreateModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="isCreateModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-900">Novo Currículo</h3>
                        <button @click="isCreateModalOpen = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCreate" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Identificação do currículo
                            </label>
                            <input
                                v-model="createForm.title"
                                type="text"
                                placeholder="Ex: Currículo Principal, Desenvolvedor..."
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none transition"
                                required
                                autofocus
                            />
                            <p v-if="createForm.errors.title" class="mt-1 text-xs text-rose-600">{{ createForm.errors.title }}</p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button
                                type="button"
                                @click="isCreateModalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="createForm.processing"
                                class="px-4 py-2 text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-sm transition disabled:opacity-50"
                            >
                                Iniciar Edição
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="resumeToDelete" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="resumeToDelete = null"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-900">Excluir Currículo</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Tem certeza que deseja excluir o currículo <strong class="text-slate-800">{{ resumeToDelete.title }}</strong>? Essa ação não pode ser desfeita.
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="resumeToDelete = null"
                            class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            @click="executeDelete"
                            class="px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition"
                        >
                            Sim, excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
