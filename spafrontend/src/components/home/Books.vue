<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from "axios";
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import HouseRules from './sections/HouseRules.vue';

const router = useRouter()
const authStore = useAuthStore();


const id = ref(0);
const slug = ref('');
const subTotals = ref([]);
const totals = ref([]);
const booking = ref({});
const dateValue = ref([]);
const primaryPhoto = ref('http://localhost:8000/uploads/Image_not_available.png');
const paymentActionTitle = ref('Pay Now via G-Cash');
const paymentCheckout = ref({});

onMounted(() => {
    // console.log("params", router.currentRoute.value.params);
    // console.log("query", router.currentRoute.value.query);
    id.value = router.currentRoute.value.params.id
    slug.value = router.currentRoute.value.query.slug;

    localStorage.setItem('reserveurl', router.currentRoute.value.fullPath);
    console.log("reserveurl", localStorage.getItem('reserveurl'));
    
    fetchData().catch(error => {
        error.message; 
        console.log("fetchData Error: ", error.message);
    }); 
    
    authStore.getUser('Product Details');
});

const fetchData = async () => {
    try {
        const response = await axios.get('/api/booking/confirm-and-pay/'+id.value+'/'+slug.value);
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        booking.value = await response.data.data;
        if (booking.value && booking.value !=null) {
            let selected_dates = [];
            selected_dates.push(booking.value.from);
            selected_dates.push(booking.value.to);
            dateValueViews(selected_dates);
            primaryPhotoView(booking.value.product_photo);
            priceDetails();
        }
        // compilePhotos();
        // regroupAttribute(product.value.product_attributes);
        // form.value = await response.data.data;
        // parent.value = form.value.listing_category;
        console.log("booking: ",  booking.value);
        // console.log("Listing: ",  product.value.listing_category);
        // regroupAttribute(form.value.product_attributes);
    } catch (error) {
        console.log("fetchData" ,error);
    }
}

const dateValueViews = (dates) => {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    let date_from = new Date(dates[0]);
    let date_to = new Date(dates[1]);

    dateValue.value.push(date_from.toLocaleDateString("en-US", options));
    dateValue.value.push(date_to.toLocaleDateString("en-US", options));
}

const primaryPhotoView = (img) => {
    
    if (img && img != null && img.indexOf('.')>0 && img.indexOf('http://')>-1) {
        primaryPhoto.value = img;
    } else if (img && img != null && img.indexOf('.')>0 && img.indexOf('http://')<0) {
        primaryPhoto.value = 'http://localhost:8000/uploads/'+img;
    }

    // console.log("Primary Photo",img.indexOf('http://'));
}

