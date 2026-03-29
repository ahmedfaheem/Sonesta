<script setup>
import Avatar from '@/Components/ui/Avatar.vue';
import Badge from '@/Components/ui/Badge.vue';
import Card from '@/Components/ui/Card.vue';
import ReceptionistLayout from '@/Layouts/ReceptionistLayout.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({
    layout: ReceptionistLayout,
});

defineProps({
    clients: {
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
        month: 'short',
        day: 'numeric',
    }).format(new Date(value));
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">My Approved Clients</h1>
            <p class="mt-1 text-sm text-slate-600">This list is filtered strictly to the clients approved by your receptionist account.</p>
        </div>

        <Card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Country</th>
                            <th class="px-6 py-4">Gender</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Approved On</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                        <tr v-for="client in clients.data" :key="client.id" class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <Avatar :src="client.avatar_url" :fallback="client.name" size="sm" :alt="client.name" />
                                    <div>
                                        <p class="font-medium text-slate-900">{{ client.name }}</p>
                                        <p class="text-xs text-slate-500">{{ client.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ client.country || '-' }}</td>
                            <td class="px-6 py-4 capitalize">{{ client.gender || '-' }}</td>
                            <td class="px-6 py-4">
                                <Badge variant="success">Approved</Badge>
                            </td>
                            <td class="px-6 py-4">{{ formatDate(client.approved_at) }}</td>
                        </tr>
                        <tr v-if="clients.data.length === 0">
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">
                                You have not approved any clients yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="clients.links.length > 3" class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
                <Link
                    v-for="link in clients.links"
                    :key="`${link.label}-${link.url}`"
                    :href="link.url || '#'"
                    class="rounded-xl px-3 py-2 text-sm transition"
                    :class="[
                        link.active ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700',
                        !link.url ? 'pointer-events-none opacity-50' : '',
                    ]"
                    v-html="link.label"
                />
            </div>
        </Card>
    </div>
</template>
