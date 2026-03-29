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
    floor: {
        type: Object,
        required: true,
    },
    managers: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const isAdmin = computed(() => (page.props.auth.user?.roles ?? []).includes('admin'));
const routePrefix = computed(() => (isAdmin.value ? 'admin' : 'manager'));

const form = useForm({
    name: props.floor.name,
    manager_id: props.floor.manager_id,
});

const submit = () => {
    form.put(route(`${routePrefix.value}.floors.update`, props.floor.id));
};
</script>

<template>
    <Card class="max-w-3xl">
        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-xl font-semibold text-slate-950">Edit floor</h2>
            <p class="mt-1 text-sm text-slate-500">Update floor details while keeping the generated floor number locked.</p>
        </div>

        <form class="space-y-6 px-6 py-6" @submit.prevent="submit">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                <Input v-model="form.name" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Floor number</label>
                <input
                    :value="floor.number"
                    disabled
                    class="flex h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500"
                />
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

            <InputError :message="form.errors.floor" />

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">Save changes</Button>
                <Link :href="route(`${routePrefix}.floors.index`)">
                    <Button type="button" variant="secondary">Cancel</Button>
                </Link>
            </div>
        </form>
    </Card>
</template>
