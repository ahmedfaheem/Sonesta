<script setup>
import DeleteConfirmationDialog from '@/Components/Admin/DeleteConfirmationDialog.vue';
import DataTable from '@/Components/DataTable.vue';
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
    query: {
        type: Object,
        default: () => ({}),
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

const columns = computed(() => {
    const base = [
        { id: 'name', header: 'Name', accessorKey: 'name', sortKey: 'name' },
        { id: 'email', header: 'Email', accessorKey: 'email', sortKey: 'email' },
        { id: 'created_at', header: 'Created', accessorKey: 'created_at', sortKey: 'created_at' },
    ];

    if (isAdmin.value) {
        base.splice(2, 0, {
            id: 'created_by_name',
            header: 'Manager Name',
            accessorKey: 'created_by_name',
            sortable: false,
        });
    }

    return base;
});

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
            title="Receptionists"
            description="Manage reception desk staff accounts."
            :endpoint="route('admin.receptionists.index')"
            :columns="columns"
            :rows="receptionists.data"
            :pagination="receptionists"
            :query="query"
            search-placeholder="Search receptionists by name or email"
        >
            <template #header-actions>
                <Link :href="route('admin.receptionists.create')">
                    <Button>Create receptionist</Button>
                </Link>
            </template>

            <template #cell-created_at="{ value }">
                {{ formatDate(value) }}
            </template>

            <template #actions="{ row }">
                <Link :href="route('admin.receptionists.edit', row.id)">
                    <Button variant="secondary" size="sm">Edit</Button>
                </Link>
                <Button variant="secondary" size="sm" @click="toggleBan(row)">
                    {{ row.is_approved ? 'Ban' : 'Unban' }}
                </Button>
                <Button variant="destructive" size="sm" @click="confirmDelete(row)">Delete</Button>
            </template>
        </DataTable>

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
