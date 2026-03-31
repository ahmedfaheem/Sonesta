<script setup>
import DeleteConfirmationDialog from '@/Components/Admin/DeleteConfirmationDialog.vue';
import UserTable from '@/Components/Admin/UserTable.vue';
import Button from '@/Components/ui/Button.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    receptionists: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const roles = computed(() => page.props.auth.user?.roles ?? []);
const isAdmin = computed(() => roles.value.includes('admin'));

const selectedReceptionist = ref(null);
const deleting = ref(false);

const confirmDelete = (receptionist) => {
    selectedReceptionist.value = receptionist;
};

const deleteReceptionist = () => {
    if (!selectedReceptionist.value) {
        return;
    }

    deleting.value = true;

    router.delete(route('admin.receptionists.destroy', selectedReceptionist.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            selectedReceptionist.value = null;
        },
    });
};

const toggleBan = (receptionist) => {
    router.patch(route('admin.receptionists.ban', receptionist.id), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div>
        <UserTable
            title="Receptionists"
            description="Manage reception desk staff accounts."
            :rows="receptionists.data"
            :links="receptionists.links"
            :create-href="route('admin.receptionists.create')"
            create-label="Create receptionist"
            :show-creator-column="isAdmin"
            creator-column-label="Manager Name"
            @delete="confirmDelete"
        >
            <template #actions="{ row }">
                <Link :href="route('admin.receptionists.edit', row.id)">
                    <Button variant="secondary" size="sm">Edit</Button>
                </Link>
                <Button variant="secondary" size="sm" @click="toggleBan(row)">
                    {{ row.is_approved ? 'Ban' : 'Unban' }}
                </Button>
                <Button variant="destructive" size="sm" @click="confirmDelete(row)">Delete</Button>
            </template>
        </UserTable>

        <DeleteConfirmationDialog
            :show="!!selectedReceptionist"
            title="Delete receptionist"
            :description="selectedReceptionist ? `Delete ${selectedReceptionist.name}? This action cannot be undone.` : ''"
            :processing="deleting"
            @close="selectedReceptionist = null"
            @confirm="deleteReceptionist"
        />
    </div>
</template>
