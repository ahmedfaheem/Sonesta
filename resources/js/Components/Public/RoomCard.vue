<script setup>
import Card from '@/Components/ui/Card.vue';
import Badge from '@/Components/ui/Badge.vue';

defineProps({
    room: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Card class="overflow-hidden transition duration-200 hover:-translate-y-1 hover:shadow-lg">
        <div class="h-36 bg-[linear-gradient(135deg,_#0f172a_0%,_#334155_100%)]" />
        <div class="space-y-4 p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Room Number</p>
                    <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">#{{ room.number }}</h3>
                </div>
                <Badge variant="success">Available</Badge>
            </div>

            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span class="text-sm text-slate-600">Capacity</span>
                <span class="text-sm font-semibold text-slate-950">{{ room.capacity }} guests</span>
            </div>

            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span class="text-sm text-slate-600">Price / night</span>
                <span class="text-sm font-semibold text-slate-950">${{ room.price }}</span>
            </div>
        </div>
    </Card>
</template>
