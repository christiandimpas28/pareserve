<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router';

import SearchForm from './sections/SearchForm.vue';
import SearchResult from './sections/SearchResult.vue';

const router = useRouter()
const collection = ref([]);
const isFetching = ref(false);

const search = ref({});


onMounted(() => {
    // console.log("Router", router.currentRoute.value.query)
    //Check Url Queries
    let search_url = '/api/product/search?query=';
    let val='';

    search.value = {
        query: '',
        start: null,
        end: null,
    }

    if (router.currentRoute.value.query.query!==undefined) {
        val = router.currentRoute.value.query.query;
        search_url += val;
        search.value.query = decodeURIComponent(val);
    } 

    if (router.currentRoute.value.query.start!==undefined) {
        val = router.currentRoute.value.query.start;
        search_url += '&start=' + val;
        search.value.start = decodeURIComponent(val);
    }

    if (router.currentRoute.value.query.end!==undefined) {
        val = router.currentRoute.value.query.end;
        search_url += '&end=' + val;
        search.value.end = decodeURIComponent(val);
    }
    

    console.log("Get URL" , search_url, search);


    fetchData(search_url).catch(error => {
        error.message; // 'An error has occurred: 404'
        console.log("fetchData Error: ", error.message);
    }); 
});

const fetchData = async (get_url) => {
    try {
        isFetching.value=true;
        console.log("get_url", get_url);
        const response = await axios.get(get_url);
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        collection.value = await response.data.data;
        console.log("Search Result",collection.value);
        isFetching.value=false;
    } catch (error) {
        console.log("onMounted fetchData" ,error);
    }
}

const getSearch = (param) => {
    
    const val = (param.query !== undefined) ? encodeURIComponent(param.query):'';  
    const start = encodeURIComponent(param.start)  
    const end = encodeURIComponent(param.end) 
    // router.push({ name: 'ListingsSearch', query: { query: val, start: start, end: end } })

    let search_url = '/api/product/search?query='+val+'&start='+start+'&end='+end;
    console.log("Search data", param, "URL", search_url);
    fetchData(search_url).catch(error => {
        error.message; // 'An error has occurred: 404'
        console.log("fetchData Error: ", error.message);
    }); 
}

const viewDetails = (item) => {
    console.log("viewDetails", item);
    router.push({ name: 'ViewProductDetails', params: { id: item.id }, query: { slug: item.slug, listing: item.listing_category_id, rate: item.rate, enabled: item.enabled, maxguest: item.max_guest } });
}

watch(() => isFetching.value, (c, b) => { 
    // console.log("Watch Fetching", c, "b", b);
    // console.log("Watch Collection", (c!=null && c.length>1 && c.img === undefined));
    if (collection.value!=null && collection.value.length>0) {
        collection.value.forEach( (item, index) => {
            const photos = item.photos.split(',');
            if (photos.length>0 && photos[0].indexOf(".")>0) {
                item['img'] = 'http://localhost:8000/uploads/' + photos[0]
            }
        });
    }
});

</script>

<template>
    <div class="pb-12 pt-10 md:pt-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            
            <SearchForm @search-form="getSearch" :form="search" />

            <div class="flex md:flex-row flex-wrap">
                <div class="w-full md:w-1/4 p-4">
                    -
                </div>
                <div class="w-full md:w-3/4 p-4">
                    <SearchResult
                        :collection="collection" 
                        :title="'Your Search'" 
                        :subtitle="'Lorem ipsum sum sum, Xi xtru..'" 
                        @view-details="viewDetails"
                    />
                </div>
            </div>
            
        </div>
    </div>
</template>