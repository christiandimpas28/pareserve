<script setup>
import axios from "axios";
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import IntegrationGuide from './sections/IntegrationGuide.vue'
import IntegrationKeys from './sections/IntegrationKeys.vue';

const authStore = useAuthStore();
const router = useRouter()

const form = ref({});
const merchant = ref(0);
const isFetching = ref(true);

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
        const response = await axios.get('/api/integration/keys');
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            isFetching.value = false;
            throw new Error(message);
        }
        
        const rensponseData = await response.data.data;
        
        form.value = rensponseData.integration;
        merchant.value = rensponseData.merchant_id;

        console.log('My Integration', rensponseData);
        isFetching.value = false;
    } catch (error) {
        console.log("onMounted fetchData" ,error);
        isFetching.value = false;
        if (error.response.statusText === "Unauthorized"){
            router.push({ name: 'Error401'})
        }

        if (error.response.status === 403){
            router.push({ name: 'Error403'})
        }
    }
}

const submit = async (id) => {
    try {

        if (isFetching.value ===true) {
            return;
        }

        const el = document.getElementById('submit');
        if (el.textContent==='Generating...') return false;
        el.textContent = 'Generating...';
        const action_url = '/api/integration/keys/'+id;
        const formData = new FormData();
        formData.append('merchant_id',id);
        const response = await axios.post(action_url, formData);        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        
        form.value = await response.data.data;
        // console.log("form", form.value);
        // console.log("submit", response);
    } catch (error) {
        console.log("method submit" ,error);
    }
    
}

</script>
<template>
    <div class="p-2 inline-grid gap-2 grid-cols-2 w-full text-md">
        <!-- Left Pane-->
        <div>
            <div class="mb-4">
                <IntegrationGuide></IntegrationGuide>
            </div>
        </div>

        <!-- Right Pane-->
        <div>
            <div class="mb-4">
                <IntegrationKeys :form="form" :merchant_id="merchant" @request-data="submit" />
            </div>
        </div>


    </div>

</template>