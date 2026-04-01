<script setup>
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
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

const metrics = [
    { label: 'Role-aware workflows', value: '4' },
    { label: 'Smart room visibility', value: 'Live' },
    { label: 'Operational overview', value: '24/7' },
];
</script>

<template>
    <section id="home" class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(245,158,11,0.14),_transparent_30%)]" />
        <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-24">
            <div class="space-y-8">
                <Badge variant="warning">Sonesta Hospitality Platform</Badge>

                <div class="space-y-5">
                    <h1 class="max-w-3xl text-5xl font-semibold tracking-tight text-slate-950 sm:text-6xl">
                        Manage Sonesta Smartly &amp; Elegantly
                    </h1>
                    <p class="max-w-2xl text-lg leading-8 text-slate-600">
                        All-in-one hospitality workspace for managers, receptionists, and clients.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <Link v-if="isAuthenticated" :href="route('dashboard')">
                        <Button size="lg">Go to Dashboard</Button>
                    </Link>
                    <Link v-else-if="canRegister" :href="route('register')">
                        <Button size="lg">Get Started</Button>
                    </Link>
                    <Link v-if="!isAuthenticated && canLogin" :href="route('login')">
                        <Button variant="secondary" size="lg">Login</Button>
                    </Link>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <Card v-for="metric in metrics" :key="metric.label">
                        <div class="p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ metric.label }}</p>
                            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ metric.value }}</p>
                        </div>
                    </Card>
                </div>
            </div>

            <div class="relative">
                <Card class="overflow-hidden border-slate-900/10 shadow-xl">
                    <div class="bg-slate-950 p-5 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Dashboard Preview</p>
                                <p class="mt-2 text-2xl font-semibold">Operational Snapshot</p>
                            </div>
                            <div class="flex gap-2">
                                <span class="h-3 w-3 rounded-full bg-red-400" />
                                <span class="h-3 w-3 rounded-full bg-yellow-400" />
                                <span class="h-3 w-3 rounded-full bg-emerald-400" />
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 bg-white p-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-100 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Reservations</p>
                                <p class="mt-3 text-3xl font-semibold text-slate-950">128</p>
                                <p class="mt-2 text-sm text-emerald-600">+18% this week</p>
                            </div>
                            <div class="rounded-2xl bg-slate-100 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Occupied Rooms</p>
                                <p class="mt-3 text-3xl font-semibold text-slate-950">42</p>
                                <p class="mt-2 text-sm text-slate-600">Across all available floors</p>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-950">Team Access</p>
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Synced</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                    <span class="text-sm text-slate-600">Managers</span>
                                    <span class="text-sm font-semibold text-slate-950">Property Oversight</span>
                                </div>
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                    <span class="text-sm text-slate-600">Receptionists</span>
                                    <span class="text-sm font-semibold text-slate-950">Front Desk Control</span>
                                </div>
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                    <span class="text-sm text-slate-600">Clients</span>
                                    <span class="text-sm font-semibold text-slate-950">Bookings &amp; Profiles</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </section>
</template>
