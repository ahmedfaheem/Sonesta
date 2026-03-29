<script setup>
import InputError from '@/Components/InputError.vue';
import Input from '@/Components/ui/Input.vue';
import Label from '@/Components/ui/Label.vue';

defineProps({
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    modelValue: {
        type: [String, Number],
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    hint: {
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
    autofocus: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'blur']);
</script>

<template>
    <div class="space-y-2">
        <Label :for="id">{{ label }}</Label>
        <Input
            :id="id"
            :type="type"
            :model-value="modelValue"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :autofocus="autofocus"
            @update:model-value="emit('update:modelValue', $event)"
            @blur="emit('blur')"
        />
        <p v-if="hint && !error" class="text-xs text-slate-500">{{ hint }}</p>
        <InputError :message="error" />
    </div>
</template>
