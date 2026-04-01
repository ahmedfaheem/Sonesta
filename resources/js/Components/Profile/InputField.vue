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
    autocomplete: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const model = defineModel({
    type: [String, Number],
    default: '',
});
</script>

<template>
    <div class="space-y-2">
        <Label :for="id">{{ label }}</Label>
        <Input
            :id="id"
            v-model="model"
            :type="type"
            :autocomplete="autocomplete"
            :placeholder="placeholder"
            :disabled="disabled"
        />
        <InputError :message="error" />
    </div>
</template>
