<script setup>
import Avatar from '@/Components/ui/Avatar.vue';
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    rows: {
        type: Array,
        required: true,
    },
    links: {
        type: Array,
        default: () => [],
    },
    createHref: {
        type: String,
        default: null,
    },
    createLabel: {
        type: String,
        default: 'Create',
    },
    showCreatorColumn: {
        type: Boolean,
        default: false,
    },
    creatorColumnLabel: {
        type: String,
        default: 'Created By',
    },
    showApproval: {
        type: Boolean,
        default: false,
    },
    showView: {
        type: Boolean,
        default: false,
    },
    showEdit: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['delete']);

const columnCount = () => 4 + (props.showCreatorColumn ? 1 : 0) + (props.showApproval ? 1 : 0);

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(value));
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ title }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ description }}</p>
            </div>

            <div class="flex items-center gap-2">
                <slot name="headerActions" />
                <Link v-if="createHref" :href="createHref">
                    <Button>{{ createLabel }}</Button>
                </Link>
            </div>
        </div>

        <Card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Created</th>
                            <th v-if="showCreatorColumn" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ creatorColumnLabel }}</th>
                            <th v-if="showApproval" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="row in rows" :key="row.id" class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <Avatar :src="row.avatar_url" :fallback="row.name" size="sm" :alt="row.name" />
                                    <span class="text-sm font-medium text-slate-900">{{ row.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ row.email }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(row.created_at) }}</td>
                            <td v-if="showCreatorColumn" class="px-6 py-4 text-sm text-slate-600">{{ row.created_by_name || 'Admin' }}</td>
                            <td v-if="showApproval" class="px-6 py-4">
                                <Badge :variant="row.is_approved ? 'success' : 'warning'">
                                    {{ row.is_approved ? 'Approved' : 'Pending' }}
                                </Badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <slot name="actions" :row="row">
                                        <Link v-if="showView" :href="row.view_href">
                                            <Button variant="secondary" size="sm">View</Button>
                                        </Link>
                                        <Link v-if="showEdit" :href="row.edit_href">
                                            <Button variant="secondary" size="sm">Edit</Button>
                                        </Link>
                                        <Button variant="destructive" size="sm" @click="emit('delete', row)">Delete</Button>
                                    </slot>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="rows.length === 0">
                            <td :colspan="columnCount()" class="px-6 py-16 text-center text-sm text-slate-500">
                                No records found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="links.length > 3" class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
                <component
                    :is="link.url ? Link : 'span'"
                    v-for="link in links"
                    :key="link.label"
                    :href="link.url"
                    class="rounded-xl px-3 py-2 text-sm transition"
                    :class="[
                        link.active ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200',
                        !link.url ? 'cursor-not-allowed opacity-50' : '',
                    ]"
                    v-html="link.label"
                />
            </div>
        </Card>
    </div>
</template>
