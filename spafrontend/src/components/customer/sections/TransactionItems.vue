<script setup>
import { toRef,ref  } from 'vue'
import axios from "axios";
import WriteReviewForm from './WriteReviewForm.vue';
import WriteReportForm from './WriteReportForm.vue';
import SeeReport from './SeeReport.vue';

const props = defineProps({
    collection: { type: Array, default: [] },
    title: { type: String, default: 'Booking Details'},
    subtitle: { type: String, default: ''},
    view: { type: String, default: 'TABLE' }
})

const formatFigure = (val) => {
    if (val==undefined) return "₱0.00";
    return "₱" + parseFloat(val).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

toRef(() => props.view);
toRef(() => props.collection);

const reviewForm = ref({});
const reportForm = ref({});
const showModal = ref(false);
const modalTitle = ref('Write Review');
const modalContentId = ref(0);
const errors = ref([]);
const toggleViewReview = ref(false);

const hasReview = (item) => {
    const statusArr = Array('Completed');
    const exist = statusArr.find( (status) => {
        return status == item.booking_status;
    });
    // console.log("hasReview: ", item, " Exist: ", exist);
    if (item.product_review_id === null && exist) return false;
    return true;
}

const writeReview = (item) => {
    console.log("Write Review: ", item);
    reviewForm.value = {
        book_id: item.books_id,
        user_id: item.user_id,
        product_id: item.product_id,
        rating: 0,
        photos: null,
        review: null,
    }
    modalContentId.value = 0;
    showModal.value = !showModal.value;
    
}

const reportThis = (item) => {
    modalTitle.value = "What happened?";
    console.log("Report This: ", item);
    reportForm.value = {
        book_id: item.books_id,
        user_id: item.user_id,
        product_id: item.product_id,
        settled: 0,
        photos: null,
        remarks: null,
        related_to: null,
        other: null
    }
    modalContentId.value = 1;
    showModal.value = !showModal.value;
    
}

const seeReport = (item) => {
    console.log("See Report This: ", item);
    modalTitle.value = "What happened?";
    reportForm.value = {
        id: item.product_report_id,
        book_id: item.books_id,
        user_id: item.user_id,
        product_id: item.product_id,
        settled: item.product_report_settled,
        photos: item.product_report_photos,
        remarks: item.product_report_remarks,
        related_to: item.product_report_related_to
    }
    modalContentId.value = 2;
    toggleModal();
}

const toggleModal = ()=>{
    showModal.value = !showModal.value;
}

const submitReview = async (data) => {
    
    const el = document.getElementById('btnWriteReview');
    // console.log("Parent submitReview: ", data, " BTN:", el.textContent );
    if (el.textContent==='Saving...') return false;

    errors.value =[];
    el.textContent="Saving...";
    const config = {
        headers: {
            'Content-Type': 'multipart/form-data',
        }
    };

    // emit('ratenow', { inputData: data, inputFiles: props.files}); 
    let action_url = '/api/customer/product/review/' + data.book_id + '/' + data.product_id;
    const formData = new FormData();

    var temp = document.createElement("div");
    temp.innerHTML = data.review;
    data.review = temp.textContent || temp.innerText;
    
    if (data.photos !== null) {
        for( var i = 0; i < data.photos.length; i++ ){
            let file = data.photos[i];
            formData.append('files[' + i + ']', file);
        }
    }
    
    if (data.rating!==0) formData.append('rating', data.rating);
    formData.append('review', data.review);
    formData.append('book_id', data.book_id);
    formData.append('product_id', data.product_id);

    try {
        const response = await axios.post(action_url, formData, config);        
        const record = response.data.data;
        // console.log("Review - Response: ", record);
        let collectionItem = props.collection.find( ({ books_id }) =>parseInt(books_id) == parseInt(record.book_id) );
        // console.log("Collection Item: ", collectionItem);
        if (collectionItem !== undefined) {
            collectionItem.product_review_book_id = record.book_id;
            collectionItem.product_review_id = record.id;
            collectionItem.product_review_photos = record.photos;
            collectionItem.product_review_product_id = record.product_id;
            collectionItem.product_review_rating = record.rating;
            collectionItem.product_review_review = record.review;
        }

        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        toggleModal();
    } catch (error) {
        errors.value = error.response.data.errors;
        console.log("Errors: ", errors.value);
        // console.log("Errors: ", errors);
    }
    el.textContent="Save";
}

const submitReport = async (data) => {
    console.log("Parent submitReport: ", data);
    const el = document.getElementById('btnWriteReport');
    if (el.textContent==='Submitting...') return false;

    errors.value =[];
    el.textContent="Submitting...";
    const config = {
        headers: {
            'Content-Type': 'multipart/form-data',
        }
    };

    var temp = document.createElement("div");
    temp.innerHTML = data.remarks;
    data.remarks = temp.textContent || temp.innerText;

    const action_url = '/api/customer/book/report/' + data.book_id;
    const formData = new FormData();

    if (data.photos !== null) {
        for( var i = 0; i < data.photos.length; i++ ){
            let file = data.photos[i];
            formData.append('files[' + i + ']', file);
        }
    }

    if (data.related_to === 'Other') data.related_to += ': ' + data.other;
    
    formData.append('books_id', data.book_id);
    formData.append('product_id', data.product_id);
    formData.append('remarks', data.remarks);
    formData.append('related_to', data.related_to);
    
    try {
        const response = await axios.post(action_url, formData, config);        
        const record = response.data.data;
        // console.log("Report - Response: ", record);
        let collectionItem = props.collection.find( ({ books_id }) =>parseInt(books_id) == parseInt(record.book_id) );
        // console.log("Collection Item: ", collectionItem);
        if (collectionItem !== undefined) {
            collectionItem.product_review_book_id = record.book_id;
            collectionItem.product_review_id = record.id;
            collectionItem.product_review_photos = record.photos;
            collectionItem.product_review_product_id = record.product_id;
            collectionItem.product_review_rating = record.rating;
            collectionItem.product_review_review = record.review;
        }

        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        toggleModal();
    } catch (error) {
        errors.value = error.response.data.errors;
        console.log("Errors: ", errors.value);
        // console.log("Errors: ", errors);
    }
    el.textContent="Submit";
}


const reformatUploads = (photos) => {
    const arr_photos = photos.split(',');
    if (arr_photos.length==0) return [];
    let fu = [];
    arr_photos.forEach( (item, index)=> {
        let img_name = item.replace(/^\s+|\s+$/gm,'');
        fu.push({
            filename: img_name,
            url: 'http://localhost:8000/uploads/reviews/' + img_name
        })
    });

    return fu;
}
</script>
<template>
    <div>
        <div class="mb-4">
            <h1 class="font-semibold text-md">{{ title }}</h1>
            <p>{{ subtitle }}</p>
            
        </div>
        <div class="flex font-semibold text-md">
            View:  
            <button @click="()=> { view='TABLE' }" class="mx-3 hover:bg-gray-300 p-1 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                </svg>
            </button>
            <button @click="()=> { view='LARGE' }" class="hover:bg-gray-300 p-1 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
            </button>
        </div>
        <div class="mb-4 p-4 inline-grid gap-4 grid-cols-4 w-full text-md bg-gray-10" v-if="view==='LARGE'">
            <div v-for="item in collection" class="relative">
                <div class="mb-4">
                    <img :src="item.product_photo" alt="No image" class="rounded-lg"/>
                </div>
                <div class="mb-4">
                    <h2 class="text-sm font-semibold mb-2">Paid Amount: {{ formatFigure(item.total) }}</h2>
                    <h2 class="text-sm font-semibold">{{ item.product_name }}</h2>
                    <p class="text-sm">{{ item.product_address }}</p>
                </div>
                <div class="mb-4 text-xs">
                    <span class="px-2 py-1 bg-cyan-300 rounded-full">{{ item.from }}</span> to 
                    <span class="px-2 py-1 bg-cyan-300 rounded-full">{{ item.to }}</span>
                </div>
                <div class="mb-4 text-sm">
                    Booking Ref #: {{ item.booking_reference }}
                </div>
                <h4 class="absolute top-4 right-0 p-3 bg-gray-100/50 font-semibold">
                    {{ item.booking_status }}
                </h4>
                <div class="absolute top-4 left-0 p-3 bg-red-400/70 font-semibold text-white" v-if="item.product_report_id!=null">
                    <a @click="seeReport(item)" class="cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </a>
                </div>
                <div class="mb-4">
                    <div class="flex font-semibold text-sm">
                        <div class="flex justify-start w-[50%]">
                            <a @click="toggleViewReview = !toggleViewReview" class="cursor-pointer underline text-cyan-700 hover:no-underline hover:text-cyan-900">Rating</a>
                        </div>
                        <div class="flex justify-end w-[50%] items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-2">{{ item.product_review_rating }}/5</span>
                        </div>
                    </div>
                    <div class="mt-4 bg-gray-200 rounded-lg p-5" v-show="toggleViewReview">
                        <div>
                            {{ item.product_review_review }}
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 w-full">                    
                            <template v-if="item.product_review_photos!=null" v-for="img in reformatUploads(item.product_review_photos)">
                                <div class="flex max-h-22">
                                    <img :src="img.url" :alt="img.name" class="rounded-md object-cover" />
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="inline-block min-w-full py-2" v-if="view==='TABLE'">
            <div class="overflow-x-auto">
                <table class="table-fixed w-full text-left text-sm font-light">
                    <thead class="border-b font-medium dark:border-neutral-500 bg-gray-300">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-[25%]">Title</th>
                            <th scope="col" class="px-6 py-4 w-[7%]">From</th>
                            <th scope="col" class="px-6 py-4 w-[7%]">To</th>
                            <th scope="col" class="px-6 py-4 w-[3%]">Days</th>
                            <th scope="col" class="px-6 py-4">Total</th>
                            <th scope="col" class="px-6 py-4">Booking Ref #</th>
                            <th scope="col" class="px-6 py-4">Booking Status</th>
                            <th scope="col" class="px-6 py-4">Payment Status</th>
                            <th scope="col" class="px-6 py-4">Date</th>
                            <th scope="col" class="px-6 py-4">Review</th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b dark:border-neutral-500" v-for="item in collection">
                            <td class="whitespace-nowrap px-6 py-4 truncate">
                                <template v-if="item.booking_status == 'Pending'">
                                    <router-link :to="{ name:'Booking', params: { id: item.books_id } }" target="_blank" class="underline hover:no-underline text-blue-600">{{ item.product_name }}</router-link>
                                </template>
                                <template v-else>
                                    {{ item.product_name }}
                                </template>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">{{ item.from }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ item.to }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ item.days }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ formatFigure(item.total) }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ item.booking_reference }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ item.booking_status }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ item.payment_status }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ item.created_at }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <template v-if="!hasReview(item)">
                                    <button @click="writeReview(item)" class="font-semibold text-xs px-2 py-2 bg-yellow-300 rounded-lg border-b-2 border-yellow-600">Write Review</button>
                                </template>
                                <template v-else>
                                    <div class="flex px-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="ml-2">{{ item.product_review_rating }}/5</span>
                                    </div>
                                </template>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex justify-center items-center">
                                    <a @click="reportThis(item)" class="underline hover:no-underline text-red-600 font-semibold cursor-pointer" v-if="item.product_report_id==null">Report</a>
                                    <a @click="seeReport(item)" class="underline hover:no-underline text-red-600 font-semibold cursor-pointer" v-if="item.product_report_id!=null">See Report</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                    <template v-if="modalContentId==0">
                        <WriteReviewForm :form="reviewForm" v-on:submitreview="submitReview" :errors="errors"></WriteReviewForm>
                    </template>
                    <template v-if="modalContentId==1">
                        <WriteReportForm :form="reportForm" v-on:submitreport="submitReport" :errors="errors"></WriteReportForm>
                    </template>
                    <template v-if="modalContentId==2">
                        <SeeReport :form="reportForm"></SeeReport>
                    </template>
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
    </div>
</template>