<script setup>
import { onMounted, computed, watch, ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import axios from "axios";
import SideBar from '../components/merchant/sections/sidebar.vue';
import BookingCounts from '../components/merchant/sections/BookingCounts.vue';
import BookingFigures from '../components/merchant/sections/BookingFigures.vue';

const router = useRouter();
const authStore = useAuthStore();

const showMain = ref(true);
const componentTitle = ref('');

const primary = ref({});
const bookingCountItems = ref([]);
const product_ids = ref([]);
const transactionCollection = ref([]);
const totals = ref({});

onMounted(() => {
    showMain.value= router.currentRoute.value.name==='Partner';
    componentTitle.value = router.currentRoute.value.meta.title;
    
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

watch(() => transactionCollection.value, (c, b) => { 
    console.log("pids b4", b);
    console.log("pids nw", c);
    totals.value = {
        counts: {
            all: c.length,
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

    if (c && c.length>0){
        transactionCollection.value.forEach((item, index) =>{
            console.log("booking_status: ", item.booking_status, " payment status: ", item.payment_status, " Total: ", item.total);
            //Counts
            if (item.booking_status==='Pending') totals.value.counts.pending +=1;
            if (item.booking_status==='Completed') totals.value.counts.completed +=1;
            if (item.booking_status==='Cancelled') totals.value.counts.cancelled +=1;
            if (item.booking_status==='Booked') totals.value.counts.booked +=1;

            //Figures
            totals.value.figures.all += parseFloat(item.total);
            if (item.payment_status==='Payment Completed') totals.value.figures.booked += parseFloat(item.total);
            if (item.payment_status==='Payment Cancelled') totals.value.figures.cancelled += parseFloat(item.total);
        });
    }
    console.log("totals:", totals.value);
});

const fetchData = async () => {
    try {
        const response = await axios.get('/api/partner/profile');
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        primary.value = await response.data.data;
        getProducts();
        fetchTransactionData();
        console.log("Primary: ",  primary.value);
        console.log("Listing: ",  primary.value.listings);
        console.log("Product Ids: ",  product_ids.value);
    } catch (error) {
        console.log("fetchData" ,error.response);
        if (error.response.statusText === "Unauthorized"){
            router.push({ name: 'Error401'})
        }

        if (error.response.status === 403){
            router.push({ name: 'Error403'})
        }
    }
}

const fetchTransactionData = async () => {
    try {
        let ids = product_ids.value.toString();
        console.log("ids", ids);
        ids = btoa(ids);
        console.log("btoa ids", ids);
        let get_url = '/api/partner/bookings?ids='+ids;
        console.log("get_url", get_url);
        const response = await axios.get(get_url);
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        transactionCollection.value = await response.data.data;
        console.log("transactionCollection", transactionCollection.value);
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

const getProducts = () => {
    if (primary.value !== null && primary.value['listings']  != undefined) {
        primary.value.listings.forEach( (item, index) => {            
            if (item.products !== null && item.products.length>0) {
                item.products.forEach( (product, idx) => {
                    product_ids.value.push(product.id)
                });
            }
        });
    }
}


router.beforeEach((to, from) => { 
    showMain.value= to.name==='Partner';
    componentTitle.value = to.meta.title;
});

</script>

<template>
    <div class="w-full min-h-screen">
        <SideBar />
        <div class="p-1 sm:ml-64">
            <div class="bg-white shadow p-4 flex justify-between ...">
                <div>
                    <span class="py-2 px-4 bg-teal-200 rounded-full font-bold text-sm">{{ componentTitle }}</span>
                </div>
                <div></div>
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ authStore.user?.name }}
                </div>
            </div>
            <div class="mx-auto max-w-screen-4xl p-4 md:p-6 2xl:p-10">
                <div v-show="showMain">
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
                        <BookingCounts :collection="[]" :summary="totals" :title="'Booking Transaction Counts'" :subtitle="'Summary'" />
                    </div>
                    
                    <div class="mb-4">
                        <BookingFigures :summary="totals" :title="'Booking Transaction Figures'" :subtitle="'Summary'" />
                    </div>
                </div>

                
                <router-view v-bind:transactions="transactionCollection"></router-view>
            </div>
        </div>
    </div>

</template>