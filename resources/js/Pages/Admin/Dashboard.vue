<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({ stats: { type: Object, required: true }, google: { type: Object, required: true }, shortcuts: { type: Array, default: () => [] } });
const showPreferences = ref(false);
const form = useForm({ shortcuts: [...props.shortcuts] });
const options = [{ id: 'admin-users', label: 'Usuários', category: 'Administração' }, { id: 'admin-integrations', label: 'Integrações', category: 'Administração' }];
function save() { form.patch(route('admin.shortcuts'), { preserveScroll: true, preserveState: false, onSuccess: () => { showPreferences.value = false; } }); }
</script>

<template>
    <Head title="Painel Administrativo" />
    <AuthenticatedLayout>
        <div class="max-w-6xl">
            <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
                <div><nav class="mb-1 flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-slate-400"><span>Administração</span><span>/</span><span class="text-slate-600">Painel Administrativo</span></nav><h1 class="text-2xl font-bold tracking-tight text-slate-900">Painel Administrativo</h1><p class="mt-1 text-sm text-slate-500">Gerencie o MeuHub e suas configurações globais.</p></div>
                <button @click="showPreferences = true" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-200"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M5.6 18.4 7 17M17 7l1.4-1.4"/><circle cx="12" cy="12" r="4"/></svg>Personalizar atalhos</button>
            </div>
            <div v-if="!shortcuts.length" class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center"><p class="font-semibold text-slate-800">Nenhum atalho selecionado</p><button @click="showPreferences = true" class="mt-3 text-sm font-semibold text-indigo-600">Escolher atalhos</button></div>
            <div v-else class="mt-8 grid gap-6 md:grid-cols-2"><Link v-if="shortcuts.includes('admin-users')" :href="route('admin.users')" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md"><div class="flex items-start justify-between"><div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M2.5 20a5.5 5.5 0 0 1 11 0M10.5 20a5.5 5.5 0 0 1 11 0"/></svg></div><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600">{{ stats.users }}</span></div><h2 class="mt-5 font-semibold text-slate-900">Usuários</h2><p class="mt-1 text-sm text-slate-500">Contas e acessos administrativos.</p></Link><Link v-if="shortcuts.includes('admin-integrations')" :href="route('admin.integrations')" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md"><div class="flex items-start justify-between"><div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3v12M18 9v12M6 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM18 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 15c5 0 5-6 12-6"/></svg></div><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600">{{ google.connected_users }} conectados</span></div><h2 class="mt-5 font-semibold text-slate-900">Integrações</h2><p class="mt-1 text-sm text-slate-500">Disponibilidade dos serviços externos.</p></Link></div>
            <div v-if="showPreferences" class="fixed inset-0 z-50 flex items-center justify-center p-4"><div class="fixed inset-0 bg-slate-900/40" @click="showPreferences = false"></div><div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl"><h2 class="text-lg font-semibold text-slate-900">Atalhos administrativos</h2><p class="mt-1 text-sm text-slate-500">Escolha apenas menus administrativos.</p><div class="mt-5 space-y-3"><label v-for="option in options" :key="option.id" class="flex items-center justify-between rounded-xl border border-slate-200 p-3"><span><span class="block text-sm font-medium text-slate-800">{{ option.label }}</span><span class="text-xs text-slate-400">{{ option.category }}</span></span><input v-model="form.shortcuts" :value="option.id" type="checkbox" class="rounded border-slate-300 text-indigo-600" /></label></div><div class="mt-6 flex justify-end gap-2"><button @click="showPreferences = false" class="rounded-xl bg-slate-100 px-4 py-2 text-sm">Cancelar</button><button @click="save" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white">Salvar</button></div></div></div>
        </div>
    </AuthenticatedLayout>
</template>
