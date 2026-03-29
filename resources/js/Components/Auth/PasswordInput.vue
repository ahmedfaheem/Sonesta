<script setup>
import InputError from '@/Components/InputError.vue';
import Input from '@/Components/ui/Input.vue';
import Label from '@/Components/ui/Label.vue';
import { computed, ref } from 'vue';

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
    error: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    autocomplete: {
        type: String,
        default: '',
    },
    showStrength: {
        type: Boolean,
        default: false,
    },
    strengthLabel: {
        type: String,
        default: '',
    },
    strengthTone: {
        type: String,
        default: 'bg-slate-200',
    },
    strengthPercentage: {
        type: Number,
        default: 0,
    },
    hint: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'blur']);
const visible = ref(false);

const type = computed(() => (visible.value ? 'text' : 'password'));
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <Label :for="id">{{ label }}</Label>
            <button type="button" class="text-xs font-medium text-slate-500 transition hover:text-slate-900" @click="visible = !visible">
                {{ visible ? 'Hide' : 'Show' }}
            </button>
        </div>

        <Input
            :id="id"
            :type="type"
            :model-value="modelValue"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            @update:model-value="emit('update:modelValue', $event)"
            @blur="emit('blur')"
        />

        <div v-if="showStrength" class="space-y-2">
            <div class="h-2 overflow-hidden rounded-full bg-slate-200">
                <div class="h-full rounded-full transition-all duration-300" :class="strengthTone" :style="{ width: `${strengthPercentage}%` }" />
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">{{ hint }}</span>
                <span class="font-semibold text-slate-700">{{ strengthLabel }}</span>
            </div>
        </div>

        <p v-else-if="hint && !error" class="text-xs text-slate-500">{{ hint }}</p>

        <InputError :message="error" />
    </div>
</template>
