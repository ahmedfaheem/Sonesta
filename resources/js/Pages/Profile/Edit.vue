<script setup>
import AvatarUploader from '@/Components/Profile/AvatarUploader.vue';
import FormCard from '@/Components/Profile/FormCard.vue';
import InputField from '@/Components/Profile/InputField.vue';
import PasswordInput from '@/Components/Profile/PasswordInput.vue';
import Button from '@/Components/ui/Button.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import ReceptionistLayout from '@/Layouts/ReceptionistLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { h } from 'vue';

defineOptions({
    layout: (h, page) => {
        const roles = page.props?.auth?.user?.roles ?? [];

        if (roles.includes('admin')) {
            return h(AdminLayout, [page]);
        }

        if (roles.includes('manager')) {
            return h(ManagerLayout, [page]);
        }

        if (roles.includes('receptionist')) {
            return h(ReceptionistLayout, [page]);
        }

        if (roles.includes('client')) {
            return h(ClientLayout, [page]);
        }

        return page;
    },
});

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const profileForm = useForm({
    name: props.user.name ?? '',
    email: props.user.email ?? '',
    avatar: null,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submitProfile = () => {
    profileForm.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('profile.update'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            profileForm.reset('avatar');
        },
    });
};

const submitPassword = () => {
    passwordForm.put(route('profile.password'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Profile Settings" />

    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Profile Settings</h1>
            <p class="mt-1 text-sm text-slate-600">Manage your account info, avatar, and password.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <FormCard
                title="Profile Information"
                description="Update your account identity details and avatar."
            >
                <form class="space-y-5" @submit.prevent="submitProfile">
                    <InputField
                        id="name"
                        v-model="profileForm.name"
                        label="Name"
                        placeholder="Your full name"
                        autocomplete="name"
                        :error="profileForm.errors.name"
                        :disabled="profileForm.processing"
                    />

                    <InputField
                        id="email"
                        v-model="profileForm.email"
                        label="Email"
                        type="email"
                        placeholder="you@example.com"
                        autocomplete="email"
                        :error="profileForm.errors.email"
                        :disabled="profileForm.processing"
                    />

                    <AvatarUploader
                        id="avatar"
                        v-model="profileForm.avatar"
                        :name="profileForm.name"
                        :current-avatar="user.avatar_url"
                        :error="profileForm.errors.avatar"
                        :disabled="profileForm.processing"
                    />

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="profileForm.processing">
                            {{ profileForm.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                    </div>
                </form>
            </FormCard>

            <FormCard
                title="Change Password"
                description="Use a strong password to keep your account secure."
            >
                <form class="space-y-5" @submit.prevent="submitPassword">
                    <PasswordInput
                        id="current_password"
                        v-model="passwordForm.current_password"
                        label="Current Password"
                        autocomplete="current-password"
                        :error="passwordForm.errors.current_password"
                        :disabled="passwordForm.processing"
                    />

                    <PasswordInput
                        id="password"
                        v-model="passwordForm.password"
                        label="New Password"
                        autocomplete="new-password"
                        :error="passwordForm.errors.password"
                        :disabled="passwordForm.processing"
                    />

                    <PasswordInput
                        id="password_confirmation"
                        v-model="passwordForm.password_confirmation"
                        label="Confirm New Password"
                        autocomplete="new-password"
                        :error="passwordForm.errors.password_confirmation"
                        :disabled="passwordForm.processing"
                    />

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="passwordForm.processing">
                            {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
                        </Button>
                    </div>
                </form>
            </FormCard>
        </div>
    </div>
</template>
