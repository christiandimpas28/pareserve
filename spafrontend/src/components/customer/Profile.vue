<script setup>
import { ref, onMounted } from 'vue';
import axios from "axios";

import { useAuthStore } from '../../stores/auth';
import ProfileForm from './sections/ProfileForm.vue/';
import ProfileView from './sections/ProfileView.vue';

const authStore = useAuthStore();
console.log("My Account Profile:", authStore.user);

const form = ref({});
const formState = ref(0);

onMounted(() => {

    authStore.getToken();
    fetchData().catch(error => {
        error.message; // 'An error has occurred: 404'
        console.log("fetchData Error: ", error.message);
    }); 
});

const fetchData = async () => {
    try {
        formState.value=-1;
        const response = await axios.get('/api/customer/profile');
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        
        form.value = await response.data;
        if (form.value != undefined && form.value.customerprofile !== null) {
            formState.value = 1;
        } else {
            formState.value = 2;
        }
        console.log("Profile: ",  form.value, " State:" , formState.value, " CP", form.value.customerprofile);

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

        let action_url = '/api/customer/profile/';
  
        formData.append('first_name', data.inputData.first_name);
        formData.append('last_name', data.inputData.last_name);
        formData.append('id_type', data.inputData.id_type);
        formData.append('billing_address', data.inputData.billing_address);
        formData.append('contact_no', data.inputData.contact_no);

        const response = await axios.post(action_url, formData, config);        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        form.value = response.data;
        console.log("Save response", form.value);
        formState.value=1;

        // activateCleaner();
        // router.push({ name: 'ViewPartnersProduct', params: {listingCategoryId:form.value.listing_category_id, id: form.value.id } });
    } catch (error) {
        console.log("error", error);
    }
}

const closeForm = () => {
    // router.push({ name: 'CustomerProfile'});
    console.log("closeForm", formState.value);
    formState.value=1;
}

const editForm = () => {
    formState.value=2;
}

</script>

<template>
    <div class="pb-12 pt-10 md:pt-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <div class="mx-auto w-full max-w" v-if="formState==0">
                <h1 class="text-2xl sm:text-xl font-medium flex justify-center">
                    Update your profile information
                </h1>
                <p class="flex justify-center">
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                </p>
                <div class="flex justify-center">
                    <button class="text-white my-4 bg-teal-700 hover:bg-teal-800 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-teal-600 dark:hover:bg-teal-700 dark:focus:ring-teal-800" @click.prevent="formState=2">
                        Start
                    </button>
                </div>
            </div>
            <!-- 
            "name": "Customer A",
            "email": "a@customer.com",
            "user_type": "Customer",
            "customerprofile": null 
            -->

            <template v-if="formState==1">
                <ProfileView :form="form" :header="{ title: 'Profile', subTitle: '' }" @edit-form="editForm" />
            </template>

            <template v-if="formState==2">
                <ProfileForm 
                    :form="form.customerprofile==null?{}:form.customerprofile" 
                    :header="{ title: 'Profile', subTitle: '' }" 
                    @close-form="closeForm" 
                    @save-data="submitForm"
                    />
            </template>
        </div>
    </div>
    

    
</template>