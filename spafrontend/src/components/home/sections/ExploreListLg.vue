<script setup>
//https://ordinarycoders.com/blog/article/17-tailwindcss-cards
import { ref } from 'vue';
import Ratings from './Ratings.vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const props = defineProps({
    collection: { type: Array, default: [] },
    title: { type: String, default: 'Explore Mindanao'},
    subtitle: { type: String, default: 'These popular destinations have a lot to offer'},
})

const rating = ref(0);

// const emit = defineEmits(['select']);

const select = (item) => {
    console.log("Selected Product:", item);
    router.push({ name: 'ViewProductDetails', params: { id: item.id }, query: { slug: item.slug, listing: item.listing_category_id, rate: item.rate, enabled: item.enabled, maxguest: item.max_guest } });
    // emit('viewDetails', item );
}

const productImage = (product) => { 
    if (product.photos === null) return 'No Image'
    const arr_product_photos = product.photos.split(',');
    const item = arr_product_photos.length>0? arr_product_photos[0]:'';
    let img_name = item.replace(/^\s+|\s+$/gm,'');
    return 'http://localhost:8000/uploads/' + img_name;
};

</script>
<template>
    <div class="mb-4 mt-8 items-center justify-center">
        <h1 class="font-semibold text-3xl">{{ title }}</h1>
        <p>{{ subtitle }}</p>
    </div>
    
    <div class="mb-4 grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-5">
        <!--Card 1-->
        <div class="rounded overflow-hidden cursor-pointer" v-for="item in collection" @click="select(item)">
            <img class="w-full rounded-lg object-contain" :src="productImage(item)" alt="" />
            <Ratings :rating="rating"></Ratings>
            <div class="py-1">
                <h1 class="font-semibold">{{ item.name }}</h1>
                <p class="text-gray-700 text-xs">
                    {{ item.count }}
                </p>
            </div>
            
            <div class="pt-4 pb-2">
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">₱{{ item.rate }} per Night</span>
            </div>
        </div>
        
    </div>

</template>