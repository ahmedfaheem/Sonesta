<script setup>
defineProps({
    stats: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-6 rounded-3xl border border-white/30 bg-white/70 p-8 shadow-xl backdrop-blur md:grid-cols-3">
            <div class="space-y-3">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Total Rooms</p>
                <p class="text-3xl font-semibold text-slate-950">{{ stats.totalRooms }}</p>
                <p class="text-sm text-slate-600">Active rooms currently managed.</p>
            </div>
            <div class="space-y-3">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Reservations</p>
                <p class="text-3xl font-semibold text-slate-950">{{ stats.totalReservations }}</p>
                <p class="text-sm text-slate-600">Total bookings handled by the system.</p>
            </div>
            <div class="space-y-3">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Average Price</p>
                <p class="text-3xl font-semibold text-slate-950">${{ stats.averagePrice }}</p>
                <p class="text-sm text-slate-600">Average nightly price across rooms.</p>
            </div>
        </div>
    </section>
</template>
