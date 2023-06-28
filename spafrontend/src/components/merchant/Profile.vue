<script setup>
import { ref, onMounted } from 'vue';
import axios from "axios";
import ProfileForm from './sections/ProfileForm.vue';
import Property from './sections/Property.vue';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();

const form = ref({});
const formState = ref(0);
const errorMsg = ref('');
const parentView = ref('PROFILE');
const listing_count = ref(0);

onMounted(() => {
    authStore.getToken();
    fetchData().catch(error => {
        error.message; // 'An error has occurred: 404'
        console.log("fetchData Error: ", error.message);
        formState.value = 0;
    });
    
});

const fetchData = async () => {

    try {
        const response = await axios.get('/api/partner/profile');
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        
        form.value = await response.data.data;
        // listing_count = form.value.listings;
        console.log("Merchant: ",  form.value);
        console.log("Listing Count: ",  form.value.listings.length);
        formState.value = 1;
    } catch (error) {
        console.log("fetchData" ,error);
    }
    
}

const submitForm = async (form_data) => {
    try {
        const response = await axios.post('/api/partner/profile', form_data);
        console.log("Data", response, "Old formState.value", formState);
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        showToast(1);
    } catch (error) {
        errorMsg.value=error.message;
        showToast(2);
    }
}

const showToast = (mode) => {
    let el = document.getElementById("toast-success");
    if (mode==2) {
        el = document.getElementById("toast-danger");
    }
    
    el.classList.remove("hidden"); 
    setTimeout(function () {
        el.classList.add("hidden");
    }, 5000);
}

</script>

