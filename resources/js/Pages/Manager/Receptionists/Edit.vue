<script setup>
import UserForm from '@/Components/Admin/UserForm.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({
    layout: ManagerLayout,
});

const props = defineProps({
    receptionist: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.receptionist.name ?? '',
    email: props.receptionist.email ?? '',
    password: '',
    national_id: props.receptionist.national_id ?? '',
    avatar: null,
});

const submit = () => {
    form.put(route('manager.receptionists.update', props.receptionist.id));
};
</script>

<template>
    <UserForm
        title="Edit receptionist"
        description="Update receptionist account details."
        :back-href="route('manager.receptionists.index')"
        back-label="Back to receptionists"
        submit-label="Save changes"
        :form="form"
        :current-avatar="receptionist.avatar_url"
        @submit="submit"
    />
</template>
