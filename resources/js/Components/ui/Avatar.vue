<script setup>
import { computed } from 'vue';

const props = defineProps({
    src: {
        type: String,
        default: null,
    },
    alt: {
        type: String,
        default: '',
    },
    fallback: {
        type: String,
        default: '?',
    },
    size: {
        type: String,
        default: 'md',
    },
});

const sizeClasses = {
    sm: 'h-10 w-10 text-xs',
    md: 'h-12 w-12 text-sm',
    lg: 'h-20 w-20 text-lg',
    xl: 'h-28 w-28 text-2xl',
};

const initials = computed(() => props.fallback.trim().slice(0, 2).toUpperCase() || '?');
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100" :class="sizeClasses[size]">
        <img v-if="src" :src="src" :alt="alt" class="h-full w-full object-cover" />
        <div v-else class="flex h-full w-full items-center justify-center font-semibold text-slate-500">
            {{ initials }}
        </div>
    </div>
</template>
