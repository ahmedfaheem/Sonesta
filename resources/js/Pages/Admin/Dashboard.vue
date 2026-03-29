<script setup>
import StatsCard from '@/Components/Admin/StatsCard.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({
    layout: AdminLayout,
});

defineProps({
    stats: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-600">Overview of the hotel administration workspace.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <StatsCard label="Total Managers" :value="stats.managers" />
            <StatsCard label="Total Receptionists" :value="stats.receptionists" />
            <StatsCard label="Total Clients" :value="stats.clients" />
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <Card class="overflow-hidden border-0 shadow-sm">
                <div class="h-2 w-full bg-gradient-to-r from-sky-500 to-cyan-500" />

                <div class="space-y-5 p-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-950">Floors</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Open the shared manager floors module with full admin visibility across all managers.
                        </p>
                    </div>

                    <ul class="space-y-3 text-sm text-slate-700">
                        <li>View all floors across the hotel.</li>
                        <li>Create floors and assign them to managers.</li>
                        <li>Update ownership while keeping deletion rules enforced.</li>
                    </ul>

                    <div class="flex flex-wrap gap-3">
                        <Link :href="route('admin.floors.index')">
                            <Button>Open floors</Button>
                        </Link>
                        <Link :href="route('admin.floors.create')">
                            <Button variant="secondary">Create floor</Button>
                        </Link>
                    </div>
                </div>
            </Card>

            <Card class="overflow-hidden border-0 shadow-sm">
                <div class="h-2 w-full bg-gradient-to-r from-emerald-500 to-teal-500" />

                <div class="space-y-5 p-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-950">Rooms</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Access the shared room inventory screens and manage rooms for any manager from one place.
                        </p>
                    </div>

                    <ul class="space-y-3 text-sm text-slate-700">
                        <li>Review all rooms with pricing, capacity, and ownership.</li>
                        <li>Create rooms on any manager-owned floor.</li>
                        <li>Respect reservation locks when deleting rooms.</li>
                    </ul>

                    <div class="flex flex-wrap gap-3">
                        <Link :href="route('admin.rooms.index')">
                            <Button>Open rooms</Button>
                        </Link>
                        <Link :href="route('admin.rooms.create')">
                            <Button variant="secondary">Create room</Button>
                        </Link>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</template>
