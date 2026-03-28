<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(15,23,42,0.08),_transparent_35%),linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-6xl items-center justify-center">
            <div class="grid w-full items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="hidden lg:block">
                    <Link href="/" class="inline-flex items-center gap-4">
                        <ApplicationLogo class="h-16 w-16 fill-current text-slate-900" />
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Sonesta</p>
                            <p class="mt-1 text-3xl font-semibold tracking-tight text-slate-950">Hotel Management</p>
                        </div>
                    </Link>

                    <div class="mt-10 max-w-xl space-y-5">
                        <h1 class="text-5xl font-semibold leading-tight tracking-tight text-slate-950">
                            A cleaner guest experience starts with a better account flow.
                        </h1>
                        <p class="text-base leading-7 text-slate-600">
                            Secure sign in, client onboarding, and approval-ready registration built on Laravel Breeze and Inertia.
                        </p>
                    </div>
                </div>

                <div class="w-full">
                    <div class="mb-6 flex justify-center lg:hidden">
                        <Link href="/" class="inline-flex items-center gap-3">
                            <ApplicationLogo class="h-14 w-14 fill-current text-slate-900" />
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Sonesta</p>
                                <p class="text-xl font-semibold text-slate-950">Hotel Management</p>
                            </div>
                        </Link>
                    </div>

                    <div class="w-full">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
