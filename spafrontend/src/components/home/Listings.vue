<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router';


import ExploreListSm from './sections/ExploreListSm.vue';
import ExploreListLg from './sections/ExploreListLg.vue';
import Hero from './sections/HeroFrom.vue';
import SearchCategory from './sections/searchCategoty.vue';

const router = useRouter()

const collection = ref([]);
const litings = ref([]);
const products = ref([]);
const searchForm = ref({});
const exploreListSmCollection = ref([]);
const isFetching = ref(true);


onMounted(() => {
    fetchData('/api/products').catch(error => {
        error.message; // 'An error has occurred: 404'
        console.log("fetchData Error: ", error.message);
    }); 

    exploreListSmCollection.value = [
        { img: 'http://localhost:8000/uploads/cdo.jpg', name: 'CDO', query: 'cagayan' },
        { img: 'http://localhost:8000/uploads/davao.jpg', name: 'Davao', query: 'davao' },
        { img: 'http://localhost:8000/uploads/zamboanga.jpeg', name: 'Zamboanga', query: 'Zamboanga' },
        { img: 'http://localhost:8000/uploads/tagum.jpg', name: 'Tagum', query: 'Tagum' },
        { img: 'http://localhost:8000/uploads/iligan.jpg', name: 'Iligan', query: 'Iligan' },
        { img: 'http://localhost:8000/uploads/cotabato.jpg', name: 'Cotabato', query: 'Cotabato' },
        { img: 'http://localhost:8000/uploads/gensan.jpg', name: 'Gensan', query: 'Gensan' },
    ];
});


watch(() => products.value, (c, b) => { 
    if (c!=null && c.length>1) {
        c.forEach( (item, index) => {
            const photos = item.photos.split(',');
            if (photos.length>0 && photos[0].indexOf(".")>0) {
                item['img'] = 'http://localhost:8000/uploads/' + photos[0]
            }
        });
    }
});

const search = async (param) => {
    console.log("Search data", param);
    const val = (param.query !== undefined) ? encodeURIComponent(param.query):'';  
    const start = encodeURIComponent(param.start)  
    const end = encodeURIComponent(param.end)  
    router.push({ name: 'ListingsSearch', query: { query: val, start: start, end: end } })
    // try {
    //     let val = encodeURIComponent(param.query)  
    //     const get_url = '/api/product/search?query=' + val
    //     const response = await axios.get(get_url);
    //     if (!response) {
    //         const message = 'An error has occured: ${response.status}';
    //         throw new Error(message);
    //     }

    //     const sr = await response.data.data;
    //     console.log("Search",sr);
    // } catch (error) {
    //     console.log("onMounted fetchData" ,error);
    // }
}

const viewDetails = (item) => {
    // console.log("viewDetails", item);
    router.push({ name: 'ViewProductDetails', params: { id: item.id }, query: { slug: item.slug, listing: item.listing_category_id, rate: item.rate, enabled: item.enabled, maxguest: item.max_guest } });
}

const fetchData = async (get_url) => {
    try {
        
        const response = await axios.get(get_url);
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        
        collection.value = await response.data.data;
        console.log("collection.value" , collection.value)
        // action.value ='POST';
        // merchant.value = collection.value.id;
        products.value =[];
        litings.value = [];
        // console.log('My Products', collection.value);
        if (collection.value && collection.value.length>0){
            collection.value.forEach( (item, index) => {
                if (item.listings && item.listings.length>0) {
                    litings.value = litings.value.concat(item.listings);
                }
            });
        }

        if (litings.value.length>0){
            litings.value.forEach( (item, index) => {
                if (item.products && item.products.length>0) {
                    products.value = products.value.concat(item.products);
                }
            });
        }

        console.log("litings", litings.value);
        console.log("products", products.value);
        isFetching.value = false;
    } catch (error) {
        console.log("onMounted fetchData" ,error);
    }
}
</script>

<template>
    <Hero @search-form="search" :form="searchForm" />
    <div class="pb-12 pt-10 md:pt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex-1">
                <ExploreListSm :collection="exploreListSmCollection" :title="'Explore Mindanao'" :subtitle="'These popular destinations have a lot to offer'" />
            </div>
            <div class="flex-1">
                <ExploreListLg 
                    :collection="products" 
                    :title="'Stay at our top unique properties'" 
                    :subtitle="'Lorem ipsum sum sum..'" 
                    @view-details="viewDetails"
                />
            </div>
            <div>
                <SearchCategory></SearchCategory>
            </div>
        </div>
    </div>
</template>