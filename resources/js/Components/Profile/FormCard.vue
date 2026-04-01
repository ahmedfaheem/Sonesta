<script setup>
import Card from '@/Components/ui/Card.vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Card>
        <div class="space-y-6 p-6">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-slate-950">{{ title }}</h2>
                <p v-if="description" class="mt-1 text-sm text-slate-600">{{ description }}</p>
            </div>
            <slot />
        </div>
    </Card>
</template>
