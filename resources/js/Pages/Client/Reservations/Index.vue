<script setup>
import DeleteConfirmationDialog from '@/Components/Admin/DeleteConfirmationDialog.vue';
import Card from '@/Components/ui/Card.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({
    layout: ClientLayout,
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

const selectedReservation = ref(null);
const removing = ref(false);

const confirmRemove = (reservation) => {
    selectedReservation.value = reservation;
};

const removeReservation = () => {
    if (!selectedReservation.value) {
        return;
    }

    removing.value = true;

    router.delete(route('client.reservations.destroy', selectedReservation.value.id), {
        preserveScroll: true,
        onFinish: () => {
            removing.value = false;
            selectedReservation.value = null;
        },
    });
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">My Reservations</h1>
            <p class="mt-1 text-sm text-slate-600">Review your reservation history and the paid-price snapshot for each booking.</p>
        </div>

        <Card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-4">Room Number</th>
                            <th class="px-6 py-4">Accompany Number</th>
                            <th class="px-6 py-4">Paid Price</th>
                            <th class="px-6 py-4">Reservation Date</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                        <tr v-for="reservation in reservations.data" :key="reservation.id" class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">#{{ reservation.room_number || '-' }}</td>
                            <td class="px-6 py-4">{{ reservation.accompany_number }}</td>
                            <td class="px-6 py-4">${{ reservation.paid_price_dollars }}</td>
                            <td class="px-6 py-4">{{ formatDate(reservation.reservation_date) }}</td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    type="button"
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50"
                                    @click="confirmRemove(reservation)"
                                >
                                    Remove
                                </button>
                            </td>
                        </tr>
                        <tr v-if="reservations.data.length === 0">
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">
                                You do not have any reservations yet.
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

        <DeleteConfirmationDialog
            :show="!!selectedReservation"
            title="Remove reservation"
            :description="selectedReservation ? `Remove reservation for room ${selectedReservation.room_number ?? '-' }?` : ''"
            confirm-label="Remove"
            :processing="removing"
            @close="selectedReservation = null"
            @confirm="removeReservation"
        />
    </div>
</template>
