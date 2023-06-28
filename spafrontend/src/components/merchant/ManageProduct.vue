<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import ProductForm from './sections/ProductForm.vue';
import SimpleToast from '../SimpleToast.vue';
import AttributeForm from './sections/AttributeForm.vue';

const router = useRouter()
const authStore = useAuthStore();

const form = ref({});
const parent = ref({});
const attrForm = ref({});
const id = ref(0);
const productFromChild = ref(null);
const action = ref('PUT');
const toastMessage = ref('Oops!');
const groupAttributes = ref([]);

onMounted(() => {
    console.log("params", router.currentRoute.value);
    id.value = router.currentRoute.value.params.id;
    authStore.getToken();
    fetchData().catch(error => {
        error.message; // 'An error has occurred: 404'
        console.log("fetchData Error: ", error.message);
    }); 
});

const fetchData = async () => {
    try {
        const response = await axios.get('/api/partner/product/'+ id.value);
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        
        form.value = await response.data.data;
        parent.value = form.value.listing_category;

        attrForm.value = {
            id:0,
            product_id:form.value.id,
            important: 0,
            highlight: 0

        }
        
        console.log("Primary: ",  form.value);
        console.log("Parent: ",  parent.value);
        console.log("Form Attr", attrForm.value);
        regroupAttribute(form.value.product_attributes)
    } catch (error) {
        console.log("fetchData" ,error);
    }
}

const submitForm = async (data) => {
    try {
        const config = {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        };

        console.log("Primary:", data.inputData);
        console.log("Files:", data.inputFiles);

        const formData = new FormData();
        
        for( var i = 0; i < data.inputFiles.length; i++ ){
            let file = data.inputFiles[i];
            formData.append('files[' + i + ']', file);
        }

        let action_url = '/api/partner/product/' + data.inputData.id;
        formData.append('_method', 'put');
        formData.append('photos', data.inputData.photos);
        formData.append('name', data.inputData.name);
        formData.append('slug', data.inputData.slug);
        formData.append('description', data.inputData.description);
        formData.append('max_guest', data.inputData.max_guest);
        formData.append('rate', data.inputData.rate);
        formData.append('discount', data.inputData.discount);
        formData.append('enabled', data.inputData.enabled);
        formData.append('listing_category_id', data.inputData.listing_category_id);

        const response = await axios.post(action_url, formData, config);        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        form.value = response.data.data;
        console.log("response", form.value);
        toastMessage.value = "Record was successfully updated.";
        showToast(1);
        // activateCleaner();
        // router.push({ name: 'ViewPartnersProduct', params: {listingCategoryId:form.value.listing_category_id, id: form.value.id } });
    } catch (error) {
        console.log("error", error);
        toastMessage.value = error.message;
        showToast(3);
    }
}

const closeForm = () => {
    router.push({ name: 'MerchantProducts'});
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

const regroupAttribute = (attrs) => {

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
            not_included: title==='NOT INCLUDED',
            data: group_src
        }
        groupAttributes.value.push(group);
    });
    console.log("uniqueValues", uniqueValues)
    console.log("groupAttributes", groupAttributes.value);
}

const deleteAttr = async (item)=> {
    console.log("attr", item)
    try {
        const response = await axios.delete('/api/partner/product/attr/'+item.id, { data: item });        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        const rd = await response.data.data;
        console.log("rd", rd);
        router.go();
        
    } catch (error) {
        console.log("Error", error);
    }
}

const addAttribute = async (item)=> {
    console.log("addAttribute", item.data, "attrForm", attrForm);
    // return true;
    // partner/product/attr
    try {

        const formData = new FormData();
        let action_url = '/api/partner/product/attr';
        
        formData.append('product_id', item.data.product_id);
        formData.append('name', item.data.name);
        formData.append('value', item.data.value);
        formData.append('highlight', item.data.highlight);
        formData.append('important', item.data.important);
        
        const response = await axios.post(action_url, formData);        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        // form.value.product_attributes.push(response.data.data)
        console.log("Primary Form", form.value);
        toastMessage.value = "Record was successfully created.";
        showToast(1);
        router.go();
        // activateCleaner();
        // router.push({ name: 'ViewPartnersProduct', params: {listingCategoryId:form.value.listing_category_id, id: form.value.id } });
    } catch (error) {
        console.log("error", error);
        toastMessage.value = error.message;
        showToast(3);
    }
}

</script>

<template>
    <div class="flex md:flex-row flex-wrap">
        <div class="w-full">
            <h1 class="product-title">
                Manage your product
            </h1>
            <p class="my-4 text-md text-gray-700">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s
            </p>
        </div>
        <div class="w-full md:w-3/4 p-4">
            <div class="mx-auto max-w-2xl">
                <ProductForm 
                    ref="productFromChild" 
                    :parent="parent" 
                    :form="form" 
                    :action="action" 
                    @save-data="submitForm"
                    @close-form="closeForm"
                />
            </div>
        </div>
        <div class="w-full md:w-1/4 p-4">
            <div class="mb-4">
                <h2 class="text-title-md2 font-bold text-black white:text-dark">What this place offers</h2>
                <p>Lorem Ipsum has been the industry's standard...</p>
            </div>
            <div class="mb-4">
                <AttributeForm 
                    :form="attrForm" 
                    @save-data="addAttribute"
                />
            </div>
            <div class="mb-4">
                <h2 class="text-title-md2 font-bold text-black white:text-dark">Product Features/Attributes</h2>
            </div>
            
            <div>
                <div class="block h-full rounded-lg bg-gray-200 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 mb-4" v-for="item in groupAttributes">
                    <div class="border-b-2 border-neutral-100 border-opacity-100 p-1 text-center dark:border-opacity-10">
                        <p class="mt-1 text-sm uppercase">
                            <strong>{{ item.title }}</strong>
                        </p>
                    </div>

                    <!-- List -->
                    <div class="px-6 pb-6">
                        <ol class="list-inside">
                            <li class="mb-2 flex" v-for="v in item.data">
                                <button @click.prevent="deleteAttr(v)" class="mr-3 text-white-900 bg-[#F77E7E]/10 hover:bg-[#F77E7E] focus:ring-4 focus:outline-none focus:ring-[#F77E7E]/50 font-medium rounded-lg text-sm px-1 py-1 text-center inline-flex items-center dark:focus:ring-[#F77E7E]/100">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-5 w-5 text-primary dark:text-primary-100">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                {{ v.value  }}
                            </li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <SimpleToast :toast-message="toastMessage" />
</template>