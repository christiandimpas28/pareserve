<script setup>
// https://github.com/mohamadkhani/vue-simple-range-slider
import { ref, onMounted, watch, computed } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router';

import SearchForm from './sections/SearchForm.vue';
import SearchResult from './sections/SearchResult.vue';

const router = useRouter();
const collection = ref([]);
const view_collection = ref([]);
const isFetching = ref(false);
const price_end = ref(5000);
const price_max = ref(5000);

const search = ref({});

const price_end_view = computed( () => { 
    return '₱ '+ moneyFormat(price_end.value);
});

const moneyFormat = (v) =>{
    try {
        if (isNaN(v)) throw "Not a Number" 
        const fv = parseFloat(v);
        return fv.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    } catch (error) {
        return "0.00"
    }
}

const roundUp = (number,near) => {
    if(number%near===0) return number;
        return parseInt(number / near) * near+near;
}


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
        if (collection.value !== null && collection.value.length>0){
            view_collection.value = collection.value;
        
            price_end.value  = Math.max.apply(Math, view_collection.value.map(function (item) {
                return item.rate;
            }));

            price_end.value = (price_end.value / 1000).toFixed(3) * 1000;
            price_end.value = roundUp(price_end.value, 1000);
            if (price_end.value > price_max.value) price_max.value = price_end.value;
        }
        
        console.log("Search Result",price_end.value );
        isFetching.value=false;
    } catch (error) {
        console.log("onMounted fetchData" ,error);
    }
}

const getMaxPrice = () => {
    // price_max.value = (view_collection.value.reduce(function (prev, current) {
    //     return (prev.rate > current.rate) ? prev : current
    // })).rate;

    price_max.value  = Math.max.apply(Math, view_collection.value.map(function (item) {
        return item.rate;
    }));

    console.log("Max Price:", price_max.value);
}

// const emits = defineEmits(["updatedcount"])
// emits("updatedcount", store.count);

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

const setPrice = (v) => {
    price_end.value = v;
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

watch(() => price_end.value, (c, b) => { 
    if (collection.value!=null && collection.value.length>0) {
        view_collection.value = collection.value.filter(function(item) {
            if (parseFloat(item.rate) > parseFloat(price_end.value)) return false;
            return true;
        });
        // collection.value = new_items
    }
});

</script>

<template>
    <div class="pb-12 pt-10 md:pt-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            
            <SearchForm @search-form="getSearch" :form="search" />

            <div class="flex md:flex-row flex-wrap">
                <div class="w-full md:w-1/4 p-4">
                    <h4 class="mb-4 text-base font-medium">Advance Filter</h4>
                    <div class="rounded-lg shadow-lg">
                        <p class="text-sm">Filter by price below or equal</p>
                        <div class="price-range py-2">
                            <span class="text-sm">{{ price_end_view }}</span>
                            <input class="w-full accent-teal-600" 
                                type="range" 
                                name="price" 
                                :value="price_end" 
                                min="0" 
                                :max="price_max"
                                @input="setPrice($event.target.value)"
                            />
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-3/4 p-4">
                    <SearchResult
                        :collection="view_collection" 
                        :title="'Your Search'" 
                        :subtitle="'Unwind in sumptuously appointed rooms and suites that redefine comfort.'" 
                        @cviewDetails="viewDetails"
                    />
                </div>
            </div>
            
        </div>
    </div>
</template>