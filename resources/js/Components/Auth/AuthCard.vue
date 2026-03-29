<script setup>
import Card from '@/Components/ui/Card.vue';

defineProps({
    eyebrow: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    alert: {
        type: String,
        default: '',
    },
    alertTone: {
        type: String,
        default: 'default',
    },
});

const tones = {
    default: 'border-slate-200 bg-slate-50 text-slate-700',
    success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    error: 'border-red-200 bg-red-50 text-red-700',
};
</script>

<template>
    <Card class="overflow-hidden border-white/60 bg-white/90 shadow-xl backdrop-blur">
        <div class="space-y-6 p-8">
            <div class="space-y-2 text-center">
                <p v-if="eyebrow" class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">{{ eyebrow }}</p>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ title }}</h1>
                <p v-if="description" class="text-sm leading-7 text-slate-600">{{ description }}</p>
            </div>

            <div v-if="alert" class="rounded-2xl border px-4 py-3 text-sm" :class="tones[alertTone]">
                {{ alert }}
            </div>

            <slot />
        </div>
    </Card>
</template>
