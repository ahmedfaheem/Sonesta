<script setup>
import Card from '@/Components/ui/Card.vue';
import ReceptionistLayout from '@/Layouts/ReceptionistLayout.vue';

defineOptions({
    layout: ReceptionistLayout,
});

defineProps({
    stats: {
        type: Object,
        required: true,
    },
});

const cards = [
    {
        key: 'pending_clients',
        label: 'Pending Clients',
        description: 'Clients waiting for receptionist approval.',
    },
    {
        key: 'approved_by_me',
        label: 'Approved By Me',
        description: 'Client accounts you have approved.',
    },
    {
        key: 'reservations',
        label: 'Reservations',
        description: 'All recorded reservations in the system.',
    },
];
</script>

<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Receptionist Dashboard</h1>
            <p class="mt-1 text-sm text-slate-600">Approve new clients quickly and monitor reservation activity from one place.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card v-for="card in cards" :key="card.key" class="p-6">
                <p class="text-sm font-medium text-slate-500">{{ card.label }}</p>
                <p class="mt-3 text-3xl font-semibold text-slate-950">{{ stats[card.key] ?? 0 }}</p>
                <p class="mt-3 text-sm text-slate-600">{{ card.description }}</p>
            </Card>
        </div>
    </div>
</template>
