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

const getRating = (reviews) => {
    if (reviews.length ==0) return 0;
    let sumRating = 0;
    const rl = reviews.length;
    reviews.forEach( (item, index) => {
        sumRating+= item.rating;
    });
    return sumRating/rl;
}

</script>
<template>
    <div class="mb-4 mt-8 items-center justify-center">
        <h1 class="font-semibold text-3xl">{{ title }}</h1>
        <p>{{ subtitle }}</p>
    </div>
    
    <div class="mb-4 grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-5">
        <!--Card 1-->
        <div class="rounded overflow-hidden cursor-pointer relative" v-for="item in collection" @click="select(item)">
            <img class="w-full rounded-lg object-contain" :src="productImage(item)" alt="" />
            <div class="py-1">
                <h1 class="font-semibold">{{ item.name }}</h1>
                <p class="text-gray-700 text-xs">
                    {{ item.count }}
                </p>
            </div>
            
            <div class="pt-4 pb-2">
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">₱{{ item.rate }} per Night</span>
            </div>

            <div class="absolute top-1 right-1 p-1 bg-gray-100/50 rounded-md font-semibold text-sm items-center flex">
                <span class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                    </svg>
                </span>
                {{ getRating(item.reviews) }}/5
            </div>
        </div>
        
    </div>

</template>