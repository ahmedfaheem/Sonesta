<script setup>
import Avatar from '@/Components/ui/Avatar.vue';
import InputError from '@/Components/InputError.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    id: {
        type: String,
        default: 'avatar',
    },
    currentAvatar: {
        type: String,
        default: null,
    },
    name: {
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
    type: [File, Object, null],
    default: null,
});

const dragActive = ref(false);

const preview = computed(() => {
    if (model.value instanceof File) {
        return URL.createObjectURL(model.value);
    }

    return props.currentAvatar;
});

const assignFile = (fileList) => {
    if (!fileList || !fileList.length) {
        return;
    }

    model.value = fileList[0];
};

const onDrop = (event) => {
    event.preventDefault();
    dragActive.value = false;
    assignFile(event.dataTransfer?.files);
};
</script>

<template>
    <div class="space-y-3">
        <div
            class="rounded-2xl border border-dashed p-4 transition"
            :class="dragActive ? 'border-slate-900 bg-slate-50' : 'border-slate-300 bg-white'"
            @dragenter.prevent="dragActive = true"
            @dragover.prevent="dragActive = true"
            @dragleave.prevent="dragActive = false"
            @drop="onDrop"
        >
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                <Avatar :src="preview" :fallback="name || 'NA'" size="lg" :alt="name" />
                <div class="w-full">
                    <input
                        :id="id"
                        type="file"
                        accept="image/jpeg,image/jpg"
                        class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:font-medium file:text-white hover:file:bg-slate-700 disabled:opacity-50"
                        :disabled="disabled"
                        @change="assignFile($event.target.files)"
                    />
                    <p class="mt-2 text-xs text-slate-500">Drop image here or browse. JPG/JPEG only.</p>
                </div>
            </div>
        </div>
        <InputError :message="error" />
    </div>
</template>
