<script setup>
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/ui/Button.vue';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Delete item',
    },
    description: {
        type: String,
        default: 'This action cannot be undone.',
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <Modal :show="show" max-width="md" @close="emit('close')">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-slate-900">{{ title }}</h3>
            <p class="mt-2 text-sm text-slate-600">{{ description }}</p>

            <div class="mt-6 flex justify-end gap-3">
                <Button variant="secondary" @click="emit('close')">Cancel</Button>
                <Button variant="destructive" :disabled="processing" @click="emit('confirm')">
                    Delete
                </Button>
            </div>
        </div>
    </Modal>
</template>
