<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-slate-50 justify-center p-4">
        <div class="mb-6">
            <Link href="/">
                <ApplicationLogo />
            </Link>
        </div>

        <div class="w-full bg-white p-6 sm:p-8 shadow-sm border border-slate-200/80 sm:max-w-md rounded-2xl">
            <slot />
        </div>
    </div>
</template>
