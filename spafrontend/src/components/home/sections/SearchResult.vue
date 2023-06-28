<script setup>
//https://ordinarycoders.com/blog/article/17-tailwindcss-cards
import { computed } from 'vue';
const props = defineProps({
    collection: { type: Array, default: [] },
    title: { type: String, default: 'Your Search'},
    subtitle: { type: String, default: 'Lorem ipsum, sung tung xi..'},
})

const emit = defineEmits(['select']);

const select = (item) => {
    // console.log("Selected Product:", item);
    emit('view-details', item );
}

const resultItems = computed(() => {
    return props.collection.length;
});

</script>

<template>
    <div class="mb-4 items-center justify-center">
        <h1 class="font-semibold text-3xl">{{ title }}</h1>
        <p>{{ subtitle }}</p>
    </div>
    
    <div class="mb-4" v-if="resultItems==0">
        No result found...
    </div>

    <div class="truncate cursor-pointer mb-4 flex flex-col rounded-lg bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 md:max-w-3x1 md:flex-row" v-for="item in collection" @click="select(item)">
        <img
            class="h-96 w-full rounded-t-lg object-cover md:h-auto md:w-48 md:rounded-none md:rounded-l-lg"
            :src="item.img"
            :alt="item.name" />
        <div class="flex flex-col justify-start p-6">
            <p class="text-sm text-neutral-500 text-bold dark:text-neutral-300">
                {{ item.listing_category.name }}
            </p>
            <p class="text-xs text-neutral-500 dark:text-neutral-300 text-ellipsis overflow-hidden">
                {{ item.listing_category.address }}
            </p>
            <h5 class="mb-2 mt-2 text-xl font-medium text-neutral-800 dark:text-neutral-50 text-ellipsis overflow-hidden">
                {{ item.name }}
            </h5>
            <p class="mb-4 text-base text-neutral-600 dark:text-neutral-200 text-ellipsis overflow-hidden">
                {{ item.description }}
            </p>
            <div class="pt-4 pb-2">
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">₱{{ item.rate }} per Night</span>
            </div>
        </div>
    </div>
</template>