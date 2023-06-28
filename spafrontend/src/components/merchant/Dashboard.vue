<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from "axios";
import { useAuthStore } from '../../stores/auth';
import BookingCounts from './sections/BookingCounts.vue';

const authStore = useAuthStore();

const primary = ref({});
const bookingCountItems = ref([])

onMounted(() => {
    authStore.getToken();
    fetchData().catch(error => {
        error.message; 
        console.log("fetchData Error: ", error.message);
    });

    bookingCountItems.value = [
        { label: 'Pending', value: 4 },
        { label: 'Booked', value: 54 },
        { label: 'Completed', value: 60 },
        { label: 'Cancelled', value: 6 },
    ];
});

const fetchData = async () => {
    try {
        const response = await axios.get('/api/partner/profile');
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        
        primary.value = await response.data.data;
        console.log("Primary: ",  primary.value);
        console.log("Listing: ",  primary.value.listings);
        
    } catch (error) {
        console.log("fetchData" ,error);
    }
}

const listingCount = computed(() => {
    if (primary.value && primary.value['listings'] != undefined) {
        return primary.value.listings.length;
    }
    return 0;
});


const productsCount = computed(() => {
    if (primary.value && primary.value['listings'] != undefined) {
        let count = 0;
        primary.value.listings.forEach( (item, index) => {            
            count += item.products.length;
        });
        return count;
    }

    return 0;
});

</script>
<template>

<div class="grid gap-3 xl:grid-cols-3 xs:grid-cols-1 sm:grid-cols-1">
    <!-- card -->
    <div class="card mt-6 bg-[#56BDF0] rounded p-2">
        <div class="card-body flex items-center">
            <div class="px-3 py-2 rounded text-white mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <div class="flex flex-col">
                <h1 class="font-semibold"><span class="num-2"></span> {{ primary.name }}</h1>
                <p class="text-xs"><span class="num-2">Address: </span>{{ primary.bus_address }}</p>
            </div>

        </div>
    </div>
    <!-- end card -->

    <!-- card -->
    <div class="card mt-6 bg-[#2795B9] rounded p-2">
        <div class="card-body flex items-center">
            <div class="px-3 py-2 rounded text-white mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </div>

            <div class="flex flex-col">
                <h1 class="font-semibold"><span class="num-2"></span>{{ listingCount  }}</h1>
                <p class="text-xs">Number of Properties</p>
            </div>

        </div>
    </div>
    <!-- end card -->

    <!-- card -->
    <div class="card mt-6 bg-[#F7BE38] rounded p-2">
        <div class="card-body flex items-center">
            <div class="px-3 py-2 rounded text-white mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
            </div>

            <div class="flex flex-col">
                <h1 class="font-semibold"><span class="num-2"></span>{{ productsCount }}</h1>
                <p class="text-xs">Number of Products/Units</p>
            </div>

        </div>
    </div>
    <!-- end card -->
</div>

<div class="mb-4">
    <!-- <BookingCounts :collection="bookingCountItems" :title="'Booking Transaction Count'" :subtitle="'Count Summary'" /> -->
</div>
</template>