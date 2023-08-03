<script setup>
import { ref, toRef  } from 'vue'
import axios from "axios";

const props = defineProps({
    collection: { type: Array, default: [] },
    title: { type: String, default: 'Booking Details'},
    subtitle: { type: String, default: 'List'},
    view: { type: String, default: 'TABLE' }
})

const formatFigure = (val) => {
    if (val==undefined) return "₱0.00";
    return "₱" + parseFloat(val).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

const isCheckout = (item) => {
    if (item==undefined) return false;
    
    const checkout_date = new Date(item.to);
    const todays_date = new Date();

    // console.log('Today Date:', todays_date, ' Checkout:', checkout_date);
    // console.log("Condition: ", todays_date >= checkout_date);
    if (item.booking_status === 'Booked' && todays_date >= checkout_date) {
        return true;
    }
    return false;
}

const emit = defineEmits(['checkout']);

const checkout = (item) => {
    console.log('Emit checkout: ');
    emit('checkout', item );
}

toRef(() => props.view);
toRef(() => props.collection);

const showModal = ref(false);
const modalTitle = ref('Booking Cancellation Request');
const cancelRequestData = ref({});


const toggleModal = ()=>{
    showModal.value = !showModal.value;
}

const viewCancel = (item) => {
    console.log('viewCancel: ',  item);
    cancelRequestData.value = item;
    toggleModal();
}

const updateCancelRequest = async () => {
    console.log('Update: ',  cancelRequestData.value);
    const el = document.getElementById('btnUpdateCancelRequest');
    // console.log("Parent submitReview: ", data, " BTN:", el.textContent );
    if (el.textContent==='Updating...') return false;
    el.textContent='Updating...';

    console.log("AMOUNTL ", cancelRequestData.value.cancellation_request_refunded_amount);
    if (cancelRequestData.value.cancellation_request_refunded_amount == 0) 
        cancelRequestData.value.cancellation_request_refunded_amount = '0.00';
    if (isNaN(parseFloat(cancelRequestData.value.cancellation_request_refunded_amount))) {
        cancelRequestData.value.cancellation_request_refunded_amount = '0.00';
        el.textContent='Update';
        return;
    }

    // /partner/request/cancel/
    const action_url = '/api/partner/request/cancel/' + cancelRequestData.value.cancellation_request_id;
    const formData = new FormData();

    let amnt = cancelRequestData.value.cancellation_request_refunded_amount;
    amnt = amnt.replace(/\,/g,'');

    formData.append('books_id', cancelRequestData.value.books_id);
    formData.append('request_status', cancelRequestData.value.cancellation_request_request_status);
    formData.append('refunded_amount', parseFloat(amnt));

    try {
        const response = await axios.post(action_url, formData);        
        const record = response.data.data;
        console.log("Update Cancel - Response: ", record);
        let collectionItem = props.collection.find( ({ books_id }) => parseInt(books_id) == parseInt(record.books_id) );
        console.log("Collection Item: ", collectionItem);
        if (collectionItem !== undefined) {
            collectionItem.cancellation_request_request_status = record.request_status;
            collectionItem.cancellation_request_refunded_amount = record.refunded_amount;
            if (record.request_status=='Approved') {
                collectionItem.booking_status = "Cancelled";
            } else {
                collectionItem.booking_status = "Booked";
            }
            collectionItem.cancellation_request_refunded_date = record.refunded_date;
        }

        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        toggleModal();
    } catch (error) {
        console.log("Errors: ", error);
        // console.log("Errors: ", errors);
    }

    el.textContent='Update';
};

const moneyFormat = (v) =>{
    try {
        if (isNaN(v)) throw "Not a Number" 
        const fv = parseFloat(v);
        return fv.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    } catch (error) {
        return "0.00"
    }
}

</script>

<template>
    <div class="mb-4">
        <h1 class="font-semibold text-md">{{ title }}</h1>
        <p>{{ subtitle }}</p>
    </div>

    <div class="mb-4">
        <div class="inline-block min-w-full py-2" v-if="view==='TABLE'">
            <div class="overflow-x-auto">
                <table class="table-fixed w-full text-left text-sm font-light">
                    <thead class="border-b font-medium dark:border-neutral-500 bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-[25%]">Title</th>
                            <th scope="col" class="px-6 py-4">From</th>
                            <th scope="col" class="px-6 py-4">To</th>
                            <th scope="col" class="px-6 py-4 w-[3%]">Days</th>
                            <th scope="col" class="px-6 py-4">Total</th>
                            <th scope="col" class="px-6 py-4">Booking Ref #</th>
                            <th scope="col" class="px-6 py-4">Booking Status</th>
                            <th scope="col" class="px-6 py-4">Payment Status</th>
                            <th scope="col" class="px-6 py-4">Guest</th>
                            <th scope="col" class="px-6 py-4">Date</th>
                            <th scope="col" class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b dark:border-neutral-500" v-for="item in collection">
                            <td class="whitespace-nowrap px-6 py-4 truncate">{{ item.product_name }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ item.from }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ item.to }}</td>
                            <td class="whitespace-nowrap px-6 py-4 truncate">{{ item.days }}</td>
                            <td class="whitespace-nowrap px-6 py-4 truncate">{{ formatFigure(item.total) }}</td>
                            <td class="whitespace-nowrap px-6 py-4 truncate">{{ item.booking_reference }}</td>
                            <td class="whitespace-nowrap px-6 py-4 truncate">{{ item.booking_status }}</td>
                            <td class="whitespace-nowrap px-6 py-4 truncate">{{ item.payment_status }}</td>
                            <td class="whitespace-nowrap px-6 py-4 truncate">{{ item.name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 truncate">{{ item.created_at }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <template v-if="item.cancellation_request_id !== null">
                                    <button @click="viewCancel(item)" class="font-normal text-xs px-2 py-2 bg-red-500 hover:bg-red-600 hover:text-white rounded-lg border-b-2 border-red-700 mr-1">View Cancel Request</button>    
                                </template>
                                <button @click="checkout(item)" class="font-normal text-xs px-2 py-2 bg-green-300 hover:bg-green-600 hover:text-white rounded-lg border-b-2 border-green-600" v-if="isCheckout(item)">Check-out</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
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
                    <div class="mb-4">
                        <form @submit.prevent="updateCancelRequest()" method="POST" >
                            <h2 class="mb-4 text-base font-medium text-gray-800">Booking Ref #: <strong>{{ cancelRequestData.booking_reference }}</strong></h2>
                            <div class="grid grid-cols-2 gap-8 mb-4">
                                <div>
                                    <p class="mb-4 text-base font-medium text-gray-800">Cancel Date Requested: <strong>{{ cancelRequestData.cancellation_request_created_at }}</strong></p>
                                    <p class="mb-4 text-base font-medium text-gray-800">Request Status: <strong>{{ cancelRequestData.cancellation_request_request_status }}</strong></p>
                                </div>
                                <div>
                                    <p class="mb-4 text-base font-medium text-gray-800">Refund Amount: <strong>{{ cancelRequestData.cancellation_request_refunded_amount }}</strong></p>
                                    <p class="mb-4 text-base font-medium text-gray-800">Refund Date: <strong>{{ cancelRequestData.cancellation_request_refunded_date }}</strong></p>
                                </div>
                                <div>
                                    <p class="mb-4 text-base font-medium text-gray-800">By: <strong>{{ cancelRequestData.name }}</strong></p>
                                </div>
                                <div>
                                    <p class="mb-4 text-base font-medium text-gray-800">Email: <strong>{{ cancelRequestData.email }}</strong></p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="p-6 rounded-lg bg-gray-200 mb-6">
                                    <p>{{ cancelRequestData.cancellation_request_remarks }}</p>
                                </div>
                            </div>

                            <div class="border-t border-gray-400 p-4">
                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div>
                                        <label for="status" class="mb-4 font-semibold flex">Request Status</label>
                                        <select id="status" name="status" v-model="cancelRequestData.cancellation_request_request_status" class="bg-gray-50 border border-[#e0e0e0] text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 white:bg-gray-700 dark:border-[#e0e0e0] dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option disabled value="">Please select one</option>
                                            <option value="Cancel Requested">Cancel Requested</option>
                                            <option value="For Review">For Review</option>
                                            <option value="Approved">Approved</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="status" class="mb-4 font-semibold flex">Refund Amount</label>
                                        <input type="text" id="refund_amount" name="refund_amount" class="justify-end bg-gray-50 border border-[#e0e0e0] text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 white:bg-gray-700 dark:border-[#e0e0e0] dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" v-model="cancelRequestData.cancellation_request_refunded_amount" />
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button id="btnUpdateCancelRequest" class="text-white bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                                        Update
                                    </button>
                                </div>
                            </div>

                            
                        </form>
                    </div>
                </div>
                <!--footer-->
                <div class="flex items-center justify-end p-6 border-t border-solid border-slate-200 rounded-b">
                    <!-- 
                    <button class="text-teal-500 bg-transparent border border-solid border-teal-500 hover:bg-teal-500 hover:text-white active:bg-teal-600 font-bold uppercase text-sm px-4 py-2 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button" v-on:click="toggleModal()">
                    Close
                    </button>
                    <button class="text-cyan-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button" v-on:click="toggleModal()">
                    Save Changes
                    </button> 
                    -->
                </div>
                </div>
            </div>
        </div>
        <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>
</template>