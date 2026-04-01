<script setup>
import AuthCard from '@/Components/Auth/AuthCard.vue';
import Button from '@/Components/ui/Button.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount } from 'vue';

defineOptions({
    layout: GuestLayout,
});

const goToLogin = () => {
    router.post(route('logout'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('login'));
        },
    });
};

const handlePopState = () => {
    goToLogin();
};

onMounted(() => {
    window.addEventListener('popstate', handlePopState);
});

onBeforeUnmount(() => {
    window.removeEventListener('popstate', handlePopState);
});
</script>

<template>
    <Head title="Pending Approval" />

    <AuthCard
        eyebrow="Pending Approval"
        title="Your account is waiting for approval"
        description="Your registration was submitted successfully. An administrator will review your profile before sign-in is enabled."
    >
        <div class="space-y-6">
            <div class="rounded-3xl bg-[linear-gradient(135deg,_#0f172a_0%,_#1e293b_100%)] p-8 text-white">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-white/10 text-4xl">
                    ⏳
                </div>
                <p class="mt-6 text-center text-sm leading-7 text-slate-300">
                    You will be able to log in after your client account has been approved.
                </p>
            </div>

            <Button class="w-full" @click="goToLogin">Back to login</Button>
        </div>
    </AuthCard>
</template>
