<script setup>
import UserForm from '@/Components/Admin/UserForm.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({
    layout: ManagerLayout,
});

const props = defineProps({
    countries: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    national_id: '',
    phone: '',
    country: '',
    gender: '',
    avatar: null,
    is_approved: false,
});

const submit = () => {
    form.post(route('manager.clients.store'));
};
</script>

<template>
    <UserForm
        title="Create client"
        description="Create a client account and set its approval state."
        :back-href="route('manager.clients.index')"
        back-label="Back to clients"
        submit-label="Create client"
        :form="form"
        :countries="countries"
        show-approval
        @submit="submit"
    />
</template>

