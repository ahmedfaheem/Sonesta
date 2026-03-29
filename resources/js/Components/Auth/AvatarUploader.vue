<script setup>
import Avatar from '@/Components/ui/Avatar.vue';
import InputError from '@/Components/InputError.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [File, Object, null],
        default: null,
    },
    error: {
        type: String,
        default: '',
    },
    name: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);
const dragActive = ref(false);

const preview = computed(() => {
    if (props.modelValue instanceof File) {
        return URL.createObjectURL(props.modelValue);
    }

    return null;
});

const applyFile = (files) => {
    const [file] = files || [];

    if (file) {
        emit('update:modelValue', file);
    }
};
</script>

<template>
    <div class="space-y-2">
        <div
            class="rounded-3xl border border-dashed p-4 transition"
            :class="dragActive ? 'border-slate-900 bg-slate-50' : 'border-slate-200 bg-white'"
            @dragover.prevent="dragActive = true"
            @dragleave.prevent="dragActive = false"
            @drop.prevent="
                dragActive = false;
                applyFile($event.dataTransfer?.files);
            "
        >
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <Avatar :src="preview" :fallback="name || 'NA'" size="lg" :alt="name" />
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-900">Profile avatar</p>
                    <p class="mt-1 text-sm text-slate-500">Drag and drop a JPG/JPEG image here, or browse from your device.</p>
                    <label class="mt-4 inline-flex cursor-pointer items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                        Choose file
                        <input
                            type="file"
                            accept="image/jpeg,image/jpg"
                            class="hidden"
                            @input="applyFile($event.target.files)"
                        />
                    </label>
                </div>
            </div>
        </div>
        <InputError :message="error" />
    </div>
</template>
