<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';

const router = useRouter()
const authStore = useAuthStore();

const primary = ref([]);
const listings = ref([]);
const products = ref([]);
const productIds = ref([]);
const isFetching = ref(false);
const selectedListing = ref({id:0, title: 'All', count: 0});

const showModal = ref(false);
const modalTitle = ref('');

const selectedItem = ref({});
const listingState = ref('Enabled');
const cases = ref([]);
const fetchingCases = ref(-1);

onMounted(() => {
    // console.log("params", router.currentRoute.value);
    authStore.getToken();
    fetchData().catch(error => {
        error.message; // 'An error has occurred: 404'
        console.log("fetchData Error: ", error.message);
    }); 
});

const fetchData = async () => {
    try {
        isFetching.value =true;
        const response = await axios.get('/api/partner/profile');
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        
        primary.value = await response.data.data;
        listings.value = primary.value.listings;
        console.log("collection: ",  primary.value);
        selectListing('*');
    } catch (error) {
        console.log("fetchData" ,error);
    }
}

const fetchCases = async (ids) => {
    try {
        let qids = ids.toString();
        // console.log("fetchCases", qids, " btoa:", btoa(qids));
        const response = await axios.get('/api/partner/reported/products?qids='+btoa(qids));
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        cases.value = await response.data.data;
        if (cases.value !== null && cases.value.length>0){
            fetchingCases.value=1;
        } else {
            fetchingCases.value=0;
        }
        console.log("fetchCases collection: ",  cases.value);
    } catch (error) {
        console.log("fetchCases" ,error);
    }
}

const caseStatus = (state) => {
    return (state==0)? 'Unsettled': 'Settled'
}

const viewCaseReport = (item) => {
    selectedItem.value = item;
    modalTitle.value = 'Case Report';
    listingState.value = "Enabled";
    if (selectedItem.value && selectedItem.value.listing_categories_enabled == 0) {
        listingState.value = "Disabled";
    }
    toggleModal();
}

const selectListing = (data) => {
    products.value = []
    if (typeof data === 'string' && data === '*') {
        selectedListing.value = { id:0, title: 'All', count: 0 }

        if (primary.value !== undefined && primary.value.listings && primary.value.listings.length >0) {

            primary.value.listings.forEach( (item, index)=> {
                // console.log(item.products);
                if (item && item.products !== undefined && item.products.length > 0) {
                    products.value = products.value.concat(item.products);
                }
            });

            productIds.value.length=0;
            if (products.value.length>0){
                products.value.forEach( (item, i) =>{
                    productIds.value.push( parseInt(item.id) );
                });

                fetchCases(productIds.value).catch(error => {
                    console.log("fetchCases Error: ", error.message);
                }); 
            }
        }
    } else {
        selectedListing.value = { id: data.id, title: data.name, count: 0}
        products.value = data.products;
    }

    // console.log("PRODUCT IDs", productIds.value);
}

const toggleModal = ()=>{
    showModal.value = !showModal.value;
}


</script>