const priceDetails = () => {
    let subtotal =  parseFloat(booking.value.rate) * parseInt(booking.value.days); 
    const adminFee_str = parseFloat(booking.value.service_fee).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    const subtotal_str = parseFloat(subtotal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    subTotals.value.push(
        {
            label: '₱'+ (booking.value.rate).toLocaleString() +'x'+ booking.value.days +' Night'+ (booking.value.days==1?'':'s'),
            value: subtotal_str,
        },
        {
            label: 'PaReserve Service Fee',
            value: adminFee_str,
        }
    );

    const new_total_str = parseFloat(booking.value.total).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    totals.value = {
        label: "Total",
        value: new_total_str,
    }
}

const allowToBook = computed(() => {
    // return !(authStore.user==null || authStore.user!=null && authStore.user.user_type.toUpperCase()!='CUSTOMER');
    return (booking.value !==null && booking.value.booking_status && (booking.value.booking_status.toUpperCase() === 'PENDING' || booking.value.booking_status.toUpperCase() === 'CANCELLED'));
});

const pay = async () => {

    if (paymentActionTitle.value.toUpperCase()==='Pay Now via G-Cash'.toUpperCase()) {
        paymentActionTitle.value = 'Processing...';
    } else {
        return;
    }

    let action_url = '/api/payment/submit/' + booking.value.id;
    const reserve_url = localStorage.getItem('reserveurl');
    const formData = new FormData();

    formData.append('books_id', booking.value.id);
    formData.append('product_id', booking.value.product_id);
    formData.append('reserve_url', reserve_url);
    localStorage.removeItem('reserveurl');

    const response = await axios.post(action_url, formData);      
      
    if (!response) {
        const message = 'An error has occured: ${response.status}';
        paymentActionTitle.value = 'Pay Now via G-Cash.';
        throw new Error(message);
    }

    paymentCheckout.value = await response.data.data;
    console.log("Pay Now", paymentCheckout.value);
    if (paymentCheckout.value && paymentCheckout.value.url !== undefined){
        window.location.href = paymentCheckout.value.url;
    }

}

</script>

<template>
    <div class="pb-12 pt-10 md:pt-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <!-- Product Title -->
            <div class="mb-4 items-center justify-center">
                <h1 class="font-semibold text-3xl">Confirm and Pay</h1>
                <p></p>
            </div>
            
            <div class="grid grid-flow-col grid-rows-3 sm:grid-rows-1 gap-4 grid-flow-row auto-rows-max">
                <div class="mt-4 order-last sm:order-first">
                    <h2 class="font-semibold text-xl mb-4">Your booking details</h2>
                    <div class="mb-4">
                        <div class="pt-2 pb-1 inline-grid gap-2 grid-cols-2 w-full text-md">
                            <div class="text-start font-semibold">Check-in</div>
                            <div class="text-start font-semibold">Check-out</div>
                        </div>

                        <div class="inline-grid gap-2 grid-cols-2 w-full text-md">
                            <div class="text-start">
                                <div>{{ dateValue[0] }}</div>
                                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-md font-normal text-gray-700 mr-2 mb-2 text-xs">From 1:00PM</span>
                            </div>
                            <div class="text-start">
                                <div>{{ dateValue[1] }}</div>
                                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-md font-normal text-gray-700 mr-2 mb-2 text-xs">To 11:00AM</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="pt-2 pb-1 inline-grid gap-2 grid-cols-2 w-full text-md">
                            <div class="text-start font-semibold">Total length of stay</div>
                            <div class="text-start font-semibold">No. of Guest</div>
                        </div>
                        <div class="inline-grid gap-2 grid-cols-2 w-full text-md">
                            <div class="text-start font-normal">{{ booking.days }} night(s)</div>
                            <div class="text-start font-normal">{{ booking.number_of_guest }}</div>
                        </div>
                    </div>

                    <div class="mb-4 mt-10 rounded-lg bg-gray-200 p-6">
                        <HouseRules />
                    </div>
                </div>
                <div class="mt-4">
                    <div class="mb-2">
                        <div class="inline-grid gap-2 grid-cols-2 w-full text-md">
                            <div class="h-48 mb-4">
                                <img :src="primaryPhoto" :alt="booking.product_name" class="object-cover h-48 md:object-fit rounded-lg"  />
                            </div>
                            <div class="text-start font-normal">
                                <span class="inline-block bg-gray-300 rounded-full px-3 py-1 text-md font-normal text-gray-700 mr-2 mb-2 text-xs">{{ booking.product_category }}</span>
                                <h3 class="font-semibold text-md mb-4">
                                    {{  booking.product_name }}
                                </h3>
                                <p>{{ booking.product_address }}</p>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                    <div class="mb-4 p-4">
                        <h2 class="font-semibold text-xl">Price details</h2>
                        <div class="border-t border-gray-300 mt-6">
                            <div class="py-4 inline-grid gap-2 grid-cols-2 w-full text-md">
                                <template v-for="item in subTotals">
                                    <div class="text-start font-semibold">{{ item?.label }}</div>
                                    <div class="text-end">₱{{ item?.value }}</div>
                                </template>
                            </div>

                            <div class="py-4 inline-grid gap-2 grid-cols-2 w-full text-md border-t border-gray-300" v-if="totals?.value !== undefined">
                                <div class="text-start font-bold">{{ totals?.label }}</div>
                                <div class="text-end">₱{{ totals?.value }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <form method="POST" @submit.prevent="pay()">
                            <input type="hidden" id="booking_id" v-model="booking.id" />
                            <div v-if="allowToBook">
                                <p class="text-sm mb-4">Next step will redirect you to secure payment page. lorem ipsum blah ...</p>
                                <button id="submit_payment" type="submit" class="flex w-full justify-center rounded-md bg-[#1A7D7A] px-3 py-4 text-lg font-semibold leading-6 text-white shadow-sm hover:bg-[#0F9C98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ paymentActionTitle }}</button>
                            </div>
                            <div v-else class="text-center">
                                <p>No action required.</p>
                            </div>
                        </form>
                    </div>

                    <div class="mb-4">
                        <div class="w-full mb-2 select-none border-t-4 border-gray-400 bg-gray-100 p-4 font-medium hover:border-gray-500">
                            <h4 class="font-semibold">PaReserve cancellation policies</h4>
                            <p class="text-sm">when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>