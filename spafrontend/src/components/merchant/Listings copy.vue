<script setup>
import { ref } from "vue";
import { Modal } from 'flowbite';
import Property from './sections/Property.vue';



</script>

<script>

const $targetEl = document.getElementById('staticModal');
const options = {
    placement: 'bottom-right',
    backdrop: 'dynamic',
    backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40',
    closable: true,
    onHide: () => {
        console.log('modal is hidden');
    },
    onShow: () => {
        console.log('modal is shown');
    },
    onToggle: () => {
        console.log('modal has been toggled');
    }
}

const modal = new Modal($targetEl, options);

export default {
    name: 'Listing',
    components: { Property },
    methods: {
        propFunc(obj) {
            console.log(obj.name);
        }
    },
    created() {
        this.listings = [{ id: 1, name: 'Marco Polo'}, { id: 2, name: 'Mandaya Hotel'}, { id: 3, name: 'Ritz Hotel'}];
    },
    data() {
        return {
            listings: [{
                id: 1,
                name: 'kaka in'
            }, {
                id: 2,
                name: 'Sampa'
            }]
        }
    }
 }
</script>

<template>
    <div class="grid grid-cols-2 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-4">
        <div class="w-26 flex rounded-md p-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-11 w-11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <div class="text-align-center">
            <h1 class="text-4xl font-normal dark:text-dark mb-2 justify-stretch">Merchant</h1>
            <p class="mb-2 justify-stretch">sdasd</p>
            </div>
        </div>
        <div class="w-26 flex rounded-md bg-gray-300 p-8 flow-root">
            <h2 class="text-2xl font-normal dark:text-dark">Where's your place located?</h2>
            <div>
                <p class="my-4 text-lg text-gray-700">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                <button data-modal-target="staticModal" data-modal-toggle="staticModal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                    Add New Listing
                </button>
            </div>
        </div>
    </div>
    <h1 class="text-2xl font-normal dark:text-dark mb-8">My Listings</h1>

    <div class="bg-gray-100 flex items-center gap-10">
        <div class="property-card">
            <img class="w-full h-full object-cover" src="https://flowbite.com/docs/images/blog/image-1.jpg" alt="" />

            <div class="p-5 flex-col gap-3">
                <div class="flex items-center gap-2">
                    <span class="property-badge ">Hotel</span>
                    <span class="property-badge ">Restaurant</span>
                </div>
                
                <h2 class="product-title">
                    Marco Polo Davao
                </h2>
                <p>asdlkjask dasd jaskd </p>

                <div class="mt-5 flex justify-normal gap-1">
                    <router-link class="text-white-900 bg-[#27AAC4] hover:bg-[#27AAC4]/90 focus:ring-4 focus:outline-none focus:ring-[#27AAC4]/50 font-medium rounded-lg text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#27AAC4]/50 mr-1 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
  <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
</svg>
                    </router-link>
                    <router-link class="text-white-900 bg-[#F77E7E] hover:bg-[#F77E7E]/90 focus:ring-4 focus:outline-none focus:ring-[#F77E7E]/50 font-medium rounded-lg text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#F77E7E]/50 mr-1 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
</svg>
                    </router-link>
                    <router-link type="button" class="text-gray-900 bg-[#F7BE38] hover:bg-[#F7BE38]/90 focus:ring-4 focus:outline-none focus:ring-[#F7BE38]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#F7BE38]/50 mr-1 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
  <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
</svg>
                        Products
                    </router-link>


                </div>
            </div>
        </div>

        
        
    </div>

    

    <div class="grid grid-cols-4 gap-4 mb-4">
        
        <div v-for="item in listings" class="flex items-center justify-stretch h-24 rounded bg-gray-50 dark:bg-gray-800">
            <Property :data="item" :setActiveData="propFunc" />
        </div>
        
    </div>

    <!-- Main modal -->
    <div id="staticModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-3xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-0">
                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 white:text-dark">
                        Property Form
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="staticModal">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                    </button>
                </div>
                <!-- Modal body -->
                <form>
                    <div class="p-6 space-y-6">
                        <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        </p>
                        
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">Name</label>
                                <input type="text" id="name" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Name" required>
                            </div>
                            <div>
                                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">Category</label>
                                <input type="text" id="category" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Category" required>
                            </div>  
                        </div>

                        <div class="mb-6">
                            <div>
                                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">Description</label>
                                <input type="text" id="description" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Say something about your place" required>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div>
                                <label for="address" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">Address</label>
                                <input type="text" id="address" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Address" required>
                            </div>
                        </div>
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label for="city" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">City</label>
                                <input type="text" id="city" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="City" required>
                            </div>  
                        </div>
                        <div class="mb-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-dark" for="listing_photos">Upload photos</label>
                                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-500 dark:text-gray-800 focus:outline-none dark:bg-gray-200 dark:border-gray-100 dark:placeholder-gray-400" id="listing_photos" type="file" accept="image/png, image/gif, image/jpeg" multiple>

                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button type="submit" data-modal-hide="staticModal" class="text-white bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">Save</button>
                        <button data-modal-hide="staticModal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>