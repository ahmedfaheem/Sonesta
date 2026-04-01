<script setup>
import StatsCard from '@/Components/Admin/StatsCard.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import Chart from 'chart.js/auto';

defineOptions({
    layout: AdminLayout,
});

defineProps({
    stats: {
        type: Object,
        required: true,
    },
});

const revenueChartCanvas = ref(null);
const topClientsChartCanvas = ref(null);

const buildRevenueChart = (labels, values) => {
    if (!revenueChartCanvas.value) {
        return;
    }

    new Chart(revenueChartCanvas.value, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: values,
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.2)',
                    fill: true,
                    tension: 0.35,
                },
            ],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    ticks: {
                        callback: (value) => `$${value}`,
                    },
                },
            },
        },
    });
};

const buildTopClientsChart = (labels, values) => {
    if (!topClientsChartCanvas.value) {
        return;
    }

    new Chart(topClientsChartCanvas.value, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Top clients',
                    data: values,
                    backgroundColor: '#14b8a6',
                },
            ],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0,
                },
            },
        },
    });
};

onMounted(async () => {
    try {
        const [revenueResponse, topClientsResponse] = await Promise.all([
            axios.get('/api/analytics/revenue'),
            axios.get('/api/analytics/top-clients'),
        ]);

        buildRevenueChart(
            revenueResponse.data.data.map((item) => item.month),
            revenueResponse.data.data.map((item) => Number(item.revenue)),
        );
        buildTopClientsChart(
            topClientsResponse.data.data.map((item) => item.name),
            topClientsResponse.data.data.map((item) => item.reservations_count),
        );
    } catch (error) {
        console.error('Failed to load admin charts', error);
    }
});
</script>

<template>
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-600">Overview of the hotel administration workspace.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <StatsCard label="Total Managers" :value="stats.managers" />
            <StatsCard label="Total Receptionists" :value="stats.receptionists" />
            <StatsCard label="Total Clients" :value="stats.clients" />
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <Card class="overflow-hidden border-0 shadow-sm">
                <div class="h-2 w-full bg-gradient-to-r from-sky-500 to-cyan-500" />

                <div class="space-y-5 p-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-950">Floors</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Open the shared manager floors module with full admin visibility across all managers.
                        </p>
                    </div>

                    <ul class="space-y-3 text-sm text-slate-700">
                        <li>View all floors across the hotel.</li>
                        <li>Create floors and assign them to managers.</li>
                        <li>Update ownership while keeping deletion rules enforced.</li>
                    </ul>

                    <div class="flex flex-wrap gap-3">
                        <Link :href="route('admin.floors.index')">
                            <Button>Open floors</Button>
                        </Link>
                        <Link :href="route('admin.floors.create')">
                            <Button variant="secondary">Create floor</Button>
                        </Link>
                    </div>
                </div>
            </Card>

            <Card class="overflow-hidden border-0 shadow-sm">
                <div class="h-2 w-full bg-gradient-to-r from-emerald-500 to-teal-500" />

                <div class="space-y-5 p-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-950">Rooms</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Access the shared room inventory screens and manage rooms for any manager from one place.
                        </p>
                    </div>

                    <ul class="space-y-3 text-sm text-slate-700">
                        <li>Review all rooms with pricing, capacity, and ownership.</li>
                        <li>Create rooms on any manager-owned floor.</li>
                        <li>Respect reservation locks when deleting rooms.</li>
                    </ul>

                    <div class="flex flex-wrap gap-3">
                        <Link :href="route('admin.rooms.index')">
                            <Button>Open rooms</Button>
                        </Link>
                        <Link :href="route('admin.rooms.create')">
                            <Button variant="secondary">Create room</Button>
                        </Link>
                    </div>
                </div>
            </Card>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <Card class="overflow-hidden border-0 shadow-sm">
                <div class="h-2 w-full bg-gradient-to-r from-sky-500 to-cyan-500" />
                <div class="space-y-5 p-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-950">Monthly Revenue</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Revenue per reservation month, updated from the analytics API.
                        </p>
                    </div>
                    <div class="h-72">
                        <canvas ref="revenueChartCanvas" class="h-full w-full"></canvas>
                    </div>
                </div>
            </Card>

            <Card class="overflow-hidden border-0 shadow-sm">
                <div class="h-2 w-full bg-gradient-to-r from-emerald-500 to-teal-500" />
                <div class="space-y-5 p-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-950">Top Clients</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Most active clients by reservation count.
                        </p>
                    </div>
                    <div class="h-72">
                        <canvas ref="topClientsChartCanvas" class="h-full w-full"></canvas>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</template>
