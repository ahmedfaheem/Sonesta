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
    room: {
        type: Object,
        required: true,
    },
    floors: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const routePrefix = computed(() => ((page.props.auth.user?.roles ?? []).includes('admin') ? 'admin' : 'manager'));

const form = useForm({
    number: props.room.number,
    capacity: props.room.capacity,
    price: props.room.price_dollars,
    floor_id: props.room.floor_id,
});

const selectedFloor = computed(() => props.floors.find((floor) => floor.id === Number(form.floor_id)) ?? null);

const submit = () => {
    form.put(route(`${routePrefix.value}.rooms.update`, props.room.id));
};
</script>

<template>
    <Card class="max-w-3xl">
        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-xl font-semibold text-slate-950">Edit room</h2>
            <p class="mt-1 text-sm text-slate-500">Move rooms between available floors while keeping ownership aligned with the selected floor.</p>
        </div>

        <form class="space-y-6 px-6 py-6" @submit.prevent="submit">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Room number</label>
                <Input v-model="form.number" />
                <InputError :message="form.errors.number" class="mt-2" />
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Capacity</label>
                    <Input v-model="form.capacity" type="number" min="1" />
                    <InputError :message="form.errors.capacity" class="mt-2" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Price (USD)</label>
                    <Input v-model="form.price" type="number" min="0.01" step="0.01" />
                    <InputError :message="form.errors.price" class="mt-2" />
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Floor</label>
                <select v-model="form.floor_id" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900">
                    <option value="" disabled>Select floor</option>
                    <option v-for="floor in floors" :key="floor.id" :value="floor.id">
                        {{ floor.name }} (#{{ floor.number }})
                    </option>
                </select>
                <InputError :message="form.errors.floor_id" class="mt-2" />
            </div>

            <div v-if="selectedFloor" class="rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-600">
                This room will belong to floor #{{ selectedFloor.number }} after saving.
            </div>

            <InputError :message="form.errors.room" />

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">Save changes</Button>
                <Link :href="route(`${routePrefix}.rooms.index`)">
                    <Button type="button" variant="secondary">Cancel</Button>
                </Link>
            </div>
        </form>
    </Card>
</template>
