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

const heroImage =
    'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=80';
</script>

<template>
    <section id="home" class="relative overflow-hidden">
        <div class="absolute inset-0">
            <img :src="heroImage" alt="Luxury hotel" class="h-full w-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-r from-white/85 via-white/70 to-white/35" />
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.6),_transparent_55%)]" />
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
            <div class="max-w-3xl space-y-8 rounded-3xl border border-white/70 bg-white/80 p-8 shadow-2xl backdrop-blur sm:p-10">
                <p class="text-xs font-semibold uppercase tracking-[0.5em] text-amber-700">Sonesta Hotels</p>
                <h1 class="text-5xl font-semibold leading-tight tracking-tight text-slate-950 sm:text-6xl">
                    Find &amp; Manage Your Perfect Stay
                </h1>
                <p class="max-w-2xl text-lg leading-8 text-slate-700">
                    A premium booking experience with role-aware operations, secure payments, and live room availability.
                </p>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <Link v-if="canRegister" :href="route('register')">
                        <Button size="lg">Book Now</Button>
                    </Link>
                    <Link v-if="!isAuthenticated && canLogin" :href="route('login')">
                        <Button variant="secondary" size="lg">Get Started</Button>
                    </Link>
                    <Link v-if="isAuthenticated" :href="route('dashboard')">
                        <Button variant="secondary" size="lg">Go to Dashboard</Button>
                    </Link>
                </div>
                <div class="flex flex-wrap items-center gap-6 text-sm text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500" />
                        Live availability
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-amber-500" />
                        Secure checkout
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-sky-500" />
                        Concierge-ready
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
