<script setup>
import StatsCard from '@/Components/Admin/StatsCard.vue';
import ChartCard from '@/Components/Charts/ChartCard.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import { nextTick, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
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
const reservationsByCountryCanvas = ref(null);
const reservationsByGenderCanvas = ref(null);

const loading = reactive({
    revenue: true,
    topClients: true,
    country: true,
    gender: true,
});

const errors = reactive({
    revenue: '',
    topClients: '',
    country: '',
    gender: '',
});

const chartInstances = {
    revenue: null,
    topClients: null,
    country: null,
    gender: null,
};

const resetChart = (key) => {
    if (chartInstances[key]) {
        chartInstances[key].destroy();
        chartInstances[key] = null;
    }
};

const buildRevenueChart = (labels, values) => {
    if (!revenueChartCanvas.value) {
        return;
    }

    resetChart('revenue');
    chartInstances.revenue = new Chart(revenueChartCanvas.value, {
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
            maintainAspectRatio: false,
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

    resetChart('topClients');
    chartInstances.topClients = new Chart(topClientsChartCanvas.value, {
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
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0,
                },
            },
        },
    });
};

const buildCountryChart = (labels, values) => {
    if (!reservationsByCountryCanvas.value) {
        return;
    }

    resetChart('country');
    chartInstances.country = new Chart(reservationsByCountryCanvas.value, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Reservations',
                    data: values,
                    backgroundColor: '#6366f1',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0,
                },
            },
        },
    });
};

const buildGenderChart = (labels, values) => {
    if (!reservationsByGenderCanvas.value) {
        return;
    }

    resetChart('gender');
    chartInstances.gender = new Chart(reservationsByGenderCanvas.value, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [
                {
                    label: 'Reservations',
                    data: values,
                    backgroundColor: ['#0ea5e9', '#f97316', '#10b981', '#8b5cf6'],
                    borderWidth: 0,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
            },
        },
    });
};

onMounted(async () => {
    const tasks = [
        axios.get(route('analytics.revenue'))
            .then(async (response) => {
                loading.revenue = false;
                errors.revenue = '';
                await nextTick();
                const rows = response.data?.data ?? [];
                buildRevenueChart(
                    rows.map((item) => item.month),
                    rows.map((item) => Number(item.revenue)),
                );
            })
            .catch((error) => {
                loading.revenue = false;
                errors.revenue = 'Failed to load revenue chart.';
                console.error('Failed to load Monthly Revenue chart', error);
            }),
        axios.get(route('analytics.top_clients'))
            .then(async (response) => {
                loading.topClients = false;
                errors.topClients = '';
                await nextTick();
                const rows = response.data?.data ?? [];
                buildTopClientsChart(
                    rows.map((item) => item.name),
                    rows.map((item) => Number(item.reservations_count)),
                );
            })
            .catch((error) => {
                loading.topClients = false;
                errors.topClients = 'Failed to load top clients chart.';
                console.error('Failed to load Top Clients chart', error);
            }),
        axios.get(route('analytics.reservations.by_country'))
            .then(async (response) => {
                loading.country = false;
                errors.country = '';
                await nextTick();
                const rows = response.data?.data ?? [];
                buildCountryChart(
                    rows.map((item) => item.country),
                    rows.map((item) => Number(item.count)),
                );
            })
            .catch((error) => {
                loading.country = false;
                errors.country = 'Failed to load country chart.';
                console.error('Failed to load Reservations By Country chart', error);
            }),
        axios.get(route('analytics.gender_ratio'))
            .then(async (response) => {
                loading.gender = false;
                errors.gender = '';
                await nextTick();
                const rows = response.data?.data ?? [];
                buildGenderChart(
                    rows.map((item) => item.gender),
                    rows.map((item) => Number(item.count)),
                );
            })
            .catch((error) => {
                loading.gender = false;
                errors.gender = 'Failed to load gender chart.';
                console.error('Failed to load Reservations By Gender chart', error);
            }),
    ];

    await Promise.allSettled(tasks);
});

onBeforeUnmount(() => {
    resetChart('revenue');
    resetChart('topClients');
    resetChart('country');
    resetChart('gender');
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
            <ChartCard
                title="Monthly Revenue"
                description="Revenue per reservation month, updated from the analytics API."
                accent="from-sky-500 to-cyan-500"
                :loading="loading.revenue"
                :error="errors.revenue"
            >
                <canvas ref="revenueChartCanvas" class="block h-full w-full"></canvas>
            </ChartCard>

            <ChartCard
                title="Top Clients"
                description="Most active clients by reservation count."
                accent="from-emerald-500 to-teal-500"
                :loading="loading.topClients"
                :error="errors.topClients"
            >
                <canvas ref="topClientsChartCanvas" class="block h-full w-full"></canvas>
            </ChartCard>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <ChartCard
                title="Reservations by Country"
                description="Distribution of reservations grouped by client country."
                accent="from-indigo-500 to-blue-500"
                :loading="loading.country"
                :error="errors.country"
            >
                <canvas ref="reservationsByCountryCanvas" class="block h-full w-full"></canvas>
            </ChartCard>

            <ChartCard
                title="Reservations by Gender"
                description="Distribution of reservations grouped by client gender."
                accent="from-orange-500 to-amber-500"
                :loading="loading.gender"
                :error="errors.gender"
            >
                <canvas ref="reservationsByGenderCanvas" class="block h-full w-full"></canvas>
            </ChartCard>
        </div>
    </div>
</template>
