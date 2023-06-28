<script setup>
import {ref} from 'vue';
import ProfileForm from './sections/ProfileForm.vue';

const form = ref({
    name: 'Red Planet',
    bus_contact_name: 'kaka',
    bus_contact_no: '234123',
    bus_email: 'kaka@dd.com',
    bus_address: 'didto',
});

defineExpose({ form })
console.log("Parent Script Setup");
</script>



<template>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <ProfileForm
                title="OI OI KAKA"
                desc="BOOM"
                :data="form"
                v-model:name="form.name" 
                v-model:bus_contact_name="form.bus_contact_name"  
                v-model:bus_contact_no="form.bus_contact_no" 
                v-model:bus_email="form.bus_email" 
                v-model:bus_address="form.bus_address" 
                @save-data="submitForm"
            />
        </div>
        <div>2</div>
        <div>3</div>
        <div>4</div>
        <div>5</div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="grid grid-flow-col justify-stretch">
            <form>
                <div class="mb-6">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">Business Name</label>
                        <input type="text" id="name" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Business/Company Name" required>
                    </div>
                </div> 
                <div class="mb-6">
                    <div>
                        <label for="bus_address" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">Business Address</label>
                        <input type="text" id="bus_address" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Business/Company Address" required>
                    </div>
                </div>
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="bus_contact_name" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">Primary Contact Name</label>
                        <input type="text" id="bus_contact_name" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Contact Name" required>
                    </div>
                    <div>
                        <label for="bus_contact_no" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">Primary Contact #</label>
                        <input type="text" id="bus_contact_no" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Contact Number" required>
                    </div>
                    <div>
                        <label for="bus_email" class="block mb-2 text-sm font-medium text-gray-900 white:text-white">Business Email Address</label>
                        <input type="email" id="bus_email" class="block w-full rounded-md border-0 p-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Business Email" required>
                    </div>  
                </div>
                <button type="submit" class="text-white bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">Save</button>
            </form>
        </div>
        <div class="flex items-center justify-center rounded">
            
        </div>
    </div>


</template>
<script>
export default {
    components: { ProfileForm },
    inheritAttrs: true,    
    data() {
        return {
            formData : {
                id: 0,
                name: null,
                bus_contact_name: null,
                bus_contact_no: null,
                bus_email: null,
                bus_address: null,
            } 
        }
    },
    mounted() {
        console.log("Normal mount");
        // this.fetchData();
    },
    methods: {
        fetchData() {
            axios.get('/sanctum/csrf-cookie').then(response => {
                // console.log(response);
                axios.get("/api/partner/profile")
                .then(response => {
                    console.log("Profile:", response);
                    this.formData = response.data.data
                }).catch(error => {
                    console.log(error.response.status);
                    // this.formData = null;
                })
            });
            
        },
        submitForm(obj) {
            console.log("FORM DATA: ", obj);
        }
    }
}
</script>