<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import Input from '@/Components/ui/Input.vue';
import Label from '@/Components/ui/Label.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({
    layout: GuestLayout,
});

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const alertMessage = computed(() => form.errors.email || form.errors.password || null);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <Card>
        <div class="space-y-6 p-8">
            <div class="space-y-2 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Welcome Back</p>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Sign in to your account</h1>
                <p class="text-sm text-slate-600">Use your email and password to access the hotel portal.</p>
            </div>

            <div v-if="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ status }}
            </div>

            <div v-if="alertMessage" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ alertMessage }}
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" autofocus autocomplete="username" placeholder="you@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <button type="button" class="text-xs font-medium text-slate-500 hover:text-slate-900" @click="showPassword = !showPassword">
                            {{ showPassword ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                    <Input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

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

                <Button type="submit" class="w-full" :disabled="form.processing">
                    {{ form.processing ? 'Signing in...' : 'Log in' }}
                </Button>
            </form>

            <p class="text-center text-sm text-slate-600">
                Don’t have an account?
                <Link :href="route('register')" class="font-medium text-slate-950 underline-offset-4 hover:underline">
                    Register here
                </Link>
            </p>
        </div>
    </Card>
</template>
