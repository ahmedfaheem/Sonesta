<script setup>
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import { router } from '@inertiajs/vue3';
import { FlexRender, getCoreRowModel, useVueTable } from '@tanstack/vue-table';
import { computed, reactive, watch } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    endpoint: {
        type: String,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    rows: {
        type: Array,
        default: () => [],
    },
    pagination: {
        type: Object,
        required: true,
    },
    query: {
        type: Object,
        default: () => ({}),
    },
    emptyMessage: {
        type: String,
        default: 'No records found.',
    },
    searchPlaceholder: {
        type: String,
        default: 'Search...',
    },
    extraFilters: {
        type: Object,
        default: () => ({}),
    },
});

const slots = defineSlots();

const state = reactive({
    search: '',
    sort: '',
    per_page: 10,
});

const normalizeQuery = (query) => {
    state.search = query?.filter?.search ?? '';
    state.sort = query?.sort ?? '';
    state.per_page = Number(query?.per_page ?? props.pagination?.per_page ?? 10);
};

normalizeQuery(props.query);

watch(
    () => props.query,
    (nextQuery) => normalizeQuery(nextQuery),
    { deep: true },
);

const withActions = computed(() => Boolean(slots.actions));

const tableColumns = computed(() => {
    const resolved = props.columns.map((column) => ({
        id: column.id ?? column.accessorKey,
        accessorKey: column.accessorKey,
        header: column.header,
        enableSorting: column.sortable !== false,
        meta: column,
    }));

    if (withActions.value) {
        resolved.push({
            id: 'actions',
            header: 'Actions',
            enableSorting: false,
        });
    }

    return resolved;
});

const table = useVueTable({
    get data() {
        return props.rows;
    },
    get columns() {
        return tableColumns.value;
    },
    manualPagination: true,
    manualSorting: true,
    getCoreRowModel: getCoreRowModel(),
});

const buildQuery = (overrides = {}) => {
    const filter = {
        ...props.extraFilters,
        ...(overrides.filter ?? {}),
    };

    if (state.search) {
        filter.search = state.search;
    }

    const merged = {
        sort: state.sort || undefined,
        page: props.query?.page ?? props.pagination?.current_page ?? 1,
        per_page: state.per_page,
        ...overrides,
        filter,
    };

    Object.keys(merged).forEach((key) => {
        if (merged[key] === '' || merged[key] === null || typeof merged[key] === 'undefined') {
            delete merged[key];
        }
    });

    if (merged.filter && typeof merged.filter === 'object') {
        Object.keys(merged.filter).forEach((key) => {
            const value = merged.filter[key];
            if (value === '' || value === null || typeof value === 'undefined') {
                delete merged.filter[key];
            }
        });

        if (Object.keys(merged.filter).length === 0) {
            delete merged.filter;
        }
    }

    return merged;
};

