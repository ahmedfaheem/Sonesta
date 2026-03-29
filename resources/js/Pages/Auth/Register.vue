<script setup>
import AuthCard from '@/Components/Auth/AuthCard.vue';
import AvatarUploader from '@/Components/Auth/AvatarUploader.vue';
import CountrySelect from '@/Components/Auth/CountrySelect.vue';
import InputField from '@/Components/Auth/InputField.vue';
import PasswordInput from '@/Components/Auth/PasswordInput.vue';
import Button from '@/Components/ui/Button.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { usePasswordStrength } from '@/Composables/usePasswordStrength';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

defineOptions({
    layout: GuestLayout,
});

const props = defineProps({
    countries: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: '',
    email: '',
    national_id: '',
    password: '',
    password_confirmation: '',
    avatar: null,
    country: '',
    gender: 'male',
});

const touched = reactive({
    name: false,
    email: false,
    national_id: false,
    password: false,
    password_confirmation: false,
    country: false,
    gender: false,
});

const markTouched = (field) => {
    touched[field] = true;
};

const validations = computed(() => ({
    name: form.name.trim().length >= 3,
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email),
    national_id: form.national_id.trim() === '' || form.national_id.trim().length >= 8,
    password: form.password.length >= 6,
    password_confirmation: form.password_confirmation !== '' && form.password_confirmation === form.password,
    country: form.country.trim() !== '',
    gender: ['male', 'female'].includes(form.gender),
}));

const allValid = computed(() => Object.values(validations.value).every(Boolean));
const { label: strengthLabel, percentage: strengthPercentage, tone: strengthTone } = usePasswordStrength(computed(() => form.password));

const submit = () => {
    Object.keys(touched).forEach((field) => {
        touched[field] = true;
    });

    if (!allValid.value) {
        return;
    }

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const fieldError = (field) => {
    if (form.errors[field]) return form.errors[field];
    if (!touched[field]) return '';

    const messages = {
        name: 'Enter at least 3 characters for your name.',
        email: 'Enter a valid email address.',
        national_id: 'National ID should be at least 8 characters if provided.',
        password: 'Password must be at least 6 characters.',
        password_confirmation: 'Passwords must match.',
        country: 'Please select a country.',
        gender: 'Please choose a gender.',
    };

    return validations.value[field] ? '' : messages[field];
};
</script>

<template>
    <Head title="Register" />

    <AuthCard
        eyebrow="Client Registration"
        title="Create your hotel account"
        description="Join the platform, complete your guest profile, and wait for admin approval before accessing booking features."
        :alert="Object.keys(form.errors).length ? 'Please review the highlighted fields before submitting.' : ''"
        alert-tone="error"
    >
        <form class="grid gap-5 md:grid-cols-2" @submit.prevent="submit">
            <div class="md:col-span-2">
                <InputField
                    id="name"
                    v-model="form.name"
                    label="Name"
                    placeholder="Your full name"
                    autocomplete="name"
                    :error="fieldError('name')"
                    @blur="markTouched('name')"
                    autofocus
                />
            </div>

            <InputField
                id="email"
                v-model="form.email"
                label="Email"
                type="email"
                placeholder="you@example.com"
                autocomplete="username"
                :error="fieldError('email')"
                @blur="markTouched('email')"
            />

            <CountrySelect
                id="country"
                v-model="form.country"
                label="Country"
                :countries="countries"
                :error="fieldError('country')"
                @blur="markTouched('country')"
            />

            <div class="md:col-span-2">
                <InputField
                    id="national_id"
                    v-model="form.national_id"
                    label="National ID"
                    placeholder="Enter your national ID"
                    :error="fieldError('national_id')"
                    hint="Optional, but recommended for profile verification."
                    @blur="markTouched('national_id')"
                />
            </div>

            <PasswordInput
                id="password"
                v-model="form.password"
                label="Password"
                autocomplete="new-password"
                placeholder="Create a secure password"
                :error="fieldError('password')"
                show-strength
                :strength-label="strengthLabel"
                :strength-percentage="strengthPercentage"
                :strength-tone="strengthTone"
                hint="Use letters, numbers, and symbols for a stronger password."
                @blur="markTouched('password')"
            />

            <PasswordInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Confirm Password"
                autocomplete="new-password"
                placeholder="Repeat your password"
                :error="fieldError('password_confirmation')"
                @blur="markTouched('password_confirmation')"
            />

            <div class="space-y-2 md:col-span-2">
                <p class="text-sm font-medium text-slate-700">Gender</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3 transition"
                        :class="form.gender === 'male' ? 'border-slate-900 bg-slate-50' : 'border-slate-200 bg-white hover:border-slate-300'"
                    >
                        <input v-model="form.gender" type="radio" value="male" class="sr-only" @change="markTouched('gender')" />
                        <span class="text-lg">♂</span>
                        <span class="text-sm font-medium text-slate-800">Male</span>
                    </label>
                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3 transition"
                        :class="form.gender === 'female' ? 'border-slate-900 bg-slate-50' : 'border-slate-200 bg-white hover:border-slate-300'"
                    >
                        <input v-model="form.gender" type="radio" value="female" class="sr-only" @change="markTouched('gender')" />
                        <span class="text-lg">♀</span>
                        <span class="text-sm font-medium text-slate-800">Female</span>
                    </label>
                </div>
                <p v-if="fieldError('gender')" class="text-sm text-red-600">{{ fieldError('gender') }}</p>
            </div>

            <div class="md:col-span-2">
                <AvatarUploader v-model="form.avatar" :error="form.errors.avatar" :name="form.name" />
            </div>

            <div class="md:col-span-2">
                <Button type="submit" class="w-full" :disabled="form.processing || !allValid">
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
                    {{ form.processing ? 'Creating account...' : 'Create account' }}
                </Button>
            </div>
        </form>

        <p class="text-center text-sm text-slate-600">
            Already registered?
            <Link :href="route('login')" class="font-medium text-slate-950 underline-offset-4 hover:underline">
                Sign in here
            </Link>
        </p>
    </AuthCard>
</template>
