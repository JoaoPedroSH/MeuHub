<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const showingMobileMenu = ref(false);
const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col md:flex-row">
        <!-- Desktop Sidebar -->
        <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-white border-r border-slate-200 z-30">
            <!-- Sidebar Header / Logo -->
            <div class="flex h-16 items-center px-6 border-b border-slate-100">
                <Link :href="route('dashboard')" class="flex items-center">
                    <ApplicationLogo />
                </Link>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 flex flex-col justify-between overflow-y-auto px-4 py-6">
                <nav class="space-y-6">
                    <!-- General Area -->
                    <div>
                        <div class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            Geral
                        </div>
                        <Link
                            :href="route('dashboard')"
                            :class="[
                                route().current('dashboard')
                                    ? 'bg-slate-900 text-white font-medium shadow-sm'
                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                                'group flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-all duration-150'
                            ]"
                        >
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="7" height="9" x="3" y="3" rx="1"/>
                                <rect width="7" height="5" x="14" y="3" rx="1"/>
                                <rect width="7" height="9" x="14" y="12" rx="1"/>
                                <rect width="7" height="5" x="3" y="16" rx="1"/>
                            </svg>
                            Dashboard
                        </Link>
                    </div>

                    <!-- Category: Profissional -->
                    <div>
                        <div class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            Profissional
                        </div>
                        <div class="space-y-1">
                            <Link
                                :href="route('resumes.index')"
                                :class="[
                                    route().current('resumes.*')
                                        ? 'bg-indigo-50 text-indigo-700 font-medium'
                                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                                    'group flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-all duration-150'
                                ]"
                            >
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                    <span>Currículos</span>
                                </div>
                                <span v-if="route().current('resumes.*')" class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            </Link>
                        </div>
                    </div>

                    <!-- Category: Configurações -->
                    <div>
                        <div class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            Sistema
                        </div>
                        <Link
                            :href="route('profile.edit')"
                            :class="[
                                route().current('profile.edit')
                                    ? 'bg-slate-100 text-slate-900 font-medium'
                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                                'group flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-all duration-150'
                            ]"
                        >
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            Configurações
                        </Link>
                    </div>
                </nav>

                <!-- Bottom User Card -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $page.props.auth.user.name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ $page.props.auth.user.email }}</p>
                        </div>
                    </div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                        title="Sair"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Mobile Top Navigation -->
        <header class="md:hidden bg-white border-b border-slate-200 sticky top-0 z-40">
            <div class="flex items-center justify-between px-4 h-16">
                <Link :href="route('dashboard')">
                    <ApplicationLogo />
                </Link>
                <button
                    @click="showingMobileMenu = !showingMobileMenu"
                    class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg"
                    aria-label="Menu"
                >
                    <svg v-if="!showingMobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div v-show="showingMobileMenu" class="border-t border-slate-200 px-4 py-3 space-y-2 bg-white shadow-lg">
                <Link
                    :href="route('dashboard')"
                    @click="showingMobileMenu = false"
                    :class="[
                        route().current('dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100',
                        'block px-3 py-2 rounded-md text-sm font-medium'
                    ]"
                >
                    Dashboard
                </Link>
                <Link
                    :href="route('resumes.index')"
                    @click="showingMobileMenu = false"
                    :class="[
                        route().current('resumes.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-700 hover:bg-slate-100',
                        'block px-3 py-2 rounded-md text-sm font-medium'
                    ]"
                >
                    Currículos
                </Link>
                <Link
                    :href="route('profile.edit')"
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Configurações
                </Link>
                <div class="pt-2 border-t border-slate-100">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="w-full text-left px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-md"
                    >
                        Sair da conta
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 md:pl-64 flex flex-col min-h-screen">
            <!-- Global Flash Banner -->
            <div v-if="page.props.flash?.success" class="bg-emerald-50 border-b border-emerald-200 px-4 py-3 sm:px-6">
                <div class="max-w-7xl mx-auto flex items-center justify-between text-emerald-800 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>{{ page.props.flash.success }}</span>
                    </div>
                </div>
            </div>

            <!-- Page Header Slot -->
            <header v-if="$slots.header" class="bg-white border-b border-slate-200 px-4 py-4 sm:px-8">
                <div class="max-w-7xl mx-auto">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 py-8 px-4 sm:px-8">
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
