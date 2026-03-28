<script setup>
import StatsCard from '@/Components/Admin/StatsCard.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

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
    </div>
</template>
