<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <Head title="MeuHub - Seu Hub Pessoal Modular" />

    <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col justify-between selection:bg-slate-900 selection:text-white">
        <!-- Top Nav -->
        <header class="max-w-7xl w-full mx-auto px-6 py-6 flex items-center justify-between">
            <ApplicationLogo />

            <nav v-if="canLogin" class="flex items-center gap-3">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="px-4 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-xs"
                >
                    Ir para o Dashboard
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition"
                    >
                        Entrar
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-4 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-xs"
                    >
                        Criar Conta
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="max-w-4xl mx-auto px-6 py-12 sm:py-20 text-center space-y-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                <span>MeuHub MVP • Ferramenta de Currículos</span>
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight sm:leading-none">
                Crie e gerencie currículos com a facilidade de um documento.
            </h1>

            <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Um espaço pessoal moderno e especializado para criar, personalizar e exportar currículos profissionais em PDF diretamente para seus processos seletivos.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <Link
                    :href="$page.props.auth.user ? route('dashboard') : route('register')"
                    class="w-full sm:w-auto px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-medium text-sm rounded-xl shadow-sm hover:shadow transition"
                >
                    Começar agora gratuitamente
                </Link>

                <Link
                    v-if="!$page.props.auth.user"
                    :href="route('login')"
                    class="w-full sm:w-auto px-8 py-3.5 bg-white hover:bg-slate-50 text-slate-800 font-medium text-sm rounded-xl border border-slate-200 transition"
                >
                    Já tenho uma conta (Demo)
                </Link>
            </div>

            <!-- Features Highlights Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-12 text-left">
                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 text-base">Editor de Documento</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Edite seu currículo como um documento fluído e elegante, sem formulários cansativos.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="13" height="13" x="9" y="9" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 text-base">Múltiplas Versões</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Crie e duplique versões personalizadas com facilidade para cada vaga ou área de atuação.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 text-base">Exportação em PDF</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Geração instantânea em formato A4, limpo, elegante e pronto para processos seletivos.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="max-w-7xl w-full mx-auto px-6 py-6 border-t border-slate-200/60 text-center text-xs text-slate-400">
            MeuHub • Hub Pessoal Modular • Executando em ambiente Docker
        </footer>
    </div>
</template>
