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
    form.post(route('admin.receptionists.store'));
};
</script>

<template>
    <UserForm
        title="Create receptionist"
        description="Add a receptionist account to the front-desk team."
        :back-href="route('admin.receptionists.index')"
        back-label="Back to receptionists"
        submit-label="Create receptionist"
        :form="form"
        @submit="submit"
    />
</template>
