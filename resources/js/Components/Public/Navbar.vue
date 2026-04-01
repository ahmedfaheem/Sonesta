<script setup>
import Button from '@/Components/ui/Button.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
        default: false,
    },
    canRegister: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const links = [
    { label: 'Home', href: '#home' },
    { label: 'Features', href: '#features' },
    { label: 'About', href: '#about' },
];
</script>

<template>
    <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/85 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="#home" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-sm font-semibold text-white shadow-sm">
                    SO
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Sonesta</p>
                    <p class="text-lg font-semibold tracking-tight text-slate-950">Sonesta</p>
                </div>
            </a>

            <nav class="hidden items-center gap-8 md:flex">
                <Link
                    v-for="link in links"
                    :key="link.label"
                    :href="link.href"
                    class="text-sm font-medium text-slate-600 transition hover:text-slate-950"
                >
                    {{ link.label }}
                </Link>
            </nav>

            <div class="flex items-center gap-3">
                <Link v-if="isAuthenticated" :href="route('dashboard')">
                    <Button variant="secondary">Dashboard</Button>
                </Link>
                <Link v-else-if="canLogin" :href="route('login')">
                    <Button variant="secondary">Login</Button>
                </Link>
                <Link v-if="!isAuthenticated && canRegister" :href="route('register')">
                    <Button>Register</Button>
                </Link>
            </div>
        </div>
    </header>
</template>
