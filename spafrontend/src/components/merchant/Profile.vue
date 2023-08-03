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
const showModal = ref(false);
const modalTitle = ref('Merchant Policy Agreement');
const agree = ref(false);

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
        agree.value = true;
        if (form.value===null) {
            showModal.value = !showModal.value;
            agree.value =false;
            return;
        }
        if (form.value !== null && form.value.terms_agreed_at === null) {
            showModal.value = !showModal.value;
            agree.value = false;
        } 
    } catch (error) {
        console.log("fetchData x" ,error.response);
        if (error.response.status == 404) {
            showModal.value = !showModal.value;
            agree.value =false;
            return;
        }
    }
}

const submitForm = async (form_data, docs) => {
    console.log("submitForm", form_data, " docs", docs);
    try {
        const config = {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        };

        let d = (form_data.documents === null)? '': form_data.documents;
        const formData = new FormData();

        if (form_data.id == null || (form_data.id !== null && form_data.id ==-0)) {
            d='';
        }
        
        if (docs.length>0) {
            for( var i = 0; i < docs.length; i++ ){
                let file = docs[i];
                formData.append('files[' + i + ']', file);
            }
        }  
        
        formData.append('id', form_data.id);
        formData.append('name', form_data.name);
        formData.append('bus_address', form_data.bus_address);
        formData.append('bus_contact_name', form_data.bus_contact_name);
        formData.append('bus_contact_no', form_data.bus_contact_no);
        formData.append('bus_email', form_data.bus_email);
        formData.append('documents', d);
        formData.append('terms_agreed_at', form_data.terms_agreed_at);
        
        const response = await axios.post('/api/partner/profile', formData, config);
        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        console.log("Old Value", form.value);
        form.value = response.data.merchant;
        showToast(1);
        formState.value = 1;
        console.log("New Value", form.value);
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

const toggleModal = ()=>{
    showModal.value = !showModal.value;
}

const acceptTP = () =>{
    if (agree.value === false) return;
    formState.value = 2;
    toggleModal();
}

const showTermsPolicies = () =>{
    toggleModal();
}


</script>

<template>
    <div class="mx-auto max-w-3xl">
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
            :formstate="formState"
            :error-msg="errorMsg"
            :agree="agree"
            @submitdata="submitForm"
            @showtp="showTermsPolicies"
        />
        <div class="mt-10">
            <!-- <h2 class="text-title-md2 font-bold text-black white:text-dark mb-4">
                Listings
            </h2> -->
            <h2 class="text-2xl font-normal dark:text-dark">Adding of Listings</h2>
            <div class="mb-4">
                <p class="my-4 text-md text-gray-700">
                    Listings are the proprietors' properties that have been declared to the proprietor's account or profile. Owners can add several listings to their accounts to manage all of their properties.
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

        <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
            <div class="relative w-auto my-6 mx-auto max-w-6xl">
                <!--content-->
                <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                <!--header-->
                <div class="flex items-start justify-center p-5 border-b border-solid border-slate-200 rounded-t">
                    <h3 class="text-3xl font-semibold justify-center w-auto">
                    {{ modalTitle }}
                    </h3>
                    <button class="absolute top-2 right-2" v-on:click="acceptTP()">
                    <span class="font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                    </button>
                </div>
                <!--body-->
                <div class="relative p-6 flex-auto text-sm">
                    <p>
                        <ul>
                            <li>1. <strong>Registration:</strong>
                            </li>
                            <ul class="pl-8">
                                <li>
                                    a. Merchants are required to provide proof of operation documents such as Business Permit, Mayor's Permit and 2 Government Valid Identifications) for verification.</li>
                                <li>
                                    b. Merchants are required to submit the said document so that their listings and products will be available in the platform.</li>
                                <li>
                                    c. Merchants must have the minimum room requirements between 26 and 300 guest rooms to be qualified in the platform.</li>
                                <li>
                                    d. Failure  to provide the said requirements will lead to account removed.</li>
                                <li>
                                    e. Account suspension to falsification and having a massive report by their customer as scammers and alike.</li>
                            </ul>
                            
                            
                        </ul>
                        <ul>
                            <li>2. <strong>Terms and Conditions:</strong>
                            </li>
                            <ul class="pl-8">
                                <li>
                                    a.  <strong>Hotel Listing:</strong> Merchants shall ensure that all information provided for their respective hotel listings on the platform is accurate, complete, and up-to-date.</li>
                                <li>
                                    b. <strong>Availability and Rates:</strong> Merchants shall be responsible for managing and updating room availability and rates on the platform regularly.</li>
                                <li>
                                    c. <strong>Booking Confirmation:</strong> Merchants can get the information of any successful bookings made through the platform including guest information, dates, and payment details.</li>
                                <li>
                                    d. <strong>Payment:</strong> PaReserve shall facilitate the payment process with guests and remit the hotel room charges to the merchants after deducting any agreed-upon commission or fees every month.</li>
                                <li>
                                    e. <strong>Cancellations and Refunds:</strong> PaReserve shall follow the agreed-upon cancellation and refund policies set forth by the merchants for bookings made through the platform.</li>
                                
                            </ul>
                            
                        </ul>
                        <ul>
                            <li>3. <strong>Commission and Fees:</strong>
                            </li>
                            <ul class="pl-8">
                                <li>
                                    a.  Merchant agree to pay the Platform a commission, as specified in a separate commission agreement, for each successful booking made through the platform.</li>
                                <li>
                                    b. The commission percentage and any applicable fees shall be mutually agreed upon by both parties and outlined in a separate commission agreement.</li>
                                
                            </ul>
                            
                        </ul>
                        <ul>
                            <li>4. <strong>Confidentiality:</strong>
                            </li>
                            <ul class="pl-8">
                                <li>
                                    Both parties shall keep all non-public information, data, and materials shared during the course of this partnership confidential and not disclose them to any third party without prior written consent from the other party.</li>
                                
                            </ul>
                            
                        </ul>

                    </p>
                    <p></p>

                </div>
                <!--footer-->
                <div class="grid grid-cols-2 items-center p-6 border-t border-solid border-slate-200 rounded-b">
                    <div class="flex justify-start">
                        <input id="agree" type="checkbox" value="" v-model="agree" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 white:bg-gray-100 dark:border-gray-600">
                        <label for="agree" class="ml-2 text-sm font-medium text-gray-900 white:text-gray-900">Yes, I agree to the terms & policies.</label> 
                    </div>
                    <div class="flex justify-end ">
                        <button class="text-gray-100 bg-teal-400 px-6 rounded-lg font-bold uppercase py-4 text-sm outline-none focus:outline-none mb-1 ease-linear transition-all duration-150" type="button" v-on:click="acceptTP()">
                        Ok
                        </button> 
                    </div>
                    <!-- 
                    <button class="text-teal-500 bg-transparent border border-solid border-teal-500 hover:bg-teal-500 hover:text-white active:bg-teal-600 font-bold uppercase text-sm px-4 py-2 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button" v-on:click="toggleModal()">
                    Close
                    </button>
                    <button class="text-cyan-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button" v-on:click="toggleModal()">
                    Save Changes
                    </button> 
                    -->
                </div>
                </div>
            </div>
        </div>
        <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>


    </div>


</template>
