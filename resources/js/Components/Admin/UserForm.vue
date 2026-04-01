<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import CountrySelect from '@/Components/Auth/CountrySelect.vue';
import InputError from '@/Components/InputError.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import Input from '@/Components/ui/Input.vue';
import Label from '@/Components/ui/Label.vue';
import Avatar from '@/Components/ui/Avatar.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineEmits(['submit']);

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    backHref: {
        type: String,
        required: true,
    },
    backLabel: {
        type: String,
        required: true,
    },
    submitLabel: {
        type: String,
        required: true,
    },
    form: {
        type: Object,
        required: true,
    },
    showApproval: {
        type: Boolean,
        default: false,
    },
    currentAvatar: {
        type: String,
        default: null,
    },
    countries: {
        type: Array,
        default: () => [],
    },
});

const preview = computed(() => {
    if (props.form.avatar instanceof File) {
        return URL.createObjectURL(props.form.avatar);
    }

    return props.currentAvatar;
});
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ title }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ description }}</p>
            </div>

            <Link :href="backHref" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">
                {{ backLabel }}
            </Link>
        </div>

        <Card>
            <form class="grid gap-6 p-6 md:grid-cols-2" @submit.prevent="$emit('submit')">
                <div class="space-y-2">
                    <Label for="name">Name</Label>
                    <Input id="name" v-model="form.name" type="text" autofocus />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <Label for="password">Password</Label>
                    <Input id="password" v-model="form.password" type="password" />
                    <p class="text-xs text-slate-500">Leave blank on edit to keep the current password.</p>
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <Label for="national_id">National ID</Label>
                    <Input id="national_id" v-model="form.national_id" type="text" />
                    <InputError :message="form.errors.national_id" />
                </div>

                <div class="space-y-2">
                    <Label for="phone">Phone</Label>
                    <Input id="phone" v-model="form.phone" type="text" />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="space-y-2">
                    <CountrySelect
                        id="country"
                        label="Country"
                        v-model="form.country"
                        :countries="countries"
                        :error="form.errors.country"
                    />
                </div>

                <div class="space-y-2">
                    <Label for="gender">Gender</Label>
                    <select
                        id="gender"
                        v-model="form.gender"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                    >
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <InputError :message="form.errors.gender" />
                </div>

                <div class="space-y-2 md:col-span-2">
                    <Label for="avatar">Avatar</Label>
                    <div class="flex flex-col gap-4 rounded-2xl border border-dashed border-slate-200 p-4 sm:flex-row sm:items-center">
                        <Avatar :src="preview" :fallback="form.name || 'NA'" size="lg" :alt="form.name" />
                        <div class="flex-1">
                            <input
                                id="avatar"
                                type="file"
                                accept="image/jpeg,image/jpg"
                                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:font-medium file:text-white hover:file:bg-slate-700"
                                @input="form.avatar = $event.target.files[0]"
                            />
                            <p class="mt-2 text-xs text-slate-500">JPG or JPEG only. Run `php artisan storage:link` if avatars are not visible.</p>
                            <InputError :message="form.errors.avatar" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div v-if="showApproval" class="md:col-span-2">
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <Checkbox v-model:checked="form.is_approved" />
                        <div>
                            <p class="text-sm font-medium text-slate-900">Client approved</p>
                            <p class="text-xs text-slate-500">Approved clients can be identified directly from the admin panel.</p>
                        </div>
                    </label>
                    <InputError :message="form.errors.is_approved" class="mt-2" />
                </div>

                <div class="flex justify-end md:col-span-2">
                    <Button type="submit" :disabled="form.processing">
                        {{ submitLabel }}
                    </Button>
                </div>
            </form>
        </Card>
    </div>
</template>
