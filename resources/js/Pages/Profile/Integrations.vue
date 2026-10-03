<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({ google: { type: Object, required: true } });
const sync = useForm({});
</script>

<template>
    <Head title="Integrações" />
    <AuthenticatedLayout>
        <div class="max-w-4xl space-y-6">
            <div>
                <Link :href="route('profile.edit')" class="text-sm text-slate-500 hover:text-slate-900">← Configurações</Link>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">Integrações</h1>
                <p class="mt-1 text-sm text-slate-500">Conecte serviços externos ao MeuHub sem misturar essa configuração com o uso diário.</p>
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-2xl text-indigo-600">G</div>
                        <div><h2 class="font-semibold text-slate-900">Google Agenda</h2><p class="mt-1 max-w-xl text-sm leading-6 text-slate-500">Sincronize eventos da sua agenda principal do Google com a agenda do MeuHub. A conexão usa autorização segura do Google.</p></div>
                    </div>
                    <span v-if="google.connected" class="inline-flex w-fit items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Conectado</span>
                    <span v-else class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Desconectado</span>
                </div>

                <div v-if="google.connected" class="mt-6 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                    <p v-if="google.email">Conta conectada: <strong class="text-slate-800">{{ google.email }}</strong></p>
                    <p v-if="google.last_synced_at" class="mt-1 text-xs text-slate-500">Última sincronização: {{ google.last_synced_at }}</p>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a v-if="!google.connected && google.configured" :href="route('integrations.google.connect')" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">Conectar Google Agenda</a>
                    <span v-if="!google.configured" class="rounded-xl bg-amber-50 px-4 py-2.5 text-sm text-amber-800">Integração ainda não configurada pelo administrador</span>
                    <button v-if="google.connected" @click="sync.post(route('integrations.google.sync'))" :disabled="sync.processing" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white disabled:opacity-60">{{ sync.processing ? 'Sincronizando…' : 'Sincronizar agora' }}</button>
                    <Link v-if="google.connected" :href="route('integrations.google.disconnect')" method="delete" as="button" class="rounded-xl bg-rose-50 px-4 py-2.5 text-sm font-medium text-rose-700">Desconectar</Link>
                </div>
                <p class="mt-4 text-xs leading-5 text-slate-400">Eventos importados do Google aparecem na Agenda com a indicação de origem. Desconectar remove apenas os eventos importados; seus eventos locais permanecem.</p>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
