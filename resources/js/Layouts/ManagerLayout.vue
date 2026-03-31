<script setup>
import Button from '@/Components/ui/Button.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const page = usePage();
const navigationOpen = ref(false);

const roles = computed(() => page.props.auth.user?.roles ?? []);
const isAdmin = computed(() => roles.value.includes('admin'));
const resourceRoutePrefix = computed(() => (isAdmin.value ? 'admin' : 'manager'));

const dashboardHref = computed(() => (isAdmin.value ? route('admin.dashboard') : route('manager.dashboard')));
const dashboardLabel = computed(() => (isAdmin.value ? 'Admin Dashboard' : 'Dashboard'));

const navigation = computed(() => [
    { label: dashboardLabel.value, href: dashboardHref.value, active: route().current(isAdmin.value ? 'admin.dashboard' : 'manager.dashboard') },
    { label: 'Receptionists', href: route(`${resourceRoutePrefix.value}.receptionists.index`), active: route().current('manager.receptionists.*') || route().current('admin.receptionists.*') },
    { label: 'Clients', href: route(`${resourceRoutePrefix.value}.clients.index`), active: route().current('manager.clients.*') || route().current('admin.clients.*') },
    { label: 'Floors', href: route(`${resourceRoutePrefix.value}.floors.index`), active: route().current('manager.floors.*') || route().current('admin.floors.*') },
    { label: 'Rooms', href: route(`${resourceRoutePrefix.value}.rooms.index`), active: route().current('manager.rooms.*') || route().current('admin.rooms.*') },
]);

const closeNavigation = () => {
    navigationOpen.value = false;
};

const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        closeNavigation();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <Head :title="$page.component.split('/').slice(-1)[0]" />

    <div class="min-h-screen bg-slate-100">
        <div class="flex min-h-screen">
            <div
                v-if="navigationOpen"
                class="fixed inset-0 z-30 bg-slate-950/30 backdrop-blur-[1px] lg:hidden"
                @click="closeNavigation"
            />

            <aside
                class="fixed inset-y-0 left-0 z-40 w-72 border-r border-slate-200 bg-white px-6 py-8 transition lg:static"
                :class="navigationOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Sonesta</p>
                        <h1 class="mt-2 text-2xl font-semibold text-slate-950">Manager Tools</h1>
                    </div>
                    <button class="lg:hidden" @click="closeNavigation">Close</button>
                </div>

                <nav class="mt-10 space-y-2">
                    <Link
                        v-for="item in navigation"
                        :key="item.label"
                        :href="item.href"
                        class="flex items-center rounded-2xl px-4 py-3 text-sm font-medium transition"
                        :class="item.active ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950'"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="mt-10 rounded-2xl bg-slate-950 p-5 text-white">
                    <p class="text-sm font-medium">{{ page.props.auth.user?.name }}</p>
                    <p class="mt-1 text-xs text-slate-300">{{ page.props.auth.user?.email }}</p>
                    <Link :href="route('logout')" method="post" as="button" class="mt-4 text-sm font-medium text-white/90">
                        Sign out
                    </Link>
                </div>
            </aside>

            <div class="flex-1">
                <header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Sonesta Management System</p>
                            <p class="text-lg font-semibold text-slate-950">Floors &amp; Rooms</p>
                        </div>

                        <Button variant="secondary" class="lg:hidden" @click="navigationOpen = !navigationOpen">
                            Menu
                        </Button>
                    </div>
                </header>

                <main class="px-4 py-8 sm:px-8">
                    <div
                        v-if="page.props.flash?.success"
                        class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
                    >
                        {{ page.props.flash.success }}
                    </div>

                    <div
                        v-if="page.props.flash?.error"
                        class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
                    >
                        {{ page.props.flash.error }}
                    </div>

                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
