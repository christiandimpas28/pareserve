<script setup>
import axios from "axios";
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';

const router = useRouter()
const authStore = useAuthStore();

const id = ref(0);
const form = ref({});
const parent = ref({});
const imagePreviewCollection = ref([]);
const groupAttributes = ref([]);

onMounted(() => {
    console.log("params", router.currentRoute.value.params);
    id.value = router.currentRoute.value.params.id;
    authStore.getToken();
    fetchData().catch(error => {
        error.message; // 'An error has occurred: 404'
        console.log("fetchData Error: ", error.message);
    }); 
});

watch(() => form.value.photos, (c, b) => {
    if (form.value.photos !== undefined && form.value.photos != null && form.value.photos.length>0){
        const arr_photos = form.value.photos.split(',');
        imagePreviewCollection.value=[];
        arr_photos.forEach( (item, index)=> {
            let img_name = item.replace(/^\s+|\s+$/gm,'');
            console.log(index, "Item:", img_name);
            imagePreviewCollection.value.push({
                id: index,
                product_id: form.value.id,
                listing_category_id: form.value.listing_category_id,
                name: img_name,
                url: 'http://localhost:8000/uploads/' + img_name
            });
        }); 
    }
});

const fetchData = async () => {
    try {
        const response = await axios.get('/api/partner/product/'+ id.value );
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        
        form.value = await response.data.data;
        parent.value = form.value.listing_category;
        console.log("Product: ",  form.value);
        console.log("Listing: ",  form.value.listing_category);
        regroupAttribute(form.value.product_attributes);
    } catch (error) {
        console.log("fetchData" ,error);
    }
}

const baseRate = computed( () => { 
    return '₱'+ moneyFormat(form.value.rate);
});

const extraPaxRate = computed( () => { 
    return '₱'+ moneyFormat(form.value.extra_pax_rate);
});

const extraBedRate = computed( () => { 
    return '₱'+ moneyFormat(form.value.extra_bed_rate);
});

const breakfastRate = computed( () => { 
    return '₱'+ moneyFormat(form.value.breakfast_rate);
});

