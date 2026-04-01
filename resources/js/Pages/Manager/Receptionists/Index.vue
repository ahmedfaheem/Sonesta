<script setup>
import DeleteConfirmationDialog from '@/Components/Admin/DeleteConfirmationDialog.vue';
import UserTable from '@/Components/Admin/UserTable.vue';
import Button from '@/Components/ui/Button.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({
    layout: ManagerLayout,
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

    router.delete(route('manager.receptionists.destroy', selectedReceptionist.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            selectedReceptionist.value = null;
        },
    });
};

const toggleBan = (receptionist) => {
    router.patch(route('manager.receptionists.ban', receptionist.id), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div>
        <UserTable
            title="Receptionists"
            description="Manage your reception desk team accounts."
            :rows="receptionists.data"
            :links="receptionists.links"
            :create-href="route('manager.receptionists.create')"
            create-label="Create receptionist"
            :show-creator-column="isAdmin"
            creator-column-label="Manager Name"
            @delete="confirmDelete"
        >
            <template #actions="{ row }">
                <div :key="row.id + '-' + row.can_manage">
                    <template v-if="row.can_manage === true">
                        <Link :href="route('manager.receptionists.edit', row.id)" class="mr-2">
                            <Button variant="secondary" size="sm">Edit</Button>
                        </Link>

                        <Button
                            variant="secondary"
                            size="sm"
                            @click="toggleBan(row)"
                            class="mr-2"
                        >
                            {{ row.is_approved ? 'Ban' : 'Unban' }}
                        </Button>

                        <Button
                            variant="destructive"
                            size="sm"
                            @click="confirmDelete(row)"
                            class="mr-2"
                        >
                            Delete
                        </Button>
                    </template>
                </div>
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
