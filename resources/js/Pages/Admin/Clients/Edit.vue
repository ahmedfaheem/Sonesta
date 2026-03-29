<script setup>
import UserForm from '@/Components/Admin/UserForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    client: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.client.name ?? '',
    email: props.client.email ?? '',
    password: '',
    national_id: props.client.national_id ?? '',
    avatar: null,
    is_approved: !!props.client.is_approved,
});

const submit = () => {
    form.put(route('admin.clients.update', props.client.id));
};
</script>

<template>
    <UserForm
        title="Edit client"
        description="Update client profile information."
        :back-href="route('admin.clients.index')"
        back-label="Back to clients"
        submit-label="Save changes"
        :form="form"
        :current-avatar="client.avatar_url"
        show-approval
        @submit="submit"
    />
</template>
