<script setup>
import InputError from '@/Components/InputError.vue';
import Label from '@/Components/ui/Label.vue';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    modelValue: {
        type: String,
        default: '',
    },
    countries: {
        type: Array,
        default: () => [],
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'blur']);

const open = ref(false);
const search = ref('');
const highlightedIndex = ref(0);

const filtered = computed(() => {
    if (!search.value) return props.countries;

    return props.countries.filter((country) =>
        country.name.toLowerCase().includes(search.value.toLowerCase()),
    );
});

watch(open, (value) => {
    if (value) {
        search.value = '';
        highlightedIndex.value = 0;
    }
});

const selectCountry = (name) => {
    emit('update:modelValue', name);
    emit('blur');
    open.value = false;
};

const onKeydown = (event) => {
    if (!open.value && ['ArrowDown', 'Enter', ' '].includes(event.key)) {
        event.preventDefault();
        open.value = true;
        return;
    }

    if (!open.value || !filtered.value.length) return;

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        highlightedIndex.value = Math.min(highlightedIndex.value + 1, filtered.value.length - 1);
    }

    if (event.key === 'ArrowUp') {
        event.preventDefault();
        highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0);
    }

    if (event.key === 'Enter') {
        event.preventDefault();
        selectCountry(filtered.value[highlightedIndex.value].name);
    }

    if (event.key === 'Escape') {
        open.value = false;
    }
};
</script>

<template>
    <div class="space-y-2">
        <Label :for="id">{{ label }}</Label>

        <div class="relative">
            <button
                :id="id"
                type="button"
                class="flex h-11 w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-slate-200"
                @click="open = !open"
                @keydown="onKeydown"
            >
                <span :class="modelValue ? 'text-slate-900' : 'text-slate-400'">
                    {{ modelValue || 'Select your country' }}
                </span>
                <span class="text-slate-400">⌄</span>
            </button>

            <div v-if="open" class="absolute z-20 mt-2 w-full rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search country..."
                    class="mb-2 flex h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none focus:border-slate-400"
                    @keydown="onKeydown"
                />

                <div class="max-h-60 overflow-y-auto">
                    <button
                        v-for="(country, index) in filtered"
                        :key="country.name"
                        type="button"
                        class="flex w-full rounded-xl px-3 py-2 text-left text-sm transition"
                        :class="index === highlightedIndex ? 'bg-slate-100 text-slate-950' : 'text-slate-600 hover:bg-slate-50'"
                        @mouseenter="highlightedIndex = index"
                        @click="selectCountry(country.name)"
                    >
                        {{ country.name }}
                    </button>

                    <p v-if="filtered.length === 0" class="px-3 py-4 text-sm text-slate-500">No countries found.</p>
                </div>
            </div>
        </div>

        <InputError :message="error" />
    </div>
</template>
