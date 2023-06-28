<script setup>
import { onMounted, watch, ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import axios from "axios";
import SideBar from '../components/customer/sections/sidebar.vue'
import TransactionCount from '../components/customer/sections/TransactionCount.vue';
import TransactionItems from '../components/customer/sections/TransactionItems.vue';

const router = useRouter();
const authStore = useAuthStore();

const showMain = ref(true);
const componentTitle = ref('');
const userAccountDetails = ref({});
const collection = ref([]);
const totals = ref({});


onMounted(() => {
    authStore.getUser('Customer Dashboard');
    showMain.value= router.currentRoute.value.name==='Customer';
    componentTitle.value = router.currentRoute.value.meta.title;

    fetchData().catch(error => {
        error.message; // 'An error has occurred: 404'
        // console.log("fetchData Error: ", error.code);
    }); 
});

watch(() => collection.value, (c, b) => { 
    // console.log("before:", b);
    // console.log("after:", c);
    totals.value = {
        counts: {
            all: c.length,
            pending: 0,
            payment_submitted: 0,
            cancelled: 0,
            booked:0
        },
        figures: {
            all: 0,
            booked: 0,
            cancelled: 0,
        }
    }
    if (c && c.length>0){
        collection.value.forEach((item, index) =>{
            console.log("booking_status: ", item.booking_status, " payment status: ", item.payment_status, " Total: ", item.total);
            //Counts
            if (item.booking_status==='Pending') totals.value.counts.pending +=1;
            if (item.booking_status==='Payment Submitted') totals.value.counts.payment_submitted +=1;
            if (item.booking_status==='Cancelled') totals.value.counts.cancelled +=1;
            if (item.booking_status==='Booked' || item.booking_status==='Completed') totals.value.counts.booked +=1;

            //Figures
            totals.value.figures.all += parseFloat(item.total);
            if (item.payment_status==='Payment Completed') totals.value.figures.booked += parseFloat(item.total);
            if (item.payment_status==='Payment Cancelled') totals.value.figures.cancelled += parseFloat(item.total);
        });
    }
    console.log("totals:", totals.value)
});

const fetchData = async () => {
    try {
        
        const response = await axios.get('/api/customer/bookings');
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        console.log("Transactions: ", response.data.data);
        collection.value = response.data.data;
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


router.beforeEach((to, from) => { 
    console.log("from", from);
    console.log("to", to);
    showMain.value= to.name==='Customer';
    componentTitle.value = to.meta.title;
});

router.beforeResolve((to, from) => {
    console.log("beforeResolve", to);
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
            <div class="p-4" v-show="showMain">
                <TransactionCount :totals="totals" />
                <TransactionItems :collection="collection" />
            </div>
            <router-view v-bind:transactions="collection"></router-view>
        </div>
    </div>
</template>