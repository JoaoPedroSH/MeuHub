<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    googleConfigured: {
        type: Boolean,
        default: false,
    },
    googleEnabled: {
        type: Boolean,
        default: false,
    },
});

const form = useForm({
    email: 'demo@meuhub.local',
    password: 'senha123',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const fillDemo = () => {
    form.email = 'demo@meuhub.local';
    form.password = 'senha123';
};
</script>

<template>
    <GuestLayout>
        <Head title="Entrar no MeuHub" />

        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900">Entrar na sua conta</h2>
            <p class="text-xs text-slate-500 mt-1">Acesse seus currículos e ferramentas do MeuHub.</p>
        </div>

        <!-- Demo credentials box -->
        <div class="mb-5 p-3.5 rounded-xl bg-indigo-50/80 border border-indigo-100 flex items-center justify-between text-xs text-indigo-900">
            <div>
                <p class="font-semibold">Conta de Demonstração:</p>
                <p class="text-indigo-700">demo@meuhub.local • senha123</p>
            </div>
            <button
                type="button"
                @click="fillDemo"
                class="px-2.5 py-1 bg-white hover:bg-indigo-100 font-semibold text-indigo-700 rounded-lg border border-indigo-200 transition"
            >
                Preencher
            </button>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-emerald-600">
            {{ status }}
        </div>

        <div v-if="$page.props.flash?.error" class="mb-4 text-sm font-medium text-rose-600">
            {{ $page.props.flash.error }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="E-mail" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Senha" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-xl"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-xs text-slate-600">Lembrar-me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-xs text-slate-600 hover:text-slate-900 underline"
                >
                    Esqueceu a senha?
                </Link>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-xs transition disabled:opacity-50"
                >
                    Entrar
                </button>
            </div>

            <div v-if="googleEnabled" class="flex items-center gap-3 py-2 text-xs text-slate-400"><span class="h-px flex-1 bg-slate-200"></span><span>ou</span><span class="h-px flex-1 bg-slate-200"></span></div>

            <a v-if="googleEnabled" :href="route('login.google')" class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"><span class="text-base font-bold text-blue-600">G</span>Continuar com o Google</a>

            <div class="pt-2 text-center text-xs text-slate-500">
                Não tem uma conta?
                <Link :href="route('register')" class="font-semibold text-indigo-600 hover:text-indigo-800">
                    Cadastre-se
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
