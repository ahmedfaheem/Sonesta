<script setup>
import DeleteConfirmationDialog from '@/Components/Admin/DeleteConfirmationDialog.vue';
import DataTable from '@/Components/DataTable.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    managers: {
        type: Object,
        required: true,
    },
    query: {
        type: Object,
        default: () => ({}),
    },
});

const selectedManager = ref(null);
const deleting = ref(false);

const confirmDelete = (manager) => {
    selectedManager.value = manager;
};

const deleteManager = () => {
    if (!selectedManager.value) {
        return;
    }

    deleting.value = true;

    router.delete(route('admin.managers.destroy', selectedManager.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            selectedManager.value = null;
        },
    });
};

const toggleBan = (manager) => {
    router.patch(route('admin.managers.ban', manager.id), {}, {
        preserveScroll: true,
    });
};

const columns = [
    { id: 'name', header: 'Name', accessorKey: 'name', sortKey: 'name' },
    { id: 'email', header: 'Email', accessorKey: 'email', sortKey: 'email' },
    { id: 'created_at', header: 'Created', accessorKey: 'created_at', sortKey: 'created_at' },
    { id: 'status', header: 'Status', accessorKey: 'is_approved', sortable: false },
];

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
        <DataTable
            title="Managers"
            description="Create, update, and remove manager accounts."
            :endpoint="route('admin.managers.index')"
            :columns="columns"
            :rows="managers.data"
            :pagination="managers"
            :query="query"
            search-placeholder="Search managers by name or email"
        >
            <template #header-actions>
                <Link :href="route('admin.managers.create')">
                    <Button>Create manager</Button>
                </Link>
            </template>

            <template #cell-created_at="{ value }">
                {{ formatDate(value) }}
            </template>

            <template #cell-status="{ row }">
                <Badge :variant="row.is_approved ? 'success' : 'warning'">
                    {{ row.is_approved ? 'Active' : 'Banned' }}
                </Badge>
            </template>

            <template #actions="{ row }">
                <Link :href="route('admin.managers.edit', row.id)">
                    <Button variant="secondary" size="sm">Edit</Button>
                </Link>
                <Button variant="secondary" size="sm" @click="toggleBan(row)">
                    {{ row.is_approved ? 'Ban' : 'Unban' }}
                </Button>
                <Button variant="destructive" size="sm" @click="confirmDelete(row)">Delete</Button>
            </template>
        </DataTable>

        <DeleteConfirmationDialog
            :show="!!selectedManager"
            title="Delete manager"
            :description="selectedManager ? `Delete ${selectedManager.name}? This action cannot be undone.` : ''"
            :processing="deleting"
            @close="selectedManager = null"
            @confirm="deleteManager"
        />
    </div>
</template>
