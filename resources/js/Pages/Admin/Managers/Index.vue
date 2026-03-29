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
    managers: {
        type: Object,
        required: true,
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
</script>

<template>
    <div>
        <UserTable
            title="Managers"
            description="Create, update, and remove manager accounts."
            :rows="managers.data"
            :links="managers.links"
            :create-href="route('admin.managers.create')"
            create-label="Create manager"
            @delete="confirmDelete"
        >
            <template #actions="{ row }">
                <Link :href="route('admin.managers.edit', row.id)">
                    <Button variant="secondary" size="sm">Edit</Button>
                </Link>
                <Button variant="destructive" size="sm" @click="confirmDelete(row)">Delete</Button>
            </template>
        </UserTable>

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
