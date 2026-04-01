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
    accent: {
        type: String,
        default: 'from-sky-500 to-cyan-500',
    },
    loading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Card class="overflow-hidden border-0 shadow-sm">
        <div class="h-2 w-full bg-gradient-to-r" :class="accent" />
        <div class="space-y-5 p-6">
            <div>
                <h2 class="text-2xl font-semibold text-slate-950">{{ title }}</h2>
                <p v-if="description" class="mt-2 text-sm leading-6 text-slate-600">
                    {{ description }}
                </p>
            </div>

            <div class="relative h-72">
                <div class="h-full w-full" :class="{ 'invisible': loading || !!error }">
                    <slot />
                </div>

                <div
                    v-if="loading"
                    class="absolute inset-0 flex items-center justify-center text-sm text-slate-500"
                >
                    Loading chart...
                </div>

                <div
                    v-else-if="error"
                    class="absolute inset-0 flex items-center justify-center text-sm text-red-600"
                >
                    {{ error }}
                </div>
            </div>
        </div>
    </Card>
</template>
