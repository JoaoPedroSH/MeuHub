<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({ google: { type: Object, required: true } });
const toggleForm = useForm({ enabled: props.google.enabled });
const credentials = useForm({ client_id: props.google.client_id || '', client_secret: '', redirect_uri: props.google.redirect_uri || '' });
function toggle() { toggleForm.enabled = !toggleForm.enabled; toggleForm.patch(route('admin.integrations.google.toggle'), { preserveScroll: true }); }
function saveCredentials() { credentials.patch(route('admin.integrations.google.credentials'), { preserveScroll: true, onSuccess: () => credentials.reset('client_secret') }); }
</script>

<template>
    <Head title="Integrações — Administração" />
    <AuthenticatedLayout>
        <div class="max-w-5xl">
            <div class="border-b border-slate-200 pb-6"><nav class="mb-1 flex gap-2 text-xs font-medium uppercase tracking-wider text-slate-400"><Link :href="route('admin.dashboard')">Geral</Link><span>/</span><span>Administração</span><span>/</span><span class="text-slate-600">Integrações</span></nav><h1 class="text-2xl font-bold tracking-tight text-slate-900">Integrações</h1><p class="mt-1 text-sm text-slate-500">Configure credenciais e controle quais integrações ficam disponíveis.</p></div>
            <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between"><div class="flex gap-4"><div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-2xl font-bold text-indigo-600">G</div><div><h2 class="font-semibold text-slate-900">Google Agenda</h2><p class="mt-1 text-sm text-slate-500">Disponibiliza conexão e sincronização com a agenda do Google.</p><p class="mt-2 text-xs text-slate-400">{{ google.connected_users }} usuários conectados</p></div></div><span :class="google.enabled ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'" class="rounded-full px-3 py-1 text-xs font-semibold">{{ google.enabled ? 'Ativa' : 'Inativa' }}</span></div>
                <div class="mt-6 flex flex-wrap items-center gap-3"><button @click="toggle" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">{{ google.enabled ? 'Desativar integração' : 'Ativar integração' }}</button><span v-if="!google.configured" class="text-xs text-amber-700">Credenciais OAuth ainda não configuradas.</span><span v-else class="text-xs text-emerald-700">Credenciais OAuth configuradas.</span></div>
                <form @submit.prevent="saveCredentials" class="mt-6 space-y-4 border-t border-slate-100 pt-6"><div><h3 class="font-semibold text-slate-900">Credenciais OAuth</h3><p class="mt-1 text-xs leading-5 text-slate-500">O segredo é armazenado criptografado e nunca é exibido novamente. Deixe o campo vazio para manter o segredo atual.</p></div><div><label class="text-xs font-semibold text-slate-600">Client ID</label><input v-model="credentials.client_id" required class="mt-1 w-full rounded-xl border-slate-300 text-sm" placeholder="Seu Client ID do Google Cloud" /></div><div><label class="text-xs font-semibold text-slate-600">Client Secret <span v-if="google.has_secret" class="font-normal text-emerald-600">(já configurado)</span></label><input v-model="credentials.client_secret" type="password" class="mt-1 w-full rounded-xl border-slate-300 text-sm" placeholder="Informe somente para substituir" /></div><div><label class="text-xs font-semibold text-slate-600">URI de redirecionamento</label><input v-model="credentials.redirect_uri" type="url" class="mt-1 w-full rounded-xl border-slate-300 text-sm" placeholder="https://seu-dominio/configuracoes/integracoes/google/callback" /></div><div class="flex justify-end"><button :disabled="credentials.processing" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white disabled:opacity-60">Salvar credenciais</button></div></form>
                <p class="mt-4 text-xs leading-5 text-slate-400">Desativar impede novas conexões, mas não apaga conexões existentes nem eventos importados.</p>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
