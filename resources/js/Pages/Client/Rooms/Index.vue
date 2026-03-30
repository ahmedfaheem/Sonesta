<script setup>
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({
    layout: ClientLayout,
});

defineProps({
    rooms: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Available Rooms</h1>
            <p class="mt-1 text-sm text-slate-600">Choose from rooms that are currently available, then continue to payment checkout.</p>
        </div>

        <Card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-4">Room Number</th>
                            <th class="px-6 py-4">Floor</th>
                            <th class="px-6 py-4">Capacity</th>
                            <th class="px-6 py-4">Price</th>
                            <th class="px-6 py-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                        <tr v-for="room in rooms.data" :key="room.id" class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">#{{ room.number }}</td>
                            <td class="px-6 py-4">{{ room.floor_name || '-' }}<span v-if="room.floor_number"> (#{{ room.floor_number }})</span></td>
                            <td class="px-6 py-4">{{ room.capacity }}</td>
                            <td class="px-6 py-4">${{ room.price_dollars }}</td>
                            <td class="px-6 py-4">
                                <Link :href="route('client.reservations.create', room.id)">
                                    <Button size="sm">Make Reservation</Button>
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="rooms.data.length === 0">
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">
                                There are no available rooms at the moment.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="rooms.links.length > 3" class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
                <Link
                    v-for="link in rooms.links"
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
