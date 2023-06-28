<script setup>
import { ref, onMounted } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter()
const authStore = useAuthStore();

const id = ref(0);
const bookingRefNo = ref('99999');
const booked = ref({});

onMounted(() => {
    console.log("params", router.currentRoute.value.params);
    
    id.value = router.currentRoute.value.params.id;
    bookingRefNo.value = router.currentRoute.value.params.booking_ref_no;
    
    const action_url = '/api/payment/success/'+id.value +'/'+bookingRefNo.value;
    console.log("params", id.value , "Bref", bookingRefNo.value, "URL:", action_url);
    updatePaymentTransaction(action_url)
    
});

const updatePaymentTransaction = async (action_url) => {
    try {

        const formData = new FormData();
        formData.append('id', id.value);
        formData.append('booking_reference', bookingRefNo.value);
        
        const response = await axios.post(action_url, formData);      
      
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        booked.value = await response.data.data;
        console.log("response", booked.value );
    } catch (error) {
        console.log("updatePaymentTransaction" ,error);
    }
}
console.log("Payment Success");
</script>

<template>
    <div class="pb-12 pt-10 md:pt-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 items-center content-center text-center" >
            <h2 class="font-semibold text-md">Payment Transaction Response</h2>
            <div class="my-5">
                <div class="w-36 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-cyan-500">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div class="mb-4 items-center content-center text-center">
                <h1 class="font-bold text-2xl">{{ bookingRefNo }}</h1>
                <p class="mb-4"><span class="inline-block bg-gray-300 rounded-full px-3 py-1 text-md font-semibold text-gray-700 mr-2 mb-2 text-xs">Booking Reference Code</span></p>
                <p class="mb-4">
                    <router-link :to="{ name: 'Listings'}" class="hover:no-underline underline">Home</router-link>
                </p>
                <h2 class="font-semibold text-2xl">Payment Successful</h2>
            </div>

            <div class="my-10 text-sm">
                <p class="mb-2">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
                <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here,</p>
            </div>
        </div>
    </div>
</template>