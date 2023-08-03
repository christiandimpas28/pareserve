<script setup>
import { ref, onMounted } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import Property from './sections/Property.vue';
import PropertyFrom from './sections/PropertyForm.vue';
import SimpleToast from '../SimpleToast.vue';
import ProductBadge from './sections/ProductBadge.vue';

const router = useRouter()
const authStore = useAuthStore();

const propertyFromChild = ref(null);
const form = ref({});
const action = ref('POST');
const merchant = ref(0);
const isForm = ref(false);
const toastMessage = ref('Oops!');
const products = ref([]);
const isFetching = ref(false);


onMounted(() => {
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
            isFetching.value = false;
            throw new Error(message);
        }
        
        form.value = await response.data.data;
        action.value ='POST';
        merchant.value = form.value.id;
        products.value =[];

        console.log('My Products', form.value);

        if (form.value && form.value.listings && form.value.listings.length > 0) {
            // console.log("Listings", form.value.listings);
            // const items = form.value.listings.filter(function(item) {
            //     if (item['products'] === undefined || item['products'].length ==0) return false;
            //     return true;
            // });
            // console.log("My Products", items);
            form.value.listings.forEach( (item, index)=> {
                if (item && item.products !== undefined && item.products.length > 0) {
                    products.value = products.value.concat(item.products);
                }
            });
            // console.log("Concat", products.value)
        }
        isFetching.value = false;
    } catch (error) {
        console.log("onMounted fetchData" ,error);
        isFetching.value = false;
    }
}

//Clicks
const onAddListingCLick = (parentId) => {
    console.log("parentId", parentId);
    if (parentId === undefined){
        toastMessage.value = 'Merchant profile is required before you can add listing record.';
        showToast(2);
        return;
    }
    
    form.value = {};
    isForm.value = true;
    router.push({ name: 'AddListing', params: { merchantId: btoa(parentId) } });

    //Query push
    //.push({ name: <NAME OF THE ROUTE>, query: { type: 2 } }) ----->  /?type=2

    //Param push
    //.push({ name: <NAME OF THE ROUTE>, params: { type: 2 } }) ----->  /type/2
}

const submitForm = async (data) => {
    try {
        const config = {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        };

        const formData = new FormData();
        
        if (data.inputFiles !== null && data.inputFiles.length>0)
        {    
            for( var i = 0; i < data.inputFiles.length; i++ ){
                let file = data.inputFiles[i];
                formData.append('files[' + i + ']', file);
            }
        }
        console.log("Real Submit sata: ", data);
        // return;
        if (data.inputData.listing_photos==null) data.inputData.listing_photos='';
        
        let action_url = '/api/partner/listings';
        formData.append('listing_photos', data.inputData.listing_photos);
        formData.append('name', data.inputData.name);
        formData.append('category', data.inputData.category);
        formData.append('description', data.inputData.description);
        formData.append('address', data.inputData.address);
        formData.append('city', data.inputData.city);
        formData.append('merchant_id', merchant.value);

        formData.append('discount_title', (data.inputData.discount_title == undefined)? '': data.inputData.discount_title);
        formData.append('discount_rate', (data.inputData.discount_rate==undefined)?'':data.inputData.discount_rate);
        formData.append('discount_condition', (data.inputData.discount_condition==undefined)? '': data.inputData.discount_condition);
        
        if (action.value==='PUT') {
            formData.append('_method', 'put');
            // formData.append('id',  data.inputData.id);
            // formData['merchant_id'] = data.inputData.merchant_id;
            // formData['listing_photos'] = data.inputData.listing_photos;
            action_url = '/api/partner/listings/' + data.inputData.id;
        }

        const response = await axios.post(action_url, formData, config);        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        isForm.value = false;
        toastMessage.value = "Record was successfully saved.";
        showToast(1);
        activateCleaner();
        router.push({ name: 'MerchantListings' });
    } catch (error) {
        console.log("error", error);
        toastMessage.value = error.message;
        showToast(3);
    }
}

