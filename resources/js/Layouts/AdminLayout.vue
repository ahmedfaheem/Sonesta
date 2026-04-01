<script setup>
import SidebarUserCard from '@/Components/Layout/SidebarUserCard.vue';
import Button from '@/Components/ui/Button.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const page = usePage();
const navigationOpen = ref(false);

const componentMatches = (prefixes) => {
    const component = page.component || '';

    return prefixes.some((prefix) => component.startsWith(prefix));
};

const navigation = computed(() => [
    { label: 'Dashboard', href: route('admin.dashboard'), active: componentMatches(['Admin/Dashboard']) },
    { label: 'Managers', href: route('admin.managers.index'), active: componentMatches(['Admin/Managers/']) },
    { label: 'Receptionists', href: route('admin.receptionists.index'), active: componentMatches(['Admin/Receptionists/']) },
    { label: 'Clients', href: route('admin.clients.index'), active: componentMatches(['Admin/Clients/']) },
    { label: 'Reservations', href: route('admin.reservations.index'), active: componentMatches(['Admin/Reservations/']) },
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
                        <h1 class="mt-2 text-2xl font-semibold text-slate-950">Admin Panel</h1>
                    </div>
                    <button class="lg:hidden" @click="closeNavigation">Close</button>
                </div>

                <div class="mt-8">
                    <SidebarUserCard />
                </div>

                <nav class="mt-8 space-y-2">
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

            </aside>

            <div class="flex-1">
                <header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Sonesta Management System</p>
                            <p class="text-lg font-semibold text-slate-950">Administration</p>
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
