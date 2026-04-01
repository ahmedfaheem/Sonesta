<script setup>
import Avatar from '@/Components/ui/Avatar.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <div class="rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 to-slate-900 p-5 text-white shadow-lg">
        <div class="flex items-center gap-3">
            <Avatar
                :src="page.props.auth.user?.avatar_url"
                :fallback="page.props.auth.user?.name || 'NA'"
                size="sm"
                :alt="page.props.auth.user?.name || 'User'"
            />
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-white">{{ page.props.auth.user?.name }}</p>
                <p class="mt-1 truncate text-xs text-slate-300">{{ page.props.auth.user?.email }}</p>
            </div>
        </div>

        <div class="mt-5 grid gap-2">
            <Link
                :href="route('profile.edit')"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm font-medium text-white transition hover:bg-white/20"
            >
                Profile
            </Link>
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="inline-flex items-center justify-center rounded-xl border border-white/15 bg-transparent px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10 hover:text-white"
            >
                Sign out
            </Link>
        </div>
    </div>
</template>