const deleteForm =  async (data) => {
    console.log("Delete Form", data);
    const formData = new FormData();

    formData.append('id', data.inputData.id);
    formData.append('merchant_id', data.inputData.merchant_id);

    try {
        const response = await axios.delete('/api/partner/listings/'+data.inputData.id, formData);        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        const rd = await response.data.data;
        toastMessage.value = "Record was successfully deleted.";
        showToast(1);
        activateCleaner();
        router.push({ name: 'MerchantListings' });
    } catch (error) {
        console.log("delete", error.response.data);
        toastMessage.value = error.message;
        if (error.response.status===400){
            toastMessage.value = error.response.data.message;
        }
        
        showToast(3);
    }
}

const activateCleaner = () => {
    isForm.value=false;
    propertyFromChild.value.cleaner();
}

const closeForm = () => {
    isForm.value=false;
    router.push({ name: 'MerchantListings'});
    // activateCleaner();
}


const showToast = (mode) => {
    let el = document.getElementById("toast-success");
    if (mode==2) el = document.getElementById("toast-warning");
    if (mode==3) el = document.getElementById("toast-danger");
    
    
    el.classList.remove("hidden"); 
    setTimeout(function () {
        el.classList.add("hidden");
    }, 5000);
}

router.beforeResolve((to, from) => {
    // console.log('User Authenticated')
    console.log('Coming from:', from.path);
    console.log('Going to:', to.path);
    console.log('Route Param', to.name);
    // console.log('Route ID', router.params.id);
    isFetching.value =true;
    if (to.name==='MerchantListings') {
        
        fetchData().catch(error => {
            error.message; // 'An error has occurred: 404'
            console.log("fetchData Error: ", error.message);
            isForm.value=false;
        }); 
        isForm.value=false;
    }

    if (to.name==='EditListing' && to.params && to.params.id != undefined) {
        isForm.value=true;
        
        const selected = {
            id: to.params.id,
        };
        
        let item = null;
        if (form.value.listings && Array.isArray(form.value.listings)){
            item = form.value.listings.filter(function(item) {
                for (var key in selected) {
                    if (item[key] === undefined || item[key] != selected[key]) return false;
                }
                return true;
            });
        }
        

        // console.log("selected", selected, "Form", form.value, "selected", item);
        if (item && item.length == 1) { 
            form.value=item[0]; 
            action.value ='PUT';
            console.log("selected", form.value);
            return true;
        } else {
            return false;
        }
        
    }
})

</script>


<template>
    
    <div class="flex md:flex-row flex-wrap">
        <div class="w-full md:w-3/4 p-4">
            <div v-show="!isForm">
                <button @click="onAddListingCLick(form.id)" class="text-gray-900 bg-[#92d9f7] hover:bg-[#92d9f7]/90 focus:ring-4 focus:outline-none focus:ring-[#92d9f7]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#92d9f7]/50 mr-1 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Listing
                </button>
                <div class="m-6" v-show="isFetching">
                    <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status">
                        <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">Loading...</span>
                    </div>
                </div>
                <div class="grid grid-flow-row my-4 gap-4 text-neutral-600 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
                    <div v-for="item in form.listings">
                        <Property :form="item" />
                    </div>
                </div> 
            </div>
            <div v-show="isForm">
                <PropertyFrom 
                    ref="propertyFromChild"
                    :form="form"
                    :action="action" 
                    @savedata="submitForm"
                    @deletedata="deleteForm"
                    @closeform="closeForm" 
                />
            </div>

        </div>
        <div class="w-full md:w-1/4 p-4">
            <h1 class="text-md font-bold text-black white:text-dark">
                <span class="inline-flex items-center justify-center min-w-6 min-h-6 p-3 ml-2 text-xs font-semibold text-cyan-800 bg-cyan-200 rounded-full">{{ products.length  }}</span>
                Products
            </h1>
            <div class="flex justify-start flex-wrap py-10 px-6 gap-1">
                <!-- Badge Type
                <div class="max-w-[80%]" v-for="item in products">
                    <ProductBadge :form="item" />
                </div>
                -->
                
                <!-- List Type -->
                <ul class="flex flex-wrap items-center justify-start mb-6 text-gray-900 white:text-white space-y-2 list-disc">
                    <li v-for="item in products">
                        <ProductBadge :form="item" />
                    </li>
                </ul>

            </div>
        </div>
        
    </div>
    <SimpleToast :toast-message="toastMessage" />
</template>