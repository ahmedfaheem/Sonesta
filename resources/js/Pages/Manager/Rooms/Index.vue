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
    rooms: {
        type: Object,
        required: true,
    },
    floors: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const routePrefix = computed(() => ((page.props.auth.user?.roles ?? []).includes('admin') ? 'admin' : 'manager'));

const form = reactive({
    search: props.filters.search ?? '',
    floor_id: props.filters.floor_id ?? '',
    sort: props.filters.sort ?? 'created_at',
    direction: props.filters.direction ?? 'desc',
});

const reload = () => {
    router.get(route(`${routePrefix.value}.rooms.index`), form, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const deleteRoom = (room) => {
    if (!window.confirm(`Delete room ${room.number}?`)) {
        return;
    }

    router.delete(route(`${routePrefix.value}.rooms.destroy`, room.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Card class="overflow-hidden">
        <div
            v-if="$page.props.errors?.room"
            class="mx-6 mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
        >
            {{ $page.props.errors.room }}
        </div>

        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">Rooms</h2>
                    <p class="mt-1 text-sm text-slate-500">Track capacity, pricing in dollars, and reservation-safe deletion.</p>
                </div>

                <Link :href="route(`${routePrefix}.rooms.create`)">
                    <Button>Create room</Button>
                </Link>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Search</label>
                    <Input v-model="form.search" placeholder="Room or floor" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Floor</label>
                    <select v-model="form.floor_id" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900" @change="reload">
                        <option value="">All floors</option>
                        <option v-for="floor in floors" :key="floor.id" :value="floor.id">
                            {{ floor.name }} (#{{ floor.number }})
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Sort by</label>
                    <select v-model="form.sort" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900" @change="reload">
                        <option value="created_at">Created date</option>
                        <option value="number">Room number</option>
                        <option value="capacity">Capacity</option>
                        <option value="price">Price</option>
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
                    @click="Object.assign(form, { search: '', floor_id: '', sort: 'created_at', direction: 'desc' }); reload();"
                >
                    Reset
                </Button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-left text-sm font-semibold text-slate-600">
                        <th class="px-6 py-4">Room</th>
                        <th class="px-6 py-4">Floor</th>
                        <th class="px-6 py-4">Manager</th>
                        <th class="px-6 py-4">Capacity</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Reservations</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-sm text-slate-700">
                    <tr v-for="room in rooms.data" :key="room.id">
                        <td class="px-6 py-4 font-medium text-slate-900">#{{ room.number }}</td>
                        <td class="px-6 py-4">{{ room.floor_name }} (#{{ room.floor_number }})</td>
                        <td class="px-6 py-4">{{ room.manager_name }}</td>
                        <td class="px-6 py-4">{{ room.capacity }}</td>
                        <td class="px-6 py-4">${{ room.price_dollars }}</td>
                        <td class="px-6 py-4">{{ room.reservations_count }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <Link :href="route(`${routePrefix}.rooms.edit`, room.id)">
                                    <Button variant="secondary" size="sm">Edit</Button>
                                </Link>
                                <Button variant="destructive" size="sm" @click="deleteRoom(room)">Delete</Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!rooms.data.length">
                        <td colspan="7" class="px-6 py-10 text-center text-slate-500">No rooms found for the current filters.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
            <Link
                v-for="link in rooms.links"
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
