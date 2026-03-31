<script setup>
import DeleteConfirmationDialog from '@/Components/Admin/DeleteConfirmationDialog.vue';
import UserTable from '@/Components/Admin/UserTable.vue';
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
</script>

<template>
    <div>
        <div class="mb-4 flex flex-wrap items-center justify-end gap-3">
            <a :href="route('admin.clients.export')" class="inline-flex">
                <Button variant="secondary">Export clients</Button>
            </a>
        </div>

        <UserTable
            title="Clients"
            description="Manage client accounts and their approval state."
            :rows="clients.data"
            :links="clients.links"
            :create-href="route('admin.clients.create')"
            create-label="Create client"
            show-approval
            @delete="confirmDelete"
        >
            <template #actions="{ row }">
                <Link :href="route('admin.clients.show', row.id)">
                    <Button variant="secondary" size="sm">View</Button>
                </Link>
                <Link :href="route('admin.clients.edit', row.id)">
                    <Button variant="secondary" size="sm">Edit</Button>
                </Link>
                <Button variant="destructive" size="sm" @click="confirmDelete(row)">Delete</Button>
            </template>
        </UserTable>

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
