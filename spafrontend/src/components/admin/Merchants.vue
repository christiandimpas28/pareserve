<script setup>
import { toRef, ref, watch, onMounted } from 'vue';
import SimpleToast from '../SimpleToast.vue';
import axios from "axios";

const props = defineProps({
    merchants: { type: Array, default: [] },
});

// toRef(() => props.merchants);

const pendings = ref([]);
const showModal = ref(false);
const modalTitle = ref('');

const docFiles = ref([]);
const docImages = ref([]);

const selectedItem = ref({});
const toastMessage = ref('Oops!');

watch(() => props.merchants, (c, b) => { 
    console.log("Ini Collection: ", c);
    if (c!==null && c.length>0) {
        pendings.value.length = 0;
        c.forEach( (item, idx) => {
            if (item.status !== 1) {
                pendings.value.push(item);
            }
        });
    }
});

onMounted(() => {
    console.log("Ini Collection onMounted: ", props.merchants.length);
    if (props.merchants !==null && props.merchants.length>0) {
        pendings.value.length = 0;
        props.merchants.forEach( (item, idx) => {
            if (item.status !== 1) {
                pendings.value.push(item);
            }
        });
    }
});

const toggleModal = ()=>{
    showModal.value = !showModal.value;
}

const review = (item, title='Review Application') => {
    modalTitle.value = title;
    selectedItem.value = item;

    docFiles.value.length=0;
    docImages.value.length=0;
    docFiles.value= [];
    docImages.value= [];

    if (item.documents !== null && item.documents.length>0) {
        const arr_files = item.documents.split(',');
        if (arr_files.length>0){
            

            arr_files.forEach( (item,i) => {
                let item_name = item.replace(/^\s+|\s+$/gm,'');
                let isImage = isFileImage(item_name);
                if (!isImage) {
                    docFiles.value.push({
                        filename: item_name,
                        url: 'http://localhost:8000/uploads/merchants/' + item_name
                    });
                } else {
                    docImages.value.push({
                        filename: item_name,
                        url: 'http://localhost:8000/uploads/merchants/' + item_name
                    });
                }
            });
        }
        // console.log("arr_files", docFiles.value, " img:", docImages.value);
    }

    // console.log("For Review Application: ", item);
    toggleModal();
};

const getFileExtension = (filename) => {
    const extension = filename.substring(filename.lastIndexOf('.') + 1, filename.length);
    return extension;
}

const isFileImage = (filename) => {
    const imagesExt = Array('gif', 'jpg', 'jpeg', 'tif', 'png');
    const fileExt = getFileExtension(filename);

    const exist = imagesExt.find( (ext) => {
        return ext.toLowerCase() === fileExt.toLowerCase();
    });

    if (exist == undefined) return false;
    return true;
}

const updateStatus = async () => {
    // console.log("selectedItem", selectedItem.value);
    try {
        const response = await axios.post('/api/admin/merchant/update/status/'+selectedItem.value.merchant_id, {
            status: selectedItem.value.status,
            status_remarks: selectedItem.value.status_remarks,
        });
        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(response);
        }
        console.log("Update Response: ", response.data.data);

        if (pendings.value.length>0) {
            pendings.value.forEach(function(item, index, object) {
                // console.log("pendings", item.merchant_id, " smi:", selectedItem.value.merchant_id, " status:", item.status)
                if (item.merchant_id === selectedItem.value.merchant_id && item.status == 1) {
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
                    <h1 class="flex justify-start py-2 font-semibold text-lg">For Review Applications</h1>
                    <div v-if="pendings.length==0">No new partners</div>
                    <table class="w-full text-sm text-left text-gray-700 border-gray-600 rounded-lg" v-if="pendings.length>0">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Business Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Address
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Contact No.
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Terms At
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Documents
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody v-if="merchants.length>0">
                            <tr class="bg-white border-b" v-for="item in pendings">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap white:text-dark">
                                    {{ item.business_name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ item.bus_address }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.bus_contact_name }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.email }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.bus_contact_no }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.terms_agreed_at }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="w-20 truncate">{{ item.documents }}</div>
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.status }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    <button @click="review(item)" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-700 text-gray-900 hover:text-gray-200 font-semibold rounded-lg">Review</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mb-8">                
                    <h1 class="flex justify-start py-2 font-semibold text-lg">Partners Record</h1>
                    <table class="w-full text-sm text-left text-gray-700 border-gray-600 rounded-lg ">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Business Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Address
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Contact No.
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Terms At
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Documents
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody v-if="merchants.length>0">
                            <tr class="bg-white border-b" v-for="item in merchants">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap white:text-dark">
                                    {{ item.business_name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ item.bus_address }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.bus_contact_name }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.email }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.bus_contact_no }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.terms_agreed_at }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    <div class="w-20 truncate">{{ item.documents }}</div>
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.status }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    <button @click="review(item, 'View Documents')" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-700 text-gray-900 hover:text-gray-200 font-semibold rounded-lg">View Docs</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <div class="grid grid-cols-2 gap-8">
        
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
                        <div class="flex mb-4" v-for="f in docFiles">
                            <a :href="f.url" download class="px-2 py-1 bg-gray-300 rounded-full">{{ f.filename }}</a>
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <div v-for="img in docImages" class="">
                                <a :href="img.url" download target="_blank">
                                    <img :src="img.url" :alt="img.name" class="rounded-lg object-cover h-48 w-76" />
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="mb-1 p-8 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <div class="mb-4">
                                    <label for="status" class="mb-3 block text-base font-medium text-[#07074D]">Merchant Application Status</label>
                                    <select id="status" name="status" v-model="selectedItem.status" class="bg-gray-50 border border-[#e0e0e0] text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 white:bg-gray-700 dark:border-[#e0e0e0] dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option disabled value="">Please select one</option>
                                        <option value="-1">For Review</option>
                                        <option value="0">Disabled</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Suspended</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="remakrs" class="mb-3 block text-base font-medium text-[#07074D]">Remarks</label>
                                    <textarea id="remarks" maxlength="2000" v-model="selectedItem.status_remarks" required rows="3" class="w-[400px] block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-600 dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Briefly explain what happened."></textarea>
                                </div>
                            </div>
                            <div>
                                <p class="text-md">
                                    Listings and products of unapproved applications will be temporary unavailable.
                                </p>
                            </div>
                        </div>
                        
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
                            Update
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