<template>
    <div class="mx-auto max-w-2xl">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-title-md2 font-bold text-black white:text-dark">
                Profile
            </h2>
        </div>

        <div class="overflow-hidden rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">

            <div class="max-h-64 relative">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev/svgjs" width="894" height="256" preserveAspectRatio="none" viewBox="0 0 894 256"><g mask="url(&quot;#SvgjsMask1222&quot;)" fill="none"><rect width="894" height="256" x="0" y="0" fill="url(&quot;#SvgjsLinearGradient1223&quot;)"></rect><use xlink:href="#SvgjsSymbol1230" x="0" y="0"></use><use xlink:href="#SvgjsSymbol1230" x="720" y="0"></use></g><defs><mask id="SvgjsMask1222"><rect width="894" height="256" fill="#ffffff"></rect></mask><linearGradient x1="82.16%" y1="162.3%" x2="17.84%" y2="-62.3%" gradientUnits="userSpaceOnUse" id="SvgjsLinearGradient1223"><stop stop-color="#0e2a47" offset="0"></stop><stop stop-color="#00459e" offset="1"></stop></linearGradient><path d="M-1 0 a1 1 0 1 0 2 0 a1 1 0 1 0 -2 0z" id="SvgjsPath1224"></path><path d="M-3 0 a3 3 0 1 0 6 0 a3 3 0 1 0 -6 0z" id="SvgjsPath1228"></path><path d="M-5 0 a5 5 0 1 0 10 0 a5 5 0 1 0 -10 0z" id="SvgjsPath1227"></path><path d="M2 -2 L-2 2z" id="SvgjsPath1226"></path><path d="M6 -6 L-6 6z" id="SvgjsPath1225"></path><path d="M30 -30 L-30 30z" id="SvgjsPath1229"></path></defs><symbol id="SvgjsSymbol1230"><use xlink:href="#SvgjsPath1224" x="30" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="30" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="30" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="30" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="30" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="90" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="90" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="90" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="90" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="90" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="150" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="150" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="150" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="150" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="150" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="210" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="210" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="210" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1228" x="210" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="210" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="270" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="270" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="270" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="270" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="270" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="330" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="330" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="330" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="330" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="330" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="390" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="390" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="390" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="390" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="390" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="450" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="450" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="450" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1228" x="450" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="450" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="510" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="510" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="510" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1228" x="510" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="510" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="570" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="570" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="570" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="570" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="570" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="630" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1228" x="630" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1229" x="630" y="150" stroke="#1c538e" stroke-width="3"></use><use xlink:href="#SvgjsPath1227" x="630" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="630" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="690" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="690" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="690" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="690" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="690" y="270" stroke="#1c538e"></use></symbol><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev/svgjs" width="894" height="256" preserveAspectRatio="none" viewBox="0 0 894 256"><g mask="url(&quot;#SvgjsMask1222&quot;)" fill="none"><rect width="894" height="256" x="0" y="0" fill="url(&quot;#SvgjsLinearGradient1223&quot;)"></rect><use xlink:href="#SvgjsSymbol1230" x="0" y="0"></use><use xlink:href="#SvgjsSymbol1230" x="720" y="0"></use></g><defs><mask id="SvgjsMask1222"><rect width="894" height="256" fill="#ffffff"></rect></mask><linearGradient x1="82.16%" y1="162.3%" x2="17.84%" y2="-62.3%" gradientUnits="userSpaceOnUse" id="SvgjsLinearGradient1223"><stop stop-color="#0e2a47" offset="0"></stop><stop stop-color="#00459e" offset="1"></stop></linearGradient><path d="M-1 0 a1 1 0 1 0 2 0 a1 1 0 1 0 -2 0z" id="SvgjsPath1224"></path><path d="M-3 0 a3 3 0 1 0 6 0 a3 3 0 1 0 -6 0z" id="SvgjsPath1228"></path><path d="M-5 0 a5 5 0 1 0 10 0 a5 5 0 1 0 -10 0z" id="SvgjsPath1227"></path><path d="M2 -2 L-2 2z" id="SvgjsPath1226"></path><path d="M6 -6 L-6 6z" id="SvgjsPath1225"></path><path d="M30 -30 L-30 30z" id="SvgjsPath1229"></path></defs><symbol id="SvgjsSymbol1230"><use xlink:href="#SvgjsPath1224" x="30" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="30" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="30" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="30" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="30" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="90" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="90" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="90" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="90" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="90" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="150" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="150" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="150" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="150" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="150" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="210" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="210" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="210" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1228" x="210" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="210" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="270" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="270" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="270" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="270" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="270" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="330" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="330" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="330" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="330" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="330" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="390" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="390" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="390" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="390" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="390" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="450" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="450" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="450" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1228" x="450" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="450" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="510" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="510" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="510" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1228" x="510" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="510" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="570" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="570" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="570" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="570" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="570" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="630" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1228" x="630" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1229" x="630" y="150" stroke="#1c538e" stroke-width="3"></use><use xlink:href="#SvgjsPath1227" x="630" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1224" x="630" y="270" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="690" y="30" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="690" y="90" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1227" x="690" y="150" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1226" x="690" y="210" stroke="#1c538e"></use><use xlink:href="#SvgjsPath1225" x="690" y="270" stroke="#1c538e"></use></symbol></svg></svg>
                <h1 class="absolute top-0 right-0 bottom-0 left-0 m-auto w-60 h-12 py-2 shadow-xl bg-white-10 w-3/4 md:w-2/5 mx-auto sm:text-xl text-white font-medium flex justify-center">
                    Merchant Information
                </h1>
            </div>
        </div>

        <ProfileForm 
            :form="form"
            :form-state="formState"
            :error-msg="errorMsg"
            @save-data="submitForm"
            @show-toast="showToast"
        />
        <div class="mt-10">
            <!-- <h2 class="text-title-md2 font-bold text-black white:text-dark mb-4">
                Listings
            </h2> -->
            <h2 class="text-2xl font-normal dark:text-dark">Where's your place located?</h2>
            <div class="mb-4">
                <p class="my-4 text-md text-gray-700">
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book...
                </p>

                <router-link :to="{name: 'MerchantListings'}" class="block text-white bg-teal-700 hover:bg-teal-800 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-teal-600 dark:hover:bg-teal-700 dark:focus:ring-teal-800" type="button">
                    My Listings
                </router-link>
            </div>

            <div class="grid grid-flow-row gap-4 text-neutral-600 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
                <div v-for="item in form.listings">
                    <Property :form="item" :parent-view="parentView" />
                </div>
            </div>
        </div>
    </div>


</template>
