<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

defineProps({ mustVerifyEmail: Boolean, status: String, tab: { type: String, default: 'perfil' }, google: { type: Object, required: true } });
</script>

<template>
    <Head title="Configurações" />
    <AuthenticatedLayout>
        <div class="max-w-6xl">
            <div class="border-b border-slate-200 pb-6">
                <nav class="mb-1 flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-slate-400"><span>Sistema</span><span>/</span><span class="text-slate-600">Configurações</span></nav>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Configurações</h1>
                <p class="mt-1 text-sm text-slate-500">Gerencie seu perfil, integrações e preferências.</p>
            </div>
            <nav class="mt-6 flex flex-wrap gap-2 border-b border-slate-200">
                <Link :href="route('profile.edit', { tab: 'perfil' })" :class="tab === 'perfil' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-800'" class="border-b-2 px-4 py-3 text-sm font-semibold">Conta</Link>
                <Link :href="route('profile.edit', { tab: 'integracoes' })" :class="tab === 'integracoes' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-800'" class="border-b-2 px-4 py-3 text-sm font-semibold">Integrações</Link>
                <Link :href="route('profile.edit', { tab: 'notificacoes' })" :class="tab === 'notificacoes' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-800'" class="border-b-2 px-4 py-3 text-sm font-semibold">Notificações</Link>
            </nav>
            <div v-if="tab === 'perfil'" class="mt-8 space-y-6">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8"><UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" /></section>
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8"><UpdatePasswordForm /></section>
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8"><DeleteUserForm /></section>
            </div>
            <section v-else-if="tab === 'integracoes'" class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between"><div class="flex gap-4"><div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-2xl font-bold text-indigo-600">G</div><div><h2 class="font-semibold text-slate-900">Google Agenda</h2><p class="mt-1 max-w-xl text-sm leading-6 text-slate-500">Sincronize eventos da sua agenda principal do Google com a agenda do MeuHub.</p><p v-if="google.connected && google.email" class="mt-2 text-xs text-slate-500">Conta conectada: <strong class="text-slate-800">{{ google.email }}</strong></p></div></div><span :class="google.connected ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'" class="rounded-full px-3 py-1 text-xs font-semibold">{{ google.connected ? 'Conectado' : 'Desconectado' }}</span></div>
                <div class="mt-6 flex flex-wrap gap-3"><a v-if="!google.connected && google.configured && google.enabled" :href="route('integrations.google.connect')" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">Conectar Google Agenda</a><span v-if="!google.configured" class="rounded-xl bg-amber-50 px-4 py-2.5 text-sm text-amber-800">Integração ainda não configurada.</span><span v-else-if="!google.enabled" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm text-slate-600">Integração desativada pelo administrador.</span><Link v-if="google.connected" :href="route('integrations.google.sync')" method="post" as="button" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white">Sincronizar agora</Link><Link v-if="google.connected" :href="route('integrations.google.disconnect')" method="delete" as="button" class="rounded-xl bg-rose-50 px-4 py-2.5 text-sm font-medium text-rose-700">Desconectar</Link></div>
            </section>
            <section v-else class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center"><div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 12h18" /></svg></div><h2 class="mt-4 font-semibold text-slate-800">Notificações</h2><p class="mt-1 text-sm text-slate-500">As preferências de notificações estarão disponíveis em breve.</p></section>
        </div>
    </AuthenticatedLayout>
</template>
