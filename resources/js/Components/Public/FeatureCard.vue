<script setup>
import Card from '@/Components/ui/Card.vue';

defineProps({
    icon: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Card class="h-full border border-white/60 bg-white/85 transition duration-200 hover:-translate-y-1 hover:shadow-xl">
        <div class="p-6">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-2xl text-white">
                {{ icon }}
            </div>
            <h3 class="mt-5 text-xl font-semibold text-slate-950">{{ title }}</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">{{ description }}</p>
        </div>
    </Card>
</template>
