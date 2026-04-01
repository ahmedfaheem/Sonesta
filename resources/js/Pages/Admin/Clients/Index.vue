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
    clients: {
        type: Object,
        required: true,
    },
    query: {
        type: Object,
        default: () => ({}),
    },
});

const selectedClient = ref(null);
const deleting = ref(false);

const confirmDelete = (client) => {
    selectedClient.value = client;
};

const deleteClient = () => {
    if (!selectedClient.value) {
        return;
    }

    deleting.value = true;

    router.delete(route('admin.clients.destroy', selectedClient.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            selectedClient.value = null;
        },
    });
};

const columns = [
    { id: 'name', header: 'Name', accessorKey: 'name', sortKey: 'name' },
    { id: 'email', header: 'Email', accessorKey: 'email', sortKey: 'email' },
    { id: 'status', header: 'Status', accessorKey: 'is_approved', sortable: false },
    { id: 'created_at', header: 'Created', accessorKey: 'created_at', sortKey: 'created_at' },
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
        <div class="mb-4 flex flex-wrap items-center justify-end gap-3">
            <a :href="route('admin.clients.export')" class="inline-flex">
                <Button variant="secondary">Export clients</Button>
            </a>
        </div>

        <DataTable
            title="Clients"
            description="Manage client accounts and their approval state."
            :endpoint="route('admin.clients.index')"
            :columns="columns"
            :rows="clients.data"
            :pagination="clients"
            :query="query"
            search-placeholder="Search clients by name or email"
        >
            <template #header-actions>
                <Link :href="route('admin.clients.create')">
                    <Button>Create client</Button>
                </Link>
            </template>

            <template #cell-status="{ row }">
                <Badge :variant="row.is_approved ? 'success' : 'warning'">
                    {{ row.is_approved ? 'Approved' : 'Pending' }}
                </Badge>
            </template>

            <template #cell-created_at="{ value }">
                {{ formatDate(value) }}
            </template>

            <template #actions="{ row }">
                <Link :href="route('admin.clients.show', row.id)">
                    <Button variant="secondary" size="sm">View</Button>
                </Link>
                <Link :href="route('admin.clients.edit', row.id)">
                    <Button variant="secondary" size="sm">Edit</Button>
                </Link>
                <Button variant="destructive" size="sm" @click="confirmDelete(row)">Delete</Button>
            </template>
        </DataTable>

        <DeleteConfirmationDialog
            :show="!!selectedClient"
            title="Delete client"
            :description="selectedClient ? `Delete ${selectedClient.name}? This action cannot be undone.` : ''"
            :processing="deleting"
            @close="selectedClient = null"
            @confirm="deleteClient"
        />
    </div>
</template>