const maxExtraBed = computed( () => { 
    if (isNaN(form.value.max_guest)) return '0';
    return form.value.max_guest - form.value.min_guest;
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

const regroupAttribute = (attrs) => {
    // const attrs = [
    //     { name: 'Outdoor', value: 'Fire Pit'}, 
    //     { name: 'OutdooR', value: 'Outdoor dining area'},
    //     { name: 'Home Safety', value: 'Security cameras on property' },
    //     { name: 'Outdoor', value: 'Grill'}, 
    //     { name: 'Home safety', value: 'Fire extinguisher' },
    //     { name: 'Facilities', value: 'Parking' },
    //     { name: 'Not Included', value: 'Washer' },
    //     { name: 'Not Included', value: 'First aid kit' },
    //     { name: 'Home safety', value: 'Smoke alarm' },
    //     { name: 'Not Included', value: 'Air conditioning' },
    //     { name: 'facilities', value: 'Pool' },
    //     { name: 'Not Included', value: 'Hair dryer' },
    // ];

    // let unique_values = [
    //     ...new Set(employees_details.map((element) => element.age)),
    // ];
    // return unique_values;

    // Get all value in a array of a given key
    const valueArray = attrs.map((attr)=>attr.name.toLowerCase());
    // console.log("valueArray", valueArray);

    const setOfValue = new Set(valueArray)
    const uniqueValues = [...setOfValue]

    uniqueValues.forEach( (item, index) => {

        let group_src = attrs.filter(function(attr) {
            if (attr['name'].toLowerCase() === item) return true;
            return false;
        });

        let title = item.toUpperCase();
        let group =  {
            name: item,
            title: title,
            not_included: title==='NOT INCLUDED' || title==='NOT ALLOWED',
            data: group_src
        }
        groupAttributes.value.push(group);
    });
    // console.log(uniqueValues)
    // console.log("groupAttributes", groupAttributes.value);
}

</script>
<template>
    <div class="flex md:flex-row flex-wrap">
        <div class="w-full">
            <h4 class="text-sm bg-sky-500">{{ parent?.name  }}</h4>
            <h1 class="product-title">
                {{ form?.name }} @ {{ baseRate }} <span>rate per night</span>
            </h1>
            <p class="my-2 text-md text-gray-700">
                {{ form?.description }}
            </p>
            
            <router-link :to="{ name: 'ManageProduct', params: { listingCategoryId:form.listing_category_id, id:form.id } }" class="text-gray-900 bg-cyan-500 hover:bg-cyan-300 focus:ring-4 focus:outline-none focus:ring-[#92d9f7]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#92d9f7]/50 mr-1 mb-2">
                Edit Product
            </router-link>
        </div>
        <div class="w-full md:w-3/4 py-4">
            <div class="mb-4">
                <dl class="grid max-w-screen-xl rounded-lg grid-cols-4 gap-8 p-4 mx-auto text-gray-900 sm:grid-cols-3 xl:grid-cols-4 white:text-dark sm:p-8 bg-yellow-200">
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-lg font-bold">{{ baseRate }}</dt>
                        <dd class="text-gray-500 dark:text-gray-700 text-sm font-semibold">Nightly Rate</dd>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-lg font-bold">{{ extraPaxRate }}</dt>
                        <dd class="text-gray-500 dark:text-gray-700 text-sm font-semibold">Extra Guest</dd>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-lg font-bold">{{ extraBedRate }}</dt>
                        <dd class="text-gray-500 dark:text-gray-700 text-sm font-semibold">Extra Bed</dd>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-lg font-bold">{{ breakfastRate }}</dt>
                        <dd class="text-gray-500 dark:text-gray-700 text-sm font-semibold">breakfast Add-on</dd>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-lg font-bold">{{ form?.min_guest }}</dt>
                        <dd class="text-gray-500 dark:text-gray-700 text-sm font-semibold">Standard No. of Guest</dd>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-lg font-bold">{{ form?.max_guest }}</dt>
                        <dd class="text-gray-500 dark:text-gray-700 text-sm font-semibold">Maximum Guest</dd>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-lg font-bold">{{ maxExtraBed }}</dt>
                        <dd class="text-gray-500 dark:text-gray-700 text-sm font-semibold">Maximum Extra Bed</dd>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-lg font-bold"><span class="text-sm font-normal">Age below</span> {{ form?.free_below_age }}</dt>
                        <dd class="text-gray-500 dark:text-gray-700 text-sm font-semibold">Free Of Charge</dd>
                    </div>
                    
                </dl>
            </div>

            <div>
                <h2 class="mb-3 text-center text-2xl font-bold">Product Attributes</h2>
                <p class="mb-8 text-center text-neutral-500 dark:text-neutral-300">
                    What this place offers
                </p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="block h-full rounded-lg bg-gray-200 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700" v-for="item in groupAttributes">
                        <div class="border-b-2 border-neutral-100 border-opacity-100 p-6 text-center dark:border-opacity-10">
                            <p class="text-sm uppercase">
                                <strong>{{ item.title }}</strong>
                            </p>
                        </div>

                        <!-- List -->
                        <div class="px-4 pb-4">
                            <ol class="list-inside">
                                <li class="mb-4 flex text-normal items-center" v-for="v in item.data">
                                    <template v-if="!item.not_included">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="4"
                                        stroke="currentColor" class="mr-3 h-4 w-4 text-green-700 dark:text-text-green-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        {{ v.value  }}
                                    </template>
                                    <template v-else>
                                        <svg fill="none" stroke="currentColor" stroke-width="4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="mr-3 h-4 w-4 text-red-600 dark:text-red-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span class="line-through">{{ v.value  }}</span>
                                    </template>
                                    
                                </li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/4 py-4 pl-4">
            <div class="flex w-2/2 flex-wrap" v-for="item in imagePreviewCollection">
                <div class="w-full md:p-1 object-cover h-36 mb-2">
                    <img
                        alt="gallery"
                        class="block h-full w-full rounded-lg object-cover object-center"
                        :src="item?.url" />
                </div>
            </div>
        </div>
        
        
</div>
</template>