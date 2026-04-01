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
import axios from 'axios';
import { h, ref } from 'vue';

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

const tokenForm = useForm({
    name: '',
    abilities: '',
});

const tokenState = ref({
    plainTextToken: '',
    success: '',
    error: '',
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

const submitToken = async () => {
    tokenState.value = {
        plainTextToken: '',
        success: '',
        error: '',
    };

    tokenForm.clearErrors();
    tokenForm.processing = true;

    try {
        const abilities = tokenForm.abilities
            ? tokenForm.abilities
                .split(',')
                .map((ability) => ability.trim())
                .filter(Boolean)
            : ['*'];

        const response = await axios.post('/api/tokens', {
            name: tokenForm.name,
            abilities,
        });

        tokenState.value = {
            plainTextToken: response.data?.token ?? '',
            success: 'API token created. Copy it now, it will not be shown again.',
            error: '',
        };

        tokenForm.reset('name', 'abilities');
    } catch (error) {
        if (error.response?.status === 422) {
            tokenForm.setError(error.response.data?.errors ?? {});
        } else {
            tokenState.value.error = error.response?.data?.message ?? 'Failed to create token.';
        }
    } finally {
        tokenForm.processing = false;
    }
};

const copyToken = async () => {
    if (!tokenState.value.plainTextToken) {
        return;
    }

    await navigator.clipboard.writeText(tokenState.value.plainTextToken);
    tokenState.value.success = 'Token copied to clipboard.';
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

            <FormCard
                title="API Tokens"
                description="Create personal access tokens for API access."
            >
                <form class="space-y-5" @submit.prevent="submitToken">
                    <InputField
                        id="token_name"
                        v-model="tokenForm.name"
                        label="Token Name"
                        placeholder="e.g. dashboard-client"
                        :error="tokenForm.errors.name"
                        :disabled="tokenForm.processing"
                    />

                    <InputField
                        id="token_abilities"
                        v-model="tokenForm.abilities"
                        label="Abilities (optional)"
                        placeholder="rooms:read,analytics:read"
                        :error="tokenForm.errors.abilities || tokenForm.errors['abilities.0']"
                        :disabled="tokenForm.processing"
                    />

                    <p class="text-xs text-slate-500">
                        Leave abilities empty to grant full access (`*`). Use comma-separated values for custom abilities.
                    </p>

                    <div
                        v-if="tokenState.error"
                        class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                    >
                        {{ tokenState.error }}
                    </div>

                    <div
                        v-if="tokenState.success"
                        class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700"
                    >
                        {{ tokenState.success }}
                    </div>

                    <div v-if="tokenState.plainTextToken" class="space-y-2">
                        <InputField
                            id="generated_token"
                            :model-value="tokenState.plainTextToken"
                            label="Generated Token"
                            :disabled="true"
                        />
                        <div class="flex justify-end">
                            <Button type="button" variant="secondary" @click="copyToken">
                                Copy Token
                            </Button>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="tokenForm.processing">
                            {{ tokenForm.processing ? 'Creating...' : 'Create Token' }}
                        </Button>
                    </div>
                </form>
            </FormCard>
        </div>
    </div>
</template>
