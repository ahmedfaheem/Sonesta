<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'default',
    },
    size: {
        type: String,
        default: 'default',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const variants = {
    default: 'bg-slate-900 text-white hover:bg-slate-700',
    secondary: 'bg-white text-slate-900 border border-slate-200 hover:bg-slate-50',
    ghost: 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900',
    destructive: 'bg-red-600 text-white hover:bg-red-500',
};

const sizes = {
    default: 'h-10 px-4 py-2',
    sm: 'h-9 px-3 py-2 text-sm',
    lg: 'h-11 px-5 py-2.5',
    icon: 'h-10 w-10',
};
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        class="inline-flex items-center justify-center rounded-xl text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
        :class="[variants[variant], sizes[size]]"
    >
        <slot />
    </button>
</template>
