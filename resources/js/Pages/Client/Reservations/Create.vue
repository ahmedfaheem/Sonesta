<script setup>
import Button from '@/Components/ui/Button.vue';
import Card from '@/Components/ui/Card.vue';
import Input from '@/Components/ui/Input.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineOptions({
    layout: ClientLayout,
});

const props = defineProps({
    room: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    accompany_number: 0,
});

const submit = () => {
    form.post(route('client.reservations.checkout', props.room.id));
};
</script>

<template>
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Make Reservation</h1>
            <p class="mt-1 text-sm text-slate-600">Confirm accompany number and continue to Stripe checkout.</p>
        </div>

        <Card class="p-6">
            <div class="grid gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 md:grid-cols-2">
                <p><span class="font-medium text-slate-900">Room:</span> #{{ room.number }}</p>
                <p><span class="font-medium text-slate-900">Floor:</span> {{ room.floor_name || '-' }}</p>
                <p><span class="font-medium text-slate-900">Capacity:</span> {{ room.capacity }}</p>
                <p><span class="font-medium text-slate-900">Price:</span> ${{ room.price_dollars }}</p>
            </div>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Accompany Number</label>
                    <Input v-model="form.accompany_number" type="number" min="0" :max="room.capacity" />
                    <p class="mt-2 text-xs text-slate-500">Maximum allowed is {{ room.capacity }} for this room.</p>
                    <p v-if="form.errors.accompany_number" class="mt-2 text-sm text-red-600">{{ form.errors.accompany_number }}</p>
                    <p v-if="$page.props.errors?.room" class="mt-2 text-sm text-red-600">{{ $page.props.errors.room }}</p>
                    <p v-if="form.errors.stripe" class="mt-2 text-sm text-red-600">{{ form.errors.stripe }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <Button type="submit" :disabled="form.processing">{{ form.processing ? 'Redirecting...' : 'Pay & Confirm Reservation' }}</Button>
                    <Link :href="route('client.rooms.index')">
                        <Button variant="secondary" type="button">Back To Rooms</Button>
                    </Link>
                </div>
            </form>
        </Card>
    </div>
</template>
