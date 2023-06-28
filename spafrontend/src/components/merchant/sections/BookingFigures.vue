<script setup>
import { toRef } from 'vue';
const props = defineProps({
    title: { type: String, default: ''},
    subtitle: { type: String, default: ''},
    summary: {
        type: Object,
        default: {
            counts: {
                all: 0,
                pending: 0,
                booked: 0,
                completed: 0,
                cancelled:0
            },
            figures: {
                all: 0,
                booked: 0,
                cancelled: 0,
            }
        }
    }
});

toRef(() => props.counts);

const formatFigure = (val) => {
    if (val==undefined) return "₱0.00";
    return "₱" + parseFloat(val).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

</script>

<template>
    <div class="mb-4 mt-8 items-center justify-center">
        <h1 class="font-semibold">{{ title }}</h1>
        <p>{{ subtitle }}</p>
    </div>
    <div class="mb-4">
        <dl class="grid max-w-screen-xl rounded-lg grid-cols-3 gap-8 p-1 mx-auto text-gray-900 sm:grid-cols-2 xl:grid-cols-3 white:text-dark sm:p-2">

            <div class="flex flex-col items-left justify-start bg-gray-100 p-4 rounded-md border-b-4 border-gray-500">
                <dt class="mb-2 text-2xl font-semibold text-gray-800">{{ formatFigure(summary.figures?.all) }}</dt>
                <dd class="text-gray-600 white:text-gray-100 font-semibold">Total</dd>
            </div>

            <div class="flex flex-col items-left justify-start bg-gray-100 p-4 rounded-md border-b-4 border-gray-500">
                <dt class="mb-2 text-2xl font-semibold text-red-500">{{ formatFigure(summary.figures?.cancelled) }}</dt>
                <dd class="text-gray-600 white:text-gray-100 font-semibold">Cancelled</dd>
            </div>

            <div class="flex flex-col items-left justify-start bg-green-200 p-4 rounded-md border-b-4 border-gray-500">
                <dt class="mb-2 text-2xl font-bold text-green-600">{{ formatFigure(summary.figures?.booked) }}</dt>
                <dd class="text-gray-600 white:text-gray-100 font-semibold">Booked</dd>
            </div>

        </dl>
    </div>
</template>