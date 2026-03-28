<script setup>
import InputError from '@/Components/InputError.vue';
import Avatar from '@/Components/ui/Avatar.vue';
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
    countries: {
        type: Array,
        required: true,
    },
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    avatar: null,
    country: '',
    gender: 'male',
});

const avatarPreview = computed(() => {
    if (form.avatar instanceof File) {
        return URL.createObjectURL(form.avatar);
    }

    return null;
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <Card>
        <div class="space-y-6 p-8">
            <div class="space-y-2 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Client Registration</p>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Create your account</h1>
                <p class="text-sm text-slate-600">Register as a client and wait for account approval.</p>
            </div>

            <div v-if="Object.keys(form.errors).length" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Please correct the highlighted fields and try again.
            </div>

            <form class="grid gap-5 md:grid-cols-2" @submit.prevent="submit">
                <div class="space-y-2 md:col-span-2">
                    <Label for="name">Name</Label>
                    <Input id="name" v-model="form.name" type="text" autofocus autocomplete="name" placeholder="Your full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" autocomplete="username" placeholder="you@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <Label for="country">Country</Label>
                    <select
                        id="country"
                        v-model="form.country"
                        class="flex h-11 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200"
                    >
                        <option disabled value="">Select your country</option>
                        <option v-for="country in countries" :key="country.name" :value="country.name">
                            {{ country.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.country" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <button type="button" class="text-xs font-medium text-slate-500 hover:text-slate-900" @click="showPassword = !showPassword">
                            {{ showPassword ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                    <Input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label for="password_confirmation">Confirm Password</Label>
                        <button
                            type="button"
                            class="text-xs font-medium text-slate-500 hover:text-slate-900"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                        >
                            {{ showPasswordConfirmation ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        autocomplete="new-password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="space-y-2 md:col-span-2">
                    <Label>Gender</Label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3"
                            :class="form.gender === 'male' ? 'border-slate-900 bg-slate-50' : 'border-slate-200 bg-white'"
                        >
                            <input v-model="form.gender" type="radio" value="male" class="h-4 w-4 border-slate-300 text-slate-900 focus:ring-slate-300" />
                            <span class="text-sm font-medium text-slate-800">Male</span>
                        </label>
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3"
                            :class="form.gender === 'female' ? 'border-slate-900 bg-slate-50' : 'border-slate-200 bg-white'"
                        >
                            <input v-model="form.gender" type="radio" value="female" class="h-4 w-4 border-slate-300 text-slate-900 focus:ring-slate-300" />
                            <span class="text-sm font-medium text-slate-800">Female</span>
                        </label>
                    </div>
                    <InputError :message="form.errors.gender" />
                </div>

                <div class="space-y-2 md:col-span-2">
                    <Label for="avatar">Avatar</Label>
                    <div class="flex flex-col gap-4 rounded-2xl border border-dashed border-slate-200 p-4 sm:flex-row sm:items-center">
                        <Avatar :src="avatarPreview" :fallback="form.name || 'NA'" size="lg" :alt="form.name" />
                        <div class="flex-1">
                            <input
                                id="avatar"
                                type="file"
                                accept="image/jpeg,image/jpg"
                                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:font-medium file:text-white hover:file:bg-slate-700"
                                @input="form.avatar = $event.target.files[0]"
                            />
                            <p class="mt-2 text-xs text-slate-500">Optional. JPG or JPEG only. A default avatar placeholder is used when none is uploaded.</p>
                            <InputError :message="form.errors.avatar" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <Button type="submit" class="w-full" :disabled="form.processing">
                        {{ form.processing ? 'Creating account...' : 'Register' }}
                    </Button>
                </div>
            </form>

            <p class="text-center text-sm text-slate-600">
                Already registered?
                <Link :href="route('login')" class="font-medium text-slate-950 underline-offset-4 hover:underline">
                    Sign in here
                </Link>
            </p>
        </div>
    </Card>
</template>
