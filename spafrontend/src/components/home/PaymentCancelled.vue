<script setup>
import { ref, onMounted } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter()
const authStore = useAuthStore();

const id = ref(0);
const bookingRefNo = ref('99999');
const checkoutLink = ref('#');

onMounted(() => {
    console.log("params", router.currentRoute.value.params);
    
    id.value = router.currentRoute.value.params.id;
    bookingRefNo.value = router.currentRoute.value.params.booking_ref_no;
    
    const action_url = '/api/payment/cancelled/'+id.value +'/'+bookingRefNo.value;
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

        checkoutLink.value = await response.data.data;
        if (checkoutLink.value.indexOf('confirm-and-pay')>-1){
            checkoutLink.value = 'http://localhost:3000' + checkoutLink.value;
        }
        console.log("response", checkoutLink.value );
    } catch (error) {
        console.log("updatePaymentTransaction" ,error);
    }
}

console.log("Payment Cancelled");
</script>

<template>
    <div class="pb-12 pt-10 md:pt-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 items-center content-center text-center" >
            <h2 class="font-semibold text-md">Payment Transaction Response</h2>
            <div class="my-5">
                <div class="w-36 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-orange-500">
                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div class="mb-4">
                <p class="mb-4"></p>
                <p class="mb-4">
                    <router-link :to="{ name: 'Listings'}" class="hover:no-underline underline">Home</router-link> | 
                    <a :href="checkoutLink" class="hover:no-underline underline">Back to Checkout page</a>
                </p>
                <h2 class="font-semibold text-2xl">Payment Cancelled</h2>
            </div>

            <div class="my-10 text-sm">
                <p class="mb-2">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
                <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here,</p>
            </div>
        </div>
    </div>
</template>