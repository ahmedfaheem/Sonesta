<script setup>
import Card from '@/Components/ui/Card.vue';
import ReceptionistLayout from '@/Layouts/ReceptionistLayout.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({
    layout: ReceptionistLayout,
});

defineProps({
    reservations: {
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
        hour: 'numeric',
        minute: '2-digit',
    }).format(new Date(value));
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Client Reservations</h1>
            <p class="mt-1 text-sm text-slate-600">Review reservation snapshots with the paid price preserved from the time of booking.</p>
        </div>

        <Card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-4">Client Name</th>
                            <th class="px-6 py-4">Room Number</th>
                            <th class="px-6 py-4">Accompany Number</th>
                            <th class="px-6 py-4">Paid Price (Snapshot)</th>
                            <th class="px-6 py-4">Reservation Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                        <tr v-for="reservation in reservations.data" :key="reservation.id" class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ reservation.client_name || '-' }}</td>
                            <td class="px-6 py-4">#{{ reservation.room_number || '-' }}</td>
                            <td class="px-6 py-4">{{ reservation.accompany_number }}</td>
                            <td class="px-6 py-4">${{ reservation.paid_price_dollars }}</td>
                            <td class="px-6 py-4">{{ formatDate(reservation.reservation_date) }}</td>
                        </tr>
                        <tr v-if="reservations.data.length === 0">
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">
                                No reservations have been recorded yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="reservations.links.length > 3" class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
                <Link
                    v-for="link in reservations.links"
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
