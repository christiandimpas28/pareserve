<script setup>
import { computed } from 'vue';
const props = defineProps({
  form: { 
        type: Object, 
        default: {
            id: 0,
            name: null,
            category: null,
            description: null,
            address: null,
            city: null,
            listing_photos: null,
            merchant_id: 0
        } 
  },
  parentView: { type: String, default: 'LISTING' }
});

const primaryPhoto = computed(() => {
    if (props.form.listing_photos==null || (props.form.listing_photos!=null && props.form.listing_photos.length===0)) return 'https://flowbite.com/docs/images/blog/image-1.jpg';
    const photos = props.form.listing_photos.split(",");
    return 'http://localhost:8000/uploads/' + photos[0];

});

</script>

<template>
<div class="property-card">
    <img class="w-full object-cover h-36" :src="primaryPhoto" alt="" />

    <div class="p-5 flex-col gap-3">
        <div class="flex items-center gap-2">
            <span class="property-badge">{{ form.category}}</span>
        </div>
        
        <h2 class="product-title">
            {{ form.name }}
        </h2>
        <div class="h-24 overflow-hidden">
            <p>{{ form.description }}</p>
        </div>

        <div class="mt-5 flex justify-normal gap-0" v-if="parentView === 'LISTING'">
            <router-link :to="{ path: '/partner/listing/edit/' + form.merchant_id + '/property/' + form.id }" class="text-white-900 bg-[#27AAC4] hover:bg-[#27AAC4]/90 focus:ring-4 focus:outline-none focus:ring-[#27AAC4]/50 font-medium rounded-lg text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#27AAC4]/50 mr-1 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </router-link>
            <router-link :to="{ path: '/partner/listing/edit/' + form.merchant_id + '/property/' + form.id }" class="text-white-900 bg-[#F77E7E] hover:bg-[#F77E7E]/90 focus:ring-4 focus:outline-none focus:ring-[#F77E7E]/50 font-medium rounded-lg text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#F77E7E]/50 mr-1 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </router-link>
            <router-link :to="{ name: 'MerchantProducts',query: {listing: form.id}}"  type="button" class="text-gray-900 bg-[#F7BE38] hover:bg-[#F7BE38]/90 focus:ring-4 focus:outline-none focus:ring-[#F7BE38]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#F7BE38]/50 mr-1 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Products
            </router-link>
        </div>
    </div>
</div>
</template>


