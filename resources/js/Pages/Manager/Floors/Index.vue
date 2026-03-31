<script setup>
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import Input from '@/Components/ui/Input.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

defineOptions({
    layout: ManagerLayout,
});

const props = defineProps({
    floors: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const routePrefix = computed(() => ((page.props.auth.user?.roles ?? []).includes('admin') ? 'admin' : 'manager'));
const isAdmin = computed(() => (page.props.auth.user?.roles ?? []).includes('admin'));
const authUserId = computed(() => page.props.auth.user?.id);
const canManageFloor = (floor) => isAdmin.value || Number(floor.manager_id) === Number(authUserId.value);

const form = reactive({
    search: props.filters.search ?? '',
    sort: props.filters.sort ?? 'created_at',
    direction: props.filters.direction ?? 'desc',
});

const reload = () => {
    router.get(route(`${routePrefix.value}.floors.index`), form, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const deleteFloor = (floor) => {
    if (!window.confirm(`Delete floor ${floor.name}?`)) {
        return;
    }

    router.delete(route(`${routePrefix.value}.floors.destroy`, floor.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Card class="overflow-hidden">
        <div
            v-if="$page.props.errors?.floor"
            class="mx-6 mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
        >
            {{ $page.props.errors.floor }}
        </div>

        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">Floors</h2>
                    <p class="mt-1 text-sm text-slate-500">Manage floor ownership and prevent deletion when rooms still exist.</p>
                </div>

                <Link :href="route(`${routePrefix}.floors.create`)">
                    <Button>Create floor</Button>
                </Link>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-3">
                <div class="md:col-span-1">
                    <label class="mb-2 block text-sm font-medium text-slate-700">Search</label>
                    <Input v-model="form.search" placeholder="Search by name or number" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Sort by</label>
                    <select v-model="form.sort" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900" @change="reload">
                        <option value="created_at">Created date</option>
                        <option value="name">Name</option>
                        <option value="number">Number</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Direction</label>
                    <select v-model="form.direction" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900" @change="reload">
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                <Button variant="secondary" @click="reload">Apply filters</Button>
                <Button
                    variant="ghost"
                    @click="Object.assign(form, { search: '', sort: 'created_at', direction: 'desc' }); reload();"
                >
                    Reset
                </Button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-left text-sm font-semibold text-slate-600">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Number</th>
                        <th v-if="isAdmin" class="px-6 py-4">Manager</th>
                        <th class="px-6 py-4">Rooms</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-sm text-slate-700">
                    <tr v-for="floor in floors.data" :key="floor.id">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ floor.name }}</td>
                        <td class="px-6 py-4">{{ floor.number }}</td>
                        <td v-if="isAdmin" class="px-6 py-4">{{ floor.manager_name }}</td>
                        <td class="px-6 py-4">{{ floor.rooms_count }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <Link v-if="canManageFloor(floor)" :href="route(`${routePrefix}.floors.edit`, floor.id)">
                                    <Button variant="secondary" size="sm">Edit</Button>
                                </Link>
                                <Button
                                    v-if="canManageFloor(floor)"
                                    variant="destructive"
                                    size="sm"
                                    @click="deleteFloor(floor)"
                                >
                                    Delete
                                </Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!floors.data.length">
                        <td colspan="5" class="px-6 py-10 text-center text-slate-500">No floors found for the current filters.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
            <Link
                v-for="link in floors.links"
                :key="`${link.label}-${link.url}`"
                :href="link.url || '#'"
                class="rounded-xl px-3 py-2 text-sm transition"
                :class="[
                    link.active ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700',
                    !link.url ? 'pointer-events-none opacity-50' : '',
                ]"
                v-html="link.label"
            />
        </div>
    </Card>
</template>
