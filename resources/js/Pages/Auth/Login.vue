<script setup>
import AuthCard from '@/Components/Auth/AuthCard.vue';
import InputField from '@/Components/Auth/InputField.vue';
import PasswordInput from '@/Components/Auth/PasswordInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Button from '@/Components/ui/Button.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({
    layout: GuestLayout,
});

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const isValid = computed(() => form.email.trim() !== '' && form.password.trim() !== '');
const alertMessage = computed(() => props.status || form.errors.email || form.errors.password || '');
const alertTone = computed(() => (props.status ? 'success' : 'error'));

const submit = () => {
    if (!isValid.value) return;

    form.post(route('login'), {
        replace: true,
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthCard
        eyebrow="Welcome Back"
        title="Sign in to your workspace"
        description="Access hotel operations, guest profiles, and reservation workflows from one secure place."
        :alert="alertMessage"
        :alert-tone="alertTone"
    >
        <form class="space-y-5" @submit.prevent="submit">
            <InputField
                id="email"
                v-model="form.email"
                label="Email"
                type="email"
                autocomplete="username"
                placeholder="you@example.com"
                :error="form.errors.email"
                autofocus
            />

            <PasswordInput
                id="password"
                v-model="form.password"
                label="Password"
                autocomplete="current-password"
                placeholder="Enter your password"
                :error="form.errors.password"
            />

            <div class="flex items-center justify-between gap-4">
                <label class="flex items-center gap-3 text-sm text-slate-600">
                    <Checkbox v-model:checked="form.remember" />
                    <span>Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-slate-600 transition hover:text-slate-950"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="space-y-3">
                <Button type="submit" class="w-full" :disabled="form.processing || !isValid">
                    <svg
                        v-if="form.processing"
                        class="mr-2 h-4 w-4 animate-spin"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25" stroke-width="4" />
                        <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                    </svg>
                    {{ form.processing ? 'Signing in...' : 'Log in' }}
                </Button>
            </div>
        </form>

        <p class="text-center text-sm text-slate-600">
            Don’t have an account?
            <Link :href="route('register')" class="font-medium text-slate-950 underline-offset-4 hover:underline">
                Register here
            </Link>
        </p>
    </AuthCard>
</template>
