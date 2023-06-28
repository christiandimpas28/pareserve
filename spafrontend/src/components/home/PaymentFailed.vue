<script setup>
import { ref, onMounted } from 'vue';
const bookingRefNo = ref('');
console.log("Payment Failed");
</script>

<template>
    <div class="pb-12 pt-10 md:pt-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 items-center content-center text-center" >
            <h2 class="font-semibold text-md">Payment Transaction Response</h2>
            <div class="my-5">
                <div class="w-36 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-red-500">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
                    </svg>

                </div>
            </div>
            <div class="mb-4">
                <p class="mb-4"></p>
                <p class="mb-4">
                    <router-link :to="{ name: 'Listings'}" class="hover:no-underline underline">Home</router-link>
                </p>
                <h2 class="font-semibold text-2xl">Payment Failed</h2>
            </div>

            <div class="my-10 text-sm">
                <p class="mb-2">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
                <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here,</p>
            </div>
        </div>
    </div>
</template>

