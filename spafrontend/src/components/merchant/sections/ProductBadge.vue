<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        default: {
            id: 0,
            name: null,
            slug: null,
            description: null,
            max_guest: 1,
            photos: null,
            enabled: 1,
            rate: 0.00,
            discount: 0.00,
            listing_category_id: 0
        }
    }
});

const productLink = computed(() => {
    if (props.form.id==0) return '/partner/products';
    //product/:listingCategoryId/:id
    return '/partner/product/'+ props.form.listing_category_id +'/' + props.form.id;

});
</script>
<template>
    <!-- Badge Type
    <div class="inline-flex max-w-[98%] items-center px-4 py-1 text-sm font-medium text-gray-900 bg-[#c8c8c8] rounded-full dark:text-gray-900">
        <p class="truncate overflow-hidden text-ellipsis">{{ form.id }}-{{ form.name }}</p>
        <router-link :to="productLink" type="button" class="inline-flex items-center p-0.5 ml-2 text-sm text-gray-900 bg-transparent rounded-sm hover:bg-gray-900 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-gray-300" aria-label="View">
            <svg fill="none" class="w-3.5 h-3.5" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
            </svg>
        </router-link>
    </div>
    -->

    <!-- List Type -->
    
    <router-link :to="{ name: 'ViewPartnersProduct', params: { listingCategoryId:form.listing_category_id, id:form.id  } }" class="mr-4 hover:underline md:mr-6 ">{{ form.id+'-'+form.name }}</router-link>
</template>

