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
const isFetching = ref(false);
const selectedListing = ref({id:0, title: 'All', count: 0});

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
        // console.log("collection: ",  primary.value);
        selectListing('*');
    } catch (error) {
        console.log("fetchData" ,error);
    }
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
        }
    } else {
        selectedListing.value = { id: data.id, title: data.name, count: 0}
        products.value = data.products;
    }

    console.log("selectedListing.value", selectedListing.value);
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
                                        <router-link :to="{name:'ManageProduct', params: { listingCategoryId: item.listing_category_id, id:item.id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </router-link>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
    </div>
    
</template>