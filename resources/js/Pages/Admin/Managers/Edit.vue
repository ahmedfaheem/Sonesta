<script setup>
import UserForm from '@/Components/Admin/UserForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    manager: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.manager.name ?? '',
    email: props.manager.email ?? '',
    password: '',
    national_id: props.manager.national_id ?? '',
    avatar: null,
});

const submit = () => {
    form.put(route('admin.managers.update', props.manager.id));
};
</script>

<template>
    <UserForm
        title="Edit manager"
        description="Update manager account details."
        :back-href="route('admin.managers.index')"
        back-label="Back to managers"
        submit-label="Save changes"
        :form="form"
        :current-avatar="manager.avatar_url"
        @submit="submit"
    />
</template>
