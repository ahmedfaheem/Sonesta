<script setup>
import UserForm from '@/Components/Admin/UserForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({
    layout: AdminLayout,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    national_id: '',
    avatar: null,
});

const submit = () => {
    form.post(route('admin.managers.store'));
};
</script>

<template>
    <UserForm
        title="Create manager"
        description="Register a new manager account for hotel operations."
        :back-href="route('admin.managers.index')"
        back-label="Back to managers"
        submit-label="Create manager"
        :form="form"
        @submit="submit"
    />
</template>
