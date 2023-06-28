<script setup>
//https://ordinarycoders.com/blog/article/17-tailwindcss-cards
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
    collection: { type: Array, default: [] },
    title: { type: String, default: 'Explore Mindanao'},
    subtitle: { type: String, default: 'These popular destinations have a lot to offer'},
});

const search = (param) => {
    console.log("Search query: ", param);

    const val = (param.query !== undefined) ? encodeURIComponent(param.query.toLowerCase()):'';  
    const start = ''; //encodeURIComponent(param.start)  
    const end = ''; // encodeURIComponent(param.end)  
    router.push({ name: 'ListingsSearch', query: { query: val, start: start, end: end } })
}

</script>
<template>
    <div class="mb-4 mt-8 items-center justify-center">
        <h1 class="font-semibold text-3xl">{{ title }}</h1>
        <p>{{ subtitle }}</p>
    </div>
    
    <div class="mb-4 grid grid-cols-4 sm:grid-cols-4 md:grid-cols-7 lg:grid-cols-7 xl:grid-cols-7 gap-5">
        <!--Card 1-->
        <div @click="search(item)" class="rounded overflow-hidden cursor-pointer" v-for="item in collection">
            <img class="w-full rounded-lg object-contain" :src="item.img" alt="" />
            
            <div class="py-1">
                <h1 class="font-semibold">{{ item.name }}</h1>
            </div>
        </div>
        
    </div>

</template>