<script setup>
import Footer from '@/Components/Public/Footer.vue';
import Navbar from '@/Components/Public/Navbar.vue';
import HeroSection from '@/Components/Public/HeroSection.vue';
import FeatureCard from '@/Components/Public/FeatureCard.vue';
import RoomCard from '@/Components/Public/RoomCard.vue';
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
        default: false,
    },
    canRegister: {
        type: Boolean,
        default: false,
    },
    rooms: {
        type: Array,
        default: () => [],
    },
});

const features = [
    {
        icon: '🛏',
        title: 'Room Management',
        description: 'Track rooms, capacity, pricing, and operational readiness from one centralized workspace.',
    },
    {
        icon: '📅',
        title: 'Reservation System',
        description: 'Organize reservations with a smoother guest flow across registration, approval, and booking.',
    },
    {
        icon: '🧩',
        title: 'Role-Based Access',
        description: 'Separate access for admins, managers, receptionists, and clients without cluttered workflows.',
    },
    {
        icon: '💳',
        title: 'Secure Payments',
        description: 'Stripe-ready structure keeps the platform prepared for secure guest payment experiences.',
    },
    {
        icon: '📈',
        title: 'Analytics Dashboard',
        description: 'Surface team activity, room usage, and operational visibility with live admin reporting.',
    },
    {
        icon: '🔐',
        title: 'Approval Workflow',
        description: 'Client accounts can stay pending until reviewed, protecting booking access and staff control.',
    },
];

const steps = [
    { number: '01', title: 'Register', description: 'Guests create an account and submit their profile information.' },
    { number: '02', title: 'Get Approved', description: 'Admins review the request and activate the client account.' },
    { number: '03', title: 'Book Rooms', description: 'Approved clients explore room options and reserve their stay.' },
    { number: '04', title: 'Manage Reservations', description: 'Staff monitor bookings, rooms, and guest needs in one system.' },
];

const testimonials = [
    {
        name: 'Mona Adel',
        role: 'Sonesta Operations Lead',
        feedback: 'This platform finally gives our team one clear workflow for rooms, staff, and guest onboarding.',
    },
    {
        name: 'Karim Youssef',
        role: 'Front Desk Supervisor',
        feedback: 'The receptionist flow feels practical. It is fast enough for daily check-ins and clear enough for training.',
    },
    {
        name: 'Sara Hassan',
        role: 'Property Manager',
        feedback: 'The separation between admin control and guest accounts makes the whole system easier to manage safely.',
    },
];
</script>

<template>
    <Head title="Sonesta" />

    <div class="min-h-screen bg-[linear-gradient(180deg,_#f8fafc_0%,_#e2e8f0_100%)] text-slate-900">
        <Navbar :can-login="canLogin" :can-register="canRegister" />
        <HeroSection :can-login="canLogin" :can-register="canRegister" />

        <section id="features" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-10 max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-700">Features</p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Purpose-built for hotel operations</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">
                    Everything on the platform is focused on hotel workflows instead of generic admin tooling.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <FeatureCard
                    v-for="feature in features"
                    :key="feature.title"
                    :icon="feature.icon"
                    :title="feature.title"
                    :description="feature.description"
                />
            </div>
        </section>

        <section id="about" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
                <Card class="bg-slate-950 text-white">
                    <div class="space-y-5 p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-300">How It Works</p>
                        <h2 class="text-3xl font-semibold tracking-tight">A simple flow for guests and hotel staff</h2>
                        <p class="text-sm leading-7 text-slate-300">
                            Start with registration, move through approval, then manage reservations and room operations with clarity.
                        </p>
                    </div>
                </Card>

                <div class="grid gap-4 sm:grid-cols-2">
                    <Card v-for="step in steps" :key="step.number" class="transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                        <div class="p-6">
                            <p class="text-sm font-semibold text-sky-700">{{ step.number }}</p>
                            <h3 class="mt-3 text-xl font-semibold text-slate-950">{{ step.title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ step.description }}</p>
                        </div>
                    </Card>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-700">Latest Rooms</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Preview rooms available in the system</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Real room data is loaded from the backend and displayed with nightly pricing in dollars.
                    </p>
                </div>
                <Link v-if="canRegister" :href="route('register')">
                    <Button variant="secondary">Create account to book</Button>
                </Link>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <RoomCard v-for="room in rooms" :key="room.id" :room="room" />
            </div>

            <Card v-if="rooms.length === 0" class="mt-6">
                <div class="p-8 text-center">
                    <p class="text-lg font-semibold text-slate-950">No rooms available yet</p>
                    <p class="mt-2 text-sm text-slate-600">Add rooms from the Sonesta management panel to showcase them here.</p>
                </div>
            </Card>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-10 max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Testimonials</p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Trusted by hotel teams focused on smoother operations</h2>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <Card v-for="item in testimonials" :key="item.name" class="h-full">
                    <div class="p-6">
                        <p class="text-sm leading-8 text-slate-600">“{{ item.feedback }}”</p>
                        <div class="mt-6">
                            <p class="text-base font-semibold text-slate-950">{{ item.name }}</p>
                            <p class="text-sm text-slate-500">{{ item.role }}</p>
                        </div>
                    </div>
                </Card>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <Card class="overflow-hidden bg-[linear-gradient(135deg,_#0f172a_0%,_#1e293b_100%)] text-white shadow-xl">
                <div class="grid gap-8 p-8 lg:grid-cols-[1fr_auto] lg:items-center lg:p-12">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-300">Start Today</p>
                        <h2 class="mt-3 text-4xl font-semibold tracking-tight">Start managing your hotel today</h2>
                        <p class="mt-4 max-w-2xl text-sm leading-8 text-slate-300">
                            Launch a cleaner operational flow for staff and clients with one unified Sonesta platform.
                        </p>
                    </div>

                    <Link v-if="canRegister" :href="route('register')">
                        <Button size="lg">Register Now</Button>
                    </Link>
                </div>
            </Card>
        </section>

        <Footer />
    </div>
</template>
