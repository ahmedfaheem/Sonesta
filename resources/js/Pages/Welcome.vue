<script setup>
import FeatureCard from '@/Components/Public/FeatureCard.vue';
import Footer from '@/Components/Public/Footer.vue';
import HeroSection from '@/Components/Public/HeroSection.vue';
import Navbar from '@/Components/Public/Navbar.vue';
import RoomCard from '@/Components/Public/RoomCard.vue';
import StatsSection from '@/Components/Public/StatsSection.vue';
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
    featuredRooms: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({
            totalRooms: 0,
            totalReservations: 0,
            averagePrice: '0.00',
        }),
    },
});

const features = [
    {
        icon: '🛎',
        title: 'Easy Booking',
        description: 'A streamlined flow from browsing rooms to checkout with instant confirmations.',
    },
    {
        icon: '🧩',
        title: 'Role Management',
        description: 'Purpose-built permissions keep admins, managers, and receptionists aligned.',
    },
    {
        icon: '🔒',
        title: 'Secure Payment',
        description: 'Stripe-ready payments ensure safe, verified checkout for every reservation.',
    },
    {
        icon: '⚡',
        title: 'Real-time Availability',
        description: 'Rooms stay accurate with live occupancy and reservation snapshots.',
    },
];

const testimonials = [
    {
        name: 'Layla Hassan',
        role: 'Guest Experience Manager',
        feedback: 'The booking journey feels premium and fast. Guests get instant clarity on rooms and pricing.',
        image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=200&q=80',
    },
    {
        name: 'Omar El-Sayed',
        role: 'Front Desk Lead',
        feedback: 'Approvals, reservations, and room status stay in sync across the entire team.',
        image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80',
    },
    {
        name: 'Nour Ali',
        role: 'Operations Director',
        feedback: 'We finally have real-time visibility into occupancy and revenue trends.',
        image: 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=200&q=80',
    },
];

const roomImages = [
    'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80',
    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1200&q=80',
    'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=80',
    'https://images.unsplash.com/photo-1502672023488-70e25813eb80?auto=format&fit=crop&w=1200&q=80',
    'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=1200&q=80',
    'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=1200&q=80',
];

const lobbyImage =
    'https://images.unsplash.com/photo-1508610048659-a06b669e3321?auto=format&fit=crop&w=1600&q=80';

const getRoomImage = (room, index) => {
    if (room.image_url) {
        return room.image_url;
    }

    return roomImages[index % roomImages.length];
};
</script>

<template>
    <Head title="Sonesta" />

    <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(15,23,42,0.14),_transparent_45%),linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)] text-slate-900">
        <Navbar :can-login="canLogin" :can-register="canRegister" />
        <HeroSection :can-login="canLogin" :can-register="canRegister" />
        <StatsSection :stats="stats" />

        <section id="featured" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-600">Featured Rooms</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Handpicked spaces for every stay</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Top-performing rooms selected from live reservation data and guest preferences.
                    </p>
                </div>
                <Link v-if="canRegister" :href="route('register')">
                    <Button variant="secondary">Book a room</Button>
                </Link>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <RoomCard
                    v-for="(room, index) in featuredRooms"
                    :key="room.id"
                    :room="room"
                    :image-url="getRoomImage(room, index)"
                    badge="Featured"
                />
            </div>

            <Card v-if="featuredRooms.length === 0" class="mt-6">
                <div class="p-8 text-center">
                    <p class="text-lg font-semibold text-slate-950">No featured rooms yet</p>
                    <p class="mt-2 text-sm text-slate-600">Add reservations to highlight your most popular rooms.</p>
                </div>
            </Card>
        </section>

        <section id="features" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-10 max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-700">Features</p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Everything you need for premium hospitality</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">
                    Operational tools built for hotel teams, paired with a booking experience guests love.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
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
            <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="overflow-hidden rounded-3xl">
                    <img :src="lobbyImage" alt="Hotel lobby" class="h-full w-full object-cover" />
                </div>
                <div class="space-y-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">About Sonesta</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Designed to deliver five-star operations</h2>
                    <p class="text-sm leading-7 text-slate-600">
                        From guest onboarding to reservation management, every module keeps teams aligned and guests delighted.
                    </p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Card class="border border-white/60 bg-white/80">
                            <div class="p-5">
                                <p class="text-sm font-semibold text-slate-900">Verified Guests</p>
                                <p class="mt-2 text-sm text-slate-600">Approval workflow ensures trusted bookings.</p>
                            </div>
                        </Card>
                        <Card class="border border-white/60 bg-white/80">
                            <div class="p-5">
                                <p class="text-sm font-semibold text-slate-900">Unified Operations</p>
                                <p class="mt-2 text-sm text-slate-600">Roles, rooms, and revenue in one view.</p>
                            </div>
                        </Card>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-700">Latest Rooms</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Fresh arrivals ready for booking</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Real room data is loaded from the backend and displayed with nightly pricing in dollars.
                    </p>
                </div>
                <Link v-if="canRegister" :href="route('register')">
                    <Button variant="secondary">Create account to book</Button>
                </Link>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <RoomCard
                    v-for="(room, index) in rooms"
                    :key="room.id"
                    :room="room"
                    :image-url="getRoomImage(room, index)"
                />
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
                            <div class="flex items-center gap-3">
                                <img :src="item.image" :alt="item.name" class="h-10 w-10 rounded-full object-cover" />
                                <div>
                                    <p class="text-base font-semibold text-slate-950">{{ item.name }}</p>
                                    <p class="text-sm text-slate-500">{{ item.role }}</p>
                                </div>
                            </div>
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
                        <h2 class="mt-3 text-4xl font-semibold tracking-tight">Start Your Journey Today</h2>
                        <p class="mt-4 max-w-2xl text-sm leading-8 text-slate-300">
                            Launch a modern booking experience for guests and a real-time control center for your team.
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
