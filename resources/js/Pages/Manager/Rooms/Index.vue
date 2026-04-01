<script setup>
import DataTable from '@/Components/DataTable.vue';
import DeleteConfirmationDialog from '@/Components/Admin/DeleteConfirmationDialog.vue';
import Button from '@/Components/ui/Button.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

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
    query: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const routePrefix = computed(() => ((page.props.auth.user?.roles ?? []).includes('admin') ? 'admin' : 'manager'));
const isAdmin = computed(() => (page.props.auth.user?.roles ?? []).includes('admin'));
const authUserId = computed(() => page.props.auth.user?.id);
const canManageRoom = (room) => isAdmin.value || Number(room.manager_id) === Number(authUserId.value);

const selectedFloorId = ref(props.query?.filter?.floor_id ? Number(props.query.filter.floor_id) : null);

watch(
    () => props.query?.filter?.floor_id,
    (next) => {
        selectedFloorId.value = next ? Number(next) : null;
    },
);

const columns = computed(() => {
    const base = [
        { id: 'number', header: 'Room', accessorKey: 'number', sortKey: 'number' },
        { id: 'floor_name', header: 'Floor', accessorKey: 'floor_name', sortable: false },
        { id: 'capacity', header: 'Capacity', accessorKey: 'capacity', sortKey: 'capacity' },
        { id: 'price_dollars', header: 'Price', accessorKey: 'price_dollars', sortKey: 'price' },
        { id: 'reservations_count', header: 'Reservations', accessorKey: 'reservations_count', sortable: false },
        { id: 'created_at', header: 'Created', accessorKey: 'created_at', sortKey: 'created_at' },
    ];

    if (isAdmin.value) {
        base.splice(2, 0, { id: 'manager_name', header: 'Manager', accessorKey: 'manager_name', sortable: false });
    }

    return base;
});

const selectedRoom = ref(null);
const deleting = ref(false);

const confirmDelete = (room) => {
    selectedRoom.value = room;
};

const deleteRoom = () => {
    if (!selectedRoom.value) {
        return;
    }

    deleting.value = true;

    router.delete(route(`${routePrefix.value}.rooms.destroy`, selectedRoom.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            selectedRoom.value = null;
        },
    });
};

const formatDate = (value) => {
    if (!value) return '-';

    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(value));
};
</script>

<template>
    <div>
        <div
            v-if="$page.props.errors?.room"
            class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
        >
            {{ $page.props.errors.room }}
        </div>

        <DataTable
            title="Rooms"
            description="Track room inventory, pricing, and reservation-safe actions."
            :endpoint="route(`${routePrefix}.rooms.index`)"
            :columns="columns"
            :rows="rooms.data"
            :pagination="rooms"
            :query="query"
            :extra-filters="{ floor_id: selectedFloorId || undefined }"
            search-placeholder="Search rooms by number or floor"
        >
            <template #header-actions>
                <Link :href="route(`${routePrefix}.rooms.create`)">
                    <Button>Create room</Button>
                </Link>
            </template>

            <template #filters="{ reload }">
                <div class="mt-3 flex items-center gap-2">
                    <label class="text-sm text-slate-600">Floor</label>
                    <select
                        v-model="selectedFloorId"
                        class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-800"
                        @change="reload({ filter: { floor_id: selectedFloorId || undefined }, page: 1 })"
                    >
                        <option :value="null">All floors</option>
                        <option v-for="floor in floors" :key="floor.id" :value="floor.id">
                            {{ floor.name }} (#{{ floor.number }})
                        </option>
                    </select>
                </div>
            </template>

            <template #cell-number="{ value }">
                #{{ value }}
            </template>

            <template #cell-floor_name="{ row }">
                {{ row.floor_name }} <span v-if="row.floor_number">(#{{ row.floor_number }})</span>
            </template>

            <template #cell-price_dollars="{ value }">
                ${{ value }}
            </template>

            <template #cell-created_at="{ value }">
                {{ formatDate(value) }}
            </template>

            <template #actions="{ row }">
                <template v-if="canManageRoom(row)">
                    <Link :href="route(`${routePrefix}.rooms.edit`, row.id)">
                        <Button variant="secondary" size="sm">Edit</Button>
                    </Link>
                    <Button variant="destructive" size="sm" @click="confirmDelete(row)">
                        Delete
                    </Button>
                </template>
            </template>
        </DataTable>

        <DeleteConfirmationDialog
            :show="!!selectedRoom"
            title="Delete room"
            :description="selectedRoom ? `Delete room ${selectedRoom.number}? This action cannot be undone.` : ''"
            :processing="deleting"
            @close="selectedRoom = null"
            @confirm="deleteRoom"
        />
    </div>
</template>
