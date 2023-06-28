<script setup>
import { ref, onMounted } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router'
import ProductForm from './sections/ProductForm.vue';
import SimpleToast from '../SimpleToast.vue';

const router = useRouter()

const form = ref({});
const parent = ref({});
const productFromChild = ref(null);
const action = ref('POST');
const toastMessage = ref('Oops!');

onMounted(() => {
    console.log("params", router.currentRoute.value.params);
    form.value.photos='';
    form.value.listing_category_id = router.currentRoute.value.params.listingCategoryId
    // form.value.discount = 0;
    form.value.enabled =1;
    
});


const submitForm = async (data) => {
    try {
        const config = {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        };

        // console.log("Primary:", data.inputData);
        // console.log("Files:", data.inputFiles);

        const formData = new FormData();
        const productPhotos = (data.inputData.photos.length==0 && data.inputFiles.length>0)? '-' : data.inputData.photos;
        
        for( var i = 0; i < data.inputFiles.length; i++ ){
            let file = data.inputFiles[i];
            formData.append('files[' + i + ']', file);
        }

        let action_url = '/api/partner/product';
        formData.append('photos', productPhotos);
        formData.append('name', data.inputData.name);
        formData.append('slug', data.inputData.slug);
        formData.append('description', data.inputData.description);
        formData.append('max_guest', data.inputData.max_guest);
        formData.append('rate', data.inputData.rate);
        formData.append('discount', data.inputData.discount);
        formData.append('enabled', data.inputData.enabled);
        formData.append('listing_category_id', data.inputData.listing_category_id);

                
        if (action.value==='PUT') {
            formData.append('_method', 'put');
            action_url = '/api/partner/product/' + data.inputData.id;
        }

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
        router.push({ name: 'ViewPartnersProduct', params: {listingCategoryId:form.value.listing_category_id, id: form.value.id } });
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



</script>

<template>
    <div class="mx-auto max-w-2xl">
        <div>
            <h1 class="product-title">
                What kind fo place will you host?
            </h1>
            <p class="my-4 text-md text-gray-700">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s
            </p>
        </div>
        <div>
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
    <SimpleToast :toast-message="toastMessage" />
</template>