const reload = (overrides = {}) => {
    router.get(props.endpoint, buildQuery(overrides), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const setSort = (column) => {
    const meta = column.columnDef.meta ?? {};
    const sortKey = meta.sortKey ?? column.id;

    if (state.sort === sortKey) {
        state.sort = `-${sortKey}`;
    } else if (state.sort === `-${sortKey}`) {
        state.sort = '';
    } else {
        state.sort = sortKey;
    }

    reload({ sort: state.sort || undefined, page: 1 });
};

const applySearch = () => {
    reload({
        filter: {
            ...props.extraFilters,
            search: state.search || undefined,
        },
        page: 1,
    });
};

const resetFilters = () => {
    state.search = '';
    state.sort = '';
    reload({ sort: undefined, page: 1 });
};

const changePerPage = () => {
    reload({ per_page: state.per_page, page: 1 });
};

const goToPage = (page) => {
    if (!page || page < 1 || page > props.pagination.last_page) {
        return;
    }

    reload({ page });
};

const sortIndicator = (column) => {
    const meta = column.columnDef.meta ?? {};
    const sortKey = meta.sortKey ?? column.id;

    if (state.sort === sortKey) {
        return '↑';
    }

    if (state.sort === `-${sortKey}`) {
        return '↓';
    }

    return '↕';
};
</script>

<template>
    <div class="space-y-6">
        <div v-if="title || description" class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 px-6 py-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 v-if="title" class="text-3xl font-semibold tracking-tight text-slate-950">{{ title }}</h1>
                    <p v-if="description" class="mt-1 text-sm text-slate-600">{{ description }}</p>
                </div>
                <slot name="header-actions" />
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/70 px-6 py-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div class="flex w-full max-w-md gap-2">
                        <Input
                            v-model="state.search"
                            :placeholder="searchPlaceholder"
                            @keyup.enter="applySearch"
                        />
                        <Button variant="secondary" @click="applySearch">Search</Button>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button variant="ghost" @click="resetFilters">Reset</Button>
                        <label class="text-sm text-slate-600">Per page</label>
                        <select
                            v-model.number="state.per_page"
                            class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-800"
                            @change="changePerPage"
                        >
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </div>
                <slot name="filters" :reload="reload" />
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-100/80">
                        <tr
                            v-for="headerGroup in table.getHeaderGroups()"
                            :key="headerGroup.id"
                            class="text-left text-sm font-semibold text-slate-600"
                        >
                            <th
                                v-for="header in headerGroup.headers"
                                :key="header.id"
                                class="whitespace-nowrap px-6 py-4"
                                :class="{
                                    'text-center w-[220px]': header.column.id === 'actions',
                                }"
                            >
                                <template v-if="!header.isPlaceholder">
                                    <button
                                        v-if="header.column.columnDef.enableSorting"
                                        type="button"
                                        class="inline-flex items-center gap-2 transition hover:text-slate-900"
                                        @click="setSort(header.column)"
                                    >
                                        <FlexRender
                                            :render="header.column.columnDef.header"
                                            :props="header.getContext()"
                                        />
                                        <span class="text-[11px] font-bold text-slate-500">{{ sortIndicator(header.column) }}</span>
                                    </button>
                                    <span v-else>
                                        <FlexRender
                                            :render="header.column.columnDef.header"
                                            :props="header.getContext()"
                                        />
                                    </span>
                                </template>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white text-sm text-slate-700">
                        <tr
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            class="transition hover:bg-slate-50/80"
                        >
                            <td
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                                class="whitespace-nowrap px-6 py-4 align-middle"
                                :class="{
                                    'text-center w-[220px]': cell.column.id === 'actions',
                                }"
                            >
                                <div
                                    v-if="cell.column.id === 'actions' && slots.actions"
                                    class="flex flex-nowrap items-center justify-center gap-2 whitespace-nowrap"
                                >
                                    <slot
                                        name="actions"
                                        :row="row.original"
                                    />
                                </div>
                                <slot
                                    v-else-if="$slots[`cell-${cell.column.id}`]"
                                    :name="`cell-${cell.column.id}`"
                                    :row="row.original"
                                    :value="cell.getValue()"
                                />
                                <FlexRender
                                    v-else
                                    :render="cell.column.columnDef.cell ?? cell.column.columnDef.header"
                                    :props="cell.getContext()"
                                />
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td
                                :colspan="tableColumns.length"
                                class="px-6 py-14 text-center text-sm text-slate-500"
                            >
                                {{ emptyMessage }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50/60 px-6 py-4">
                <p class="text-sm text-slate-600">
                    Page {{ pagination.current_page }} of {{ pagination.last_page }}
                </p>
                <div class="flex items-center gap-2">
                    <Button
                        variant="secondary"
                        :disabled="pagination.current_page <= 1"
                        @click="goToPage(pagination.current_page - 1)"
                    >
                        Previous
                    </Button>
                    <Button
                        variant="secondary"
                        :disabled="pagination.current_page >= pagination.last_page"
                        @click="goToPage(pagination.current_page + 1)"
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
