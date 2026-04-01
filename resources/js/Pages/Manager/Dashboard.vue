<script setup>
import Badge from '@/Components/ui/Badge.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import Chart from 'chart.js/auto';

defineOptions({
    layout: ManagerLayout,
});

const page = usePage();

const isAdmin = computed(() => (page.props.auth.user?.roles ?? []).includes('admin'));

const topClientsChartCanvas = ref(null);

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
                    label: 'Reservations',
                    data: values,
                    backgroundColor: '#38bdf8',
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
        const response = await axios.get(route('api.analytics.top_clients'));

        buildTopClientsChart(
            response.data.data.map((item) => item.name),
            response.data.data.map((item) => item.reservations_count),
        );
    } catch (error) {
        // ignore chart errors for now
    }
});

const modules = [
    {
        title: 'Floors',
        description: 'Create and manage hotel floors. Floor numbers are generated automatically and ownership is enforced.',
        indexRoute: 'manager.floors.index',
        createRoute: 'manager.floors.create',
        accent: 'from-sky-500 to-cyan-500',
        points: [
            'Managers can only access their own floors',
            'Admins can review all floor records',
            'Floors with rooms cannot be deleted',
        ],
    },
    {
        title: 'Rooms',
        description: 'Manage room inventory, capacity, and pricing while keeping rooms tied to their assigned floor owner.',
        indexRoute: 'manager.rooms.index',
        createRoute: 'manager.rooms.create',
        accent: 'from-emerald-500 to-teal-500',
        points: [
            'Room numbers must be unique and at least 4 digits',
            'Prices are entered in dollars and stored in cents',
            'Rooms with reservations cannot be deleted',
        ],
    },
];
</script>

<template>
    <div class="space-y-8">
        <section class="overflow-hidden rounded-[2rem] bg-slate-950 text-white">
            <div class="grid gap-8 px-6 py-8 lg:grid-cols-[1.4fr_0.8fr] lg:px-10 lg:py-10">
                <div>
                    <Badge variant="success">Manager Workspace</Badge>
                    <h1 class="mt-4 max-w-2xl text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                        Floors and rooms operations in one place.
                    </h1>
                    <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                        Use this dashboard to move between floor setup and room inventory quickly while keeping manager ownership and deletion rules intact.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link :href="route('manager.floors.index')">
                            <Button>Manage floors</Button>
                        </Link>
                        <Link :href="route('manager.rooms.index')">
                            <Button variant="secondary">Manage rooms</Button>
                        </Link>
                    </div>
                </div>

                <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                    <p class="text-sm font-semibold text-white">Access policy</p>
                    <ul class="mt-4 space-y-3 text-sm text-slate-300">
                        <li>Managers only see and edit their own floors and rooms.</li>
                        <li>Admins can open the same screens with full visibility.</li>
                        <li>Room ownership always follows the selected floor.</li>
                    </ul>

                    <div class="mt-6 rounded-2xl bg-white/10 px-4 py-3 text-sm text-slate-200">
                        Signed in as <span class="font-semibold text-white">{{ page.props.auth.user?.name }}</span>
                        <span v-if="isAdmin" class="text-emerald-300"> with admin visibility.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <Card
                v-for="module in modules"
                :key="module.title"
                class="overflow-hidden border-0 shadow-sm"
            >
                <div class="h-2 w-full bg-gradient-to-r" :class="module.accent" />

                <div class="space-y-6 p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-slate-950">{{ module.title }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                {{ module.description }}
                            </p>
                        </div>
                        <Badge>{{ module.title }}</Badge>
                    </div>

                    <ul class="space-y-3 text-sm text-slate-700">
                        <li v-for="point in module.points" :key="point" class="flex gap-3">
                            <span class="mt-1 h-2.5 w-2.5 rounded-full bg-slate-900" />
                            <span>{{ point }}</span>
                        </li>
                    </ul>

                    <div class="flex flex-wrap gap-3">
                        <Link :href="route(module.indexRoute)">
                            <Button>Open {{ module.title.toLowerCase() }}</Button>
                        </Link>
                        <Link :href="route(module.createRoute)">
                            <Button variant="secondary">Create {{ module.title.slice(0, -1).toLowerCase() }}</Button>
                        </Link>
                    </div>
                </div>
            </Card>

        </section>

        <section>
            <Card class="overflow-hidden border-0 shadow-sm">
                <div class="h-2 w-full bg-gradient-to-r from-sky-500 to-cyan-500" />
                <div class="space-y-5 p-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-950">Top Clients</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Active clients ranked by reservation volume, loaded from the analytics API.
                        </p>
                    </div>
                    <div class="h-72">
                        <canvas ref="topClientsChartCanvas" class="h-full w-full"></canvas>
                    </div>
                </div>
            </Card>
        </section>
    </div>
</template>
