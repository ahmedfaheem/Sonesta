<script setup>
import Card from '@/Components/ui/Card.vue';

defineProps({
    room: {
        type: Object,
        required: true,
    },
    imageUrl: {
        type: String,
        default: '',
    },
    badge: {
        type: String,
        default: 'Available',
    },
});
</script>

<template>
    <Card class="group overflow-hidden border border-white/40 bg-white/90 shadow-lg transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
        <div class="relative h-48 overflow-hidden">
            <img
                :src="imageUrl"
                alt="Room"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent" />
            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-900">
                {{ badge }}
            </span>
        </div>

        <div class="space-y-4 p-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Room Number</p>
                <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">#{{ room.number }}</h3>
            </div>

            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span class="text-sm text-slate-600">Capacity</span>
                <span class="text-sm font-semibold text-slate-950">{{ room.capacity }} guests</span>
            </div>

            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span class="text-sm text-slate-600">Price / night</span>
                <span class="text-sm font-semibold text-slate-950">${{ room.price_dollars }}</span>
            </div>
        </div>
    </Card>
</template>
