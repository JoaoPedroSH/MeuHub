<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    resumesCount: {
        type: Number,
        default: 0,
    },
    latestResume: {
        type: Object,
        default: null,
    },
});

const isCreateModalOpen = ref(false);
const form = useForm({
    title: '',
});

const openCreateModal = () => {
    form.title = props.resumesCount === 0 ? 'Currículo Principal' : '';
    isCreateModalOpen.value = true;
};

const submitCreate = () => {
    form.post(route('resumes.store'), {
        onSuccess: () => {
            isCreateModalOpen.value = false;
        },
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-4xl space-y-8">
            <!-- Greeting Header -->
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Olá, {{ $page.props.auth.user.name.split(' ')[0] }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Bem-vindo ao seu hub pessoal. Acesse suas ferramentas e gerencie seus documentos.
                </p>
            </div>

            <!-- Category: Profissional -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Profissional</span>
                    <div class="h-px bg-slate-200 flex-1"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tool Card: Currículos -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between">
                                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ resumesCount }} {{ resumesCount === 1 ? 'currículo' : 'currículos' }}
                                </span>
                            </div>

                            <h3 class="text-lg font-semibold text-slate-900">Currículos</h3>
                            <p class="text-sm text-slate-500 mt-1">
                                Crie, edite e exporte versões especializadas dos seus currículos em PDF.
                            </p>

                            <!-- Latest Resume preview info -->
                            <div v-if="latestResume" class="mt-5 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Última atualização</div>
                                <div class="mt-1 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-slate-800 truncate mr-2">{{ latestResume.title }}</span>
                                    <span class="text-xs text-slate-500 shrink-0">{{ latestResume.updated_at_formatted }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-xl shadow-sm transition"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Criar currículo
                            </button>

                            <Link
                                v-if="resumesCount > 0"
                                :href="route('resumes.index')"
                                class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition"
                            >
                                Ver todos
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Create Modal -->
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
                                v-model="form.title"
                                type="text"
                                placeholder="Ex: Currículo Principal, Desenvolvedor Fullstack..."
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none transition"
                                required
                                autofocus
                            />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600">{{ form.errors.title }}</p>
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
                                :disabled="form.processing"
                                class="px-4 py-2 text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-sm transition disabled:opacity-50"
                            >
                                Iniciar Edição
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
