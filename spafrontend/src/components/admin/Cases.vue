<script setup>
import { toRef, ref, watch } from 'vue';
import SimpleToast from '../SimpleToast.vue';
import axios from "axios";

const selectedItem = ref({});
const showModal = ref(false);
const modalTitle = ref('');
const toastMessage = ref('Oops!');

const props = defineProps({
    cases: { type: Array, default: [] },
});

watch(() => props.cases, (c, b) => { 
    console.log("Ini Cases Collection: ", c);
});

const takeAction = (item) => {
    selectedItem.value = item;
    modalTitle.value = 'Action';
    console.log("takeAction", selectedItem.value);
    toggleModal();
}

const updateStatus = async () => {
    console.log("updateStatus", selectedItem.value);

    try {
        const response = await axios.post('/api/admin/report/update/status/'+selectedItem.value.product_report_id+'/'+selectedItem.value.product_id, {
            state: 0,
        });
        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(response);
        }
        console.log("Update Response: ", response.data);

        if (props.cases.length>0) {
            props.cases.forEach(function(item, index, object) {
                console.log("cases", item)
                if (item.listing_category_id === selectedItem.value.listing_category_id) {
                    object.splice(index, 1);
                }
            });
        }
        toggleModal();
        toastMessage.value = response.data.message;
        showToast(1);
    } catch (error) {
        toastMessage.value = error.response.data.message;
        showToast(3);
    }
};

const toggleModal = ()=>{
    showModal.value = !showModal.value;
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
<div class="p-4">
    <div class="mb-8">
        <div class="grid grid-cols-1 gap-2">
            <div class="relative overflow-x-auto">

                <div class="mb-8">                
                    <h1 class="flex justify-start py-2 font-semibold text-lg">For Review Recent Alarming Cases</h1>
                    <div v-if="cases.length==0">No open case</div>
                    <table class="w-full text-sm text-left text-gray-700 border-gray-600 rounded-lg" v-if="cases.length>0">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Product Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Address
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Booked Dates
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Booking Status
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Booked By Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Booked By Email
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Report Category
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Report Remarks
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Action
                                </th>
                                
                            </tr>
                        </thead>

                        <tbody v-if="cases.length>0">
                            <tr class="bg-white border-b" v-for="item in cases">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap white:text-dark">
                                    {{ item.product_name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ item.product_address }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    From: {{ item.from }} To: {{ item.to }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.booking_status }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.name }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.email }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.related_to }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ item.remarks }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    <button @click="takeAction(item)" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-700 text-gray-900 hover:text-gray-200 font-semibold rounded-lg">Take Action</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
        <div class="relative w-auto my-6 mx-auto max-w-6xl">
            <!--content-->
            <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                <!--header-->
                <div class="flex items-start justify-between p-5 border-b border-solid border-slate-200 rounded-t">
                    <h3 class="text-3xl font-semibold">
                    {{ modalTitle }}
                    </h3>
                    <button class="" v-on:click="toggleModal()">
                    <span class="font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                    </button>
                </div>
                <!--body-->
                <div class="relative p-6 flex-auto">
                    <div class="mb-4 p-8">
                        Suspend Listing.
                    </div>
                </div>
                <!--footer-->
                <div class="flex items-center justify-end px-6 py-6 border-t border-solid border-slate-200 rounded-b">
                    <!-- 
                    <button class="text-teal-500 bg-transparent border border-solid border-teal-500 hover:bg-teal-500 hover:text-white active:bg-teal-600 font-bold uppercase text-sm px-4 py-2 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button" v-on:click="toggleModal()">
                    Close
                    </button>
                    <button class="text-cyan-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button" v-on:click="toggleModal()">
                    Save Changes
                    </button> 
                    -->
                    <div class="flex justify-end">
                        <button @click="updateStatus()" class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                            Proceed
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>
    <SimpleToast :toast-message="toastMessage" />
</div>
</template>