<script setup>
import Card from '@/Components/ui/Card.vue';

defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [Number, String],
        required: true,
    },
});
</script>

<template>
    <Card>
        <div class="p-6">
            <p class="text-sm font-medium text-slate-500">{{ label }}</p>
            <p class="mt-3 text-4xl font-semibold tracking-tight text-slate-950">{{ value }}</p>
        </div>
    </Card>
</template>
