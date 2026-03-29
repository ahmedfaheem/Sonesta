<script setup>
import InputError from '@/Components/InputError.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import Input from '@/Components/ui/Input.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({
    layout: ManagerLayout,
});

const props = defineProps({
    managers: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const isAdmin = computed(() => (page.props.auth.user?.roles ?? []).includes('admin'));
const routePrefix = computed(() => (isAdmin.value ? 'admin' : 'manager'));

const form = useForm({
    name: '',
    manager_id: props.managers[0]?.id ?? '',
});

const submit = () => {
    form.post(route(`${routePrefix.value}.floors.store`));
};
</script>

<template>
    <Card class="max-w-3xl">
        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-xl font-semibold text-slate-950">Create floor</h2>
            <p class="mt-1 text-sm text-slate-500">Floor numbers are generated automatically and cannot be edited later.</p>
        </div>

        <form class="space-y-6 px-6 py-6" @submit.prevent="submit">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                <Input v-model="form.name" placeholder="Deluxe wing" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div v-if="isAdmin">
                <label class="mb-2 block text-sm font-medium text-slate-700">Manager</label>
                <select v-model="form.manager_id" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900">
                    <option value="" disabled>Select manager</option>
                    <option v-for="manager in managers" :key="manager.id" :value="manager.id">
                        {{ manager.name }}
                    </option>
                </select>
                <InputError :message="form.errors.manager_id" class="mt-2" />
            </div>

            <div class="rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-600">
                The floor number will be generated automatically when the record is saved.
            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">Create floor</Button>
                <Link :href="route(`${routePrefix}.floors.index`)">
                    <Button type="button" variant="secondary">Cancel</Button>
                </Link>
            </div>
        </form>
    </Card>
</template>
