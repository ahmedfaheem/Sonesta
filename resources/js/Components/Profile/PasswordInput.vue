<script setup>
import InputError from '@/Components/InputError.vue';
import Input from '@/Components/ui/Input.vue';
import Label from '@/Components/ui/Label.vue';
import { ref } from 'vue';

defineProps({
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    autocomplete: {
        type: String,
        default: 'current-password',
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
    type: String,
    default: '',
});

const visible = ref(false);
</script>

<template>
    <div class="space-y-2">
        <Label :for="id">{{ label }}</Label>
        <div class="relative">
            <Input
                :id="id"
                v-model="model"
                :type="visible ? 'text' : 'password'"
                :autocomplete="autocomplete"
                :placeholder="placeholder"
                :disabled="disabled"
                class="pr-14"
            />
            <button
                type="button"
                class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-500 transition hover:text-slate-900"
                @click="visible = !visible"
            >
                {{ visible ? 'Hide' : 'Show' }}
            </button>
        </div>
        <InputError :message="error" />
    </div>
</template>