<template>
    <div class="mb-2">
        <h2 class="text-title-md2 font-bold text-black white:text-dark">Listings</h2>
    </div>
    <div class="mb-8">
        <button @click.prevent="selectListing('*')" class="text-gray-900 bg-[#27AAC4] hover:bg-[#27AAC4]/90 focus:ring-4 focus:outline-none focus:ring-[#F7BE38]/50 font-medium rounded-full text-sm px-6 pb-2 pt-2.5 text-center inline-flex items-center dark:focus:ring-[#F7BE38]/50 mr-2 mb-2">All</button>
        <template v-for="item in listings">
            <button @click.prevent="selectListing(item)" class="text-white-900 bg-[#C2C2C2] hover:bg-[#27AAC4]/90 focus:ring-4 focus:outline-none focus:ring-[#27AAC4]/50 font-medium rounded-full text-sm px-6 pb-2 pt-2.5 text-center inline-flex items-center dark:focus:ring-[#27AAC4]/50 mr-2 mb-2">
                {{ item.name }}
            </button>
        </template>
    </div>
    <div class="mb-6">
        <template v-if="selectedListing && selectedListing.id!==0">
            <h2 class="text-title-md2 font-bold text-black white:text-dark mb-4">{{ selectedListing?.title }} - Products</h2>
            <router-link :to="{ name: 'CreateProduct', params: { listingCategoryId: selectedListing?.id} }"  type="button" class="text-gray-900 bg-[#F7BE38] hover:bg-[#F7BE38]/90 focus:ring-4 focus:outline-none focus:ring-[#F7BE38]/50 font-medium rounded-full text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#F7BE38]/50 mr-1 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Product +
            </router-link>
        </template>
        
    </div>
    <div class="mb4" v-if="products!== undefined && products.length>0">
        <div class="flex flex-col overflow-x-auto">
            <div class="sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <div class="overflow-x-auto">
                        <table class="table-fixed w-full text-left text-sm font-light">
                            <thead class="border-b font-medium dark:border-neutral-500">
                                <tr>
                                    <th scope="col" class="px-6 py-4 w-[30%]">Title</th>
                                    <th scope="col" class="px-6 py-4 max-w-[38%]">Description</th>
                                    <th scope="col" class="px-6 py-4">Max Guest</th>
                                    <th scope="col" class="px-6 py-4">Rate</th>
                                    <th scope="col" class="px-6 py-4">Discount</th>
                                    <th scope="col" class="px-6 py-4">
                                    </th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b dark:border-neutral-500" v-for="item in products">
                                    <td class="whitespace-nowrap px-6 py-4 truncate">
                                        <router-link :to="{ name: 'ViewPartnersProduct', params: { listingCategoryId: item.listing_category_id, id:item.id }}" class="no-underline hover:underline">
                                            {{ item.name }}
                                        </router-link>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 truncate">{{ item.description }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ item.max_guest }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ item.rate }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ item.discount }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex gap-2">
                                            <router-link :to="{name:'ManageProduct', params: { listingCategoryId: item.listing_category_id, id:item.id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </router-link>
                                            <router-link :to="{ name: 'ViewPartnersProduct', params: { listingCategoryId: item.listing_category_id, id:item.id }}" class="no-underline hover:underline">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </router-link>
                                        </div>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
    </div>

    <div class="my-5" v-if="fetchingCases==1">
        <h2 class="text-title-md2 font-bold text-black white:text-dark mb-4">Reported Product Listings</h2>
        <div v-if="fetchingCases==0">No reported product listing found..</div>
        <table class="w-full text-sm text-left text-gray-700 border-gray-600 rounded-lg" v-if="cases.length>0">
            <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Product Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Listing
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Booked Dates
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Booking Status
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Booked By Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Booked By Email
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Report Category
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Report Remarks
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Status
                    </th>
                    
                </tr>
            </thead>
            <tbody v-if="cases.length>0">
                <tr class="bg-white border-b" v-for="item in cases">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap white:text-dark">
                        <a @click="viewCaseReport(item)" class="underline text-blue-700 hover:no-underline cursor-pointer">
                            {{ item.product_name }}
                        </a>
                    </th>
                    <td class="px-6 py-4 truncate">
                        {{ item.listing_categories_name }}
                    </td>
                    <td class="px-6 py-4 truncate">
                        From: {{ item.from }} To: {{ item.to }}
                    </td>
                    <td class="px-6 py-4 truncate">
                        {{ item.booking_status }}
                    </td>
                    <td class="px-6 py-4 truncate">
                        {{ item.name }}
                    </td>
                    <td class="px-6 py-4 truncate">
                        {{ item.email }}
                    </td>
                    <td class="px-6 py-4">
                        {{ item.related_to }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="w-[200px] truncate">{{ item.remarks }}</div>
                    </td>
                    <td class="px-6 py-4 flex items-center">
                        <span class="mr-3">{{ caseStatus(item.settled)  }}</span>
                        <a @click="viewCaseReport(item)" class="cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                         
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
        <div class="relative w-auto my-6 mx-auto max-w-6xl">
            <!--content-->
            <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                <!--header-->
                <div class="flex items-start justify-between p-5 border-b border-solid border-slate-200 rounded-t">
                    <h3 class="text-3xl font-semibold">
                    {{ modalTitle }}
                    </h3>
                    <button class="" v-on:click="toggleModal()">
                    <span class="font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                    </button>
                </div>
                <!--body-->
                <div class="relative p-6 flex-auto">
                    <div class="mb-4 p-4">
                        <div class="grid grid-cols-2 mb-4">
                            <div>
                                <p>{{ selectedItem.product_name }}</p>
                                <p class="text-sm">{{ selectedItem.listing_categories_name  }}</p>
                                <p class="text-sm">{{ selectedItem.product_address }}</p>
                            </div>
                            <div>
                                <p>Reported By: {{ selectedItem.name }}</p>
                                <p class="text-sm">{{ selectedItem.email }}</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <strong>Case Related To:</strong> {{ selectedItem.related_to }}
                        </div>
                        <div class="mb-4">
                            <p><strong>Remarks:</strong></p>
                            <p>{{ selectedItem.remarks }}</p>
                        </div>
                        <div class="mb-4">
                            <p><strong>Current Listing Status:</strong> {{ listingState }}</p>
                        </div>
                        
                    </div>
                </div>
                <!--footer-->
                <div class="flex items-center justify-end px-6 py-6 border-t border-solid border-slate-200 rounded-b">
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
</template>