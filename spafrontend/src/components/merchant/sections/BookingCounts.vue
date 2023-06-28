<script setup>
import { toRef } from 'vue';
const props = defineProps({
    collection: { type: Array, default: [] },
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
toRef(() => props.collection);

</script>
<template>
    <div class="mb-4 mt-8 items-center justify-center">
        <h1 class="font-semibold">{{ title }}</h1>
        <p>{{ subtitle }}</p>
    </div>
    <div class="mb-4">
        <dl class="grid max-w-screen-xl rounded-lg grid-cols-5 gap-8 p-1 mx-auto text-gray-900 sm:grid-cols-2 xl:grid-cols-5 white:text-dark sm:p-2">
            <template v-if="collection.length>0">
                <div class="flex flex-col items-center justify-center" v-for="item in collection">
                    <dt class="mb-2 text-2xl font-semibold">{{ item?.value }}</dt>
                    <dd class="text-gray-600 white:text-gray-100">{{ item?.label }}</dd>
                </div>
            </template>

            <div class="flex flex-col items-center justify-center">
                <dt class="mb-2 text-2xl font-semibold">{{ summary.counts?.all }}</dt>
                <dd class="text-gray-600 white:text-gray-100">All</dd>
            </div>

            <div class="flex flex-col items-center justify-center">
                <dt class="mb-2 text-2xl font-semibold">{{ summary.counts?.pending }}</dt>
                <dd class="text-gray-600 white:text-gray-100">Pending</dd>
            </div>

            <div class="flex flex-col items-center justify-center">
                <dt class="mb-2 text-2xl font-semibold">{{ summary.counts?.cancelled }}</dt>
                <dd class="text-gray-600 white:text-gray-100">Cancelled</dd>
            </div>

            <div class="flex flex-col items-center justify-center">
                <dt class="mb-2 text-2xl font-semibold">{{ summary.counts?.booked }}</dt>
                <dd class="text-gray-600 white:text-gray-100">Booked</dd>
            </div>

            <div class="flex flex-col items-center justify-center">
                <dt class="mb-2 text-2xl font-semibold">{{ summary.counts?.completed }}</dt>
                <dd class="text-gray-600 white:text-gray-100">Completed</dd>
            </div>

        </dl>
    </div>
</template>