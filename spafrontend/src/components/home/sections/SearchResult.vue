<script setup>
//https://ordinarycoders.com/blog/article/17-tailwindcss-cards
import { computed } from 'vue';
const props = defineProps({
    collection: { type: Array, default: null },
    title: { type: String, default: 'Your Search'},
    subtitle: { type: String, default: ''},
})

const emit = defineEmits(['select']);

const select = (item) => {
    emit('cview-details', item );
}

const baseRate = computed( () => { 
    return '₱'+ moneyFormat(form.value.rate);
});

const moneyFormat = (v) =>{
    try {
        if (isNaN(v)) throw "Not a Number" 
        const fv = parseFloat(v);
        return fv.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    } catch (error) {
        return "0.00"
    }
}

const resultItems = computed(() => {
    if (props.collection == null) return -1;
    return props.collection.length;
});

const getRating = (reviews) => {
    console.log("reviews: ", reviews);
    if (reviews.length ==0) return 0;
    let sumRating = 0;
    const rl = reviews.length;
    reviews.forEach( (item, index) => {
        sumRating+= item.rating;
    });
    return sumRating/rl;
}

</script>

<template>
    <div class="mb-4 items-center justify-center">
        <h1 class="font-semibold text-xl">{{ title }}</h1>
        <p>{{ subtitle }}</p>
    </div>
    
    <div class="mb-4" v-if="resultItems==0">
        No result found... {{ resultItems }}
    </div>

    <div class="relative truncate cursor-pointer mb-4 flex flex-col rounded-lg bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 md:max-w-3x1 md:flex-row" v-for="item in collection" @click="select(item)">
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
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">₱{{ moneyFormat(item.rate) }} per Night</span>
            </div>
        </div>

        <div class="absolute top-1 left-1 p-1 bg-gray-100/50 rounded-md font-semibold text-sm items-center flex">
            <span class="mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3">
                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                </svg>
            </span>
            {{ getRating(item.product_reviews) }}/5
        </div>
    </div>
</template>