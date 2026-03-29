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
    is_approved: false,
});

const submit = () => {
    form.post(route('admin.clients.store'));
};
</script>

<template>
    <UserForm
        title="Create client"
        description="Create a client account and set its approval state."
        :back-href="route('admin.clients.index')"
        back-label="Back to clients"
        submit-label="Create client"
        :form="form"
        show-approval
        @submit="submit"
    />
</template>
