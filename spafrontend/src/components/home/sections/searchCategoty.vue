<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const categories = ref([]);

onMounted(() => {

    categories.value = [
        { img: 'http://localhost:8000/uploads/categories/rooms.jpg', name: 'Rooms', query: 'room' },
        { img: 'http://localhost:8000/uploads/categories/beach-front.jpg', name: 'Beach Front', query: 'beach' },
        { img: 'http://localhost:8000/uploads/categories/bed-n-break.jpg', name: 'Bed & Breakfast', query: 'Bed & Breakfast' },
        { img: 'http://localhost:8000/uploads/categories/island.jpg', name: 'Island', query: 'island' },
        { img: 'http://localhost:8000/uploads/categories/city.jpg', name: 'City', query: 'city' },
        { img: 'http://localhost:8000/uploads/categories/camping.jpg', name: 'Camping', query: 'camping' },
        { img: 'http://localhost:8000/uploads/categories/mountain-resort.jpg', name: 'Mountain Resort', query: 'mountain' },
        { img: 'http://localhost:8000/uploads/categories/pools.jpg', name: 'Pools', query: 'pool' },
        { img: 'http://localhost:8000/uploads/categories/homes.jpg', name: 'Homes', query: 'home' },
        { img: 'http://localhost:8000/uploads/categories/tiny-house.jpg', name: 'Tiny House', query: 'tiny' },
        { img: 'http://localhost:8000/uploads/categories/tropical.jpg', name: 'Tropical', query: 'tropical' },
    ];
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
<div class="p-2 inline-grid gap-2 grid-cols-12 xs:grid-cols-4 w-full text-sm">
    <div @click="search(item)" v-for="item in categories" class="h-[80px] max-w-[48px] sm:w-[38px] mx-auto items-center content-center text-center hover:border-b-2 border-gray-600 cursor-pointer">
        <img :src="item.img" width="24" height="24" class="object-cover w-full" />
        <p class="text-xs">{{ item.name }}</p>
    </div>
    <router-link :to="{ name: 'Listings'}">
        <div class="h-[80px] max-w-[48px] sm:w-[38px] mx-auto items-center content-center text-center hover:border-b-2 border-gray-600 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-xs">more</p>
        </div>
    </router-link>
</div>
</template>