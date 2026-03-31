<script setup>
import UserForm from '@/Components/Admin/UserForm.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({
    layout: ManagerLayout,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    national_id: '',
    avatar: null,
});

const submit = () => {
    form.post(route('manager.receptionists.store'));
};
</script>

<template>
    <UserForm
        title="Create receptionist"
        description="Add a receptionist account to your managed front-desk team."
        :back-href="route('manager.receptionists.index')"
        back-label="Back to receptionists"
        submit-label="Create receptionist"
        :form="form"
        @submit="submit"
    />
</template>
