<script setup>
import DataTable from '@/Components/DataTable.vue';
import DeleteConfirmationDialog from '@/Components/Admin/DeleteConfirmationDialog.vue';
import Button from '@/Components/ui/Button.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({
    layout: ManagerLayout,
});

const props = defineProps({
    floors: {
        type: Object,
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
const canManageFloor = (floor) => isAdmin.value || Number(floor.manager_id) === Number(authUserId.value);

const columns = computed(() => {
    const base = [
        { id: 'name', header: 'Name', accessorKey: 'name', sortKey: 'name' },
        { id: 'number', header: 'Number', accessorKey: 'number', sortKey: 'number' },
        { id: 'rooms_count', header: 'Rooms', accessorKey: 'rooms_count', sortable: false },
        { id: 'created_at', header: 'Created', accessorKey: 'created_at', sortKey: 'created_at' },
    ];

    if (isAdmin.value) {
        base.splice(2, 0, { id: 'manager_name', header: 'Manager', accessorKey: 'manager_name', sortable: false });
    }

    return base;
});

const selectedFloor = ref(null);
const deleting = ref(false);

const confirmDelete = (floor) => {
    selectedFloor.value = floor;
};

const deleteFloor = () => {
    if (!selectedFloor.value) {
        return;
    }

    deleting.value = true;

    router.delete(route(`${routePrefix.value}.floors.destroy`, selectedFloor.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            selectedFloor.value = null;
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
            v-if="$page.props.errors?.floor"
            class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
        >
            {{ $page.props.errors.floor }}
        </div>

        <DataTable
            title="Floors"
            description="Manage floor ownership and prevent deletion when rooms still exist."
            :endpoint="route(`${routePrefix}.floors.index`)"
            :columns="columns"
            :rows="floors.data"
            :pagination="floors"
            :query="query"
            search-placeholder="Search floors by name or number"
        >
            <template #header-actions>
                <Link :href="route(`${routePrefix}.floors.create`)">
                    <Button>Create floor</Button>
                </Link>
            </template>

            <template #cell-created_at="{ value }">
                {{ formatDate(value) }}
            </template>

            <template #actions="{ row }">
                <template v-if="canManageFloor(row)">
                    <Link :href="route(`${routePrefix}.floors.edit`, row.id)">
                        <Button variant="secondary" size="sm">Edit</Button>
                    </Link>
                    <Button variant="destructive" size="sm" @click="confirmDelete(row)">
                        Delete
                    </Button>
                </template>
            </template>
        </DataTable>

        <DeleteConfirmationDialog
            :show="!!selectedFloor"
            title="Delete floor"
            :description="selectedFloor ? `Delete floor ${selectedFloor.name}? This action cannot be undone.` : ''"
            :processing="deleting"
            @close="selectedFloor = null"
            @confirm="deleteFloor"
        />
    </div>
</template>
