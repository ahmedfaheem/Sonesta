<script setup>
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import Avatar from '@/Components/ui/Avatar.vue';
import Badge from '@/Components/ui/Badge.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({
    layout: ManagerLayout,
});

defineProps({
    client: {
        type: Object,
        required: true,
    },
});

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(new Date(value));
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Client details</h1>
                <p class="mt-1 text-sm text-slate-600">Detailed information for the selected client account.</p>
            </div>

            <div class="flex gap-3">
                <Link :href="route('manager.clients.edit', client.id)">
                    <Button variant="secondary">Edit</Button>
                </Link>
                <Link :href="route('manager.clients.index')">
                    <Button>Back to clients</Button>
                </Link>
            </div>
        </div>

        <Card>
            <div class="grid gap-8 p-6 md:grid-cols-[240px_1fr]">
                <div class="flex flex-col items-center gap-4">
                    <Avatar :src="client.avatar_url" :fallback="client.name" size="xl" :alt="client.name" />
                    <Badge :variant="client.is_approved ? 'success' : 'warning'">
                        {{ client.is_approved ? 'Approved client' : 'Pending approval' }}
                    </Badge>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Name</p>
                        <p class="mt-2 text-base font-medium text-slate-900">{{ client.name }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</p>
                        <p class="mt-2 text-base font-medium text-slate-900">{{ client.email }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">National ID</p>
                        <p class="mt-2 text-base font-medium text-slate-900">{{ client.national_id || 'N/A' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Created</p>
                        <p class="mt-2 text-base font-medium text-slate-900">{{ formatDate(client.created_at) }}</p>
                    </div>
                </div>
            </div>
        </Card>
    </div>
</template>

