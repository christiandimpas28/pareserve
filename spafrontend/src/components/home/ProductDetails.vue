<script setup>
import axios from "axios";
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

import VueTailwindDatepicker from 'vue-tailwind-datepicker';
import ProductImages from './sections/ProductImages.vue';
import SimpleToast from '../SimpleToast.vue';

const router = useRouter()
const authStore = useAuthStore();

const id = ref(0);
const slug = ref('');
const product = ref({});
const toastMessage = ref('Oops!');
const imageCollection = ref([]);
const groupAttributes = ref([]);
const isFetching = ref(false);

const no_of_guest = ref(1);
const dateValue = ref([]);
const dateFormatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MMMM'
});

const hasBookingConflicts = ref(false);

const subTotals = ref([]);
const totals = ref([]);

onMounted(() => {
    console.log("params", router.currentRoute.value.params);
    id.value = router.currentRoute.value.params.id;
    slug.value = router.currentRoute.value.query.slug? router.currentRoute.value.query.slug:'null';
    if (localStorage.getItem('guestCheckoutAttempt') !== null) localStorage.removeItem('guestCheckoutAttempt');
    fetchData().catch(error => {
        error.message; 
        console.log("fetchData Error: ", error.message);
    }); 
    
    authStore.getUser('Product Details');
});

const allowCheckout = computed(() => {
    return !(authStore.user==null || authStore.user!=null && authStore.user.user_type.toUpperCase()!='CUSTOMER');
});

const showToast = (mode) => {
    let el = document.getElementById("toast-success");
    if (mode==2) el = document.getElementById("toast-warning");
    if (mode==3) el = document.getElementById("toast-danger");
    
    el.classList.remove("hidden"); 
    setTimeout(function () {
        el.classList.add("hidden");
    }, 5000);
}


watch(() => dateValue.value, (c, b) => { 
    console.log("before:", b);
    console.log("after:", c);
    console.log("Date Diff:", );
    let days = DateDiff.inDays(new Date(c[0]), new Date(c[1]));

    if (product.value && product.value.rate!== undefined) {
        const fee_rate = 0.03;
        subTotals.value.length=0;
        days = days==0? 1 : days;
        let subtotal =  parseFloat(product.value.rate) * parseInt(days); 
        let adminFee = subtotal * fee_rate;
        const adminFee_str = parseFloat(adminFee).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const subtotal_str = parseFloat(subtotal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        subTotals.value.push(
            {
                label: '₱'+ (product.value.rate).toLocaleString() +'x'+ days+' Night'+ (days==1?'':'s'),
                value: subtotal_str,
                raw: {
                    rate: product.value.rate,
                    qty: days,
                    total: subtotal,
                }
            },
            {
                label: 'PaReserve Service Fee',
                value: adminFee_str,
                raw: {
                    rate: fee_rate,
                    qty: 1,
                    total: adminFee,
                }
            }
        );

        let new_total = subTotals.value.reduce(function (prev, cur) {
            return parseFloat(prev) + parseFloat(String(cur.value).replace(/,/g,''));
        }, 0);
        const new_total_str = new_total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        totals.value = {
            label: "Total",
            value: new_total_str,
            raw: {
                total: new_total,
            }
        }

        preBooking().catch(error => {
            error.message; 
            console.log("Pre-Booking Check Error: ", error.message);
        }); 

        if (hasBookingConflicts.value) {
            toastMessage.value = "Oops! Selected dates are aleady booked.";
            showToast(3);
        }

    }
    console.log("Date Diff:", subTotals.value);

});

const DateDiff = {
    inDays: function(d1, d2) {
        var t2 = d2.getTime();
        var t1 = d1.getTime();

        return Math.floor((t2-t1)/(24*3600*1000));
    }
}


const fetchData = async () => {
    try {
        const response = await axios.get('/api/product/'+id.value+'/'+product.value,slug);
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        product.value = await response.data.data;
        compilePhotos();
        regroupAttribute(product.value.product_attributes);
        // form.value = await response.data.data;
        // parent.value = form.value.listing_category;
        console.log("Product: ",  product.value);
        // console.log("Listing: ",  product.value.listing_category);
        // regroupAttribute(form.value.product_attributes);
    } catch (error) {
        console.log("fetchData" ,error);
    }
}

const preBooking = async () => {
    // ?query='+val+'&start='+start+'&end='+end;
    const get_url = '/api/booking/pre-check/'+product.value.id+'?from='+ dateValue.value[0] + '&to='+dateValue.value[1];
    console.log("get_url", get_url);
    const response = await axios.get(get_url);
    if (!response) {
        const message = 'An error has occured: ${response.status}';
        throw new Error(message);
    }

    const rd = await response.data.data;
    hasBookingConflicts.value = rd.length>0? true: false;
    console.log("hasBookingConflicts", hasBookingConflicts.value , "Bookings:", rd);
}

const guestCheckoutAttempt = (routeName) =>{
    localStorage.removeItem('guestCheckoutAttempt');
    const data = {
        url: router.currentRoute.value.fullPath,
        dates: dateValue.value,
        no_of_guest: no_of_guest.value
    }

    localStorage.setItem('guestCheckoutAttempt', JSON.stringify(data));

    console.log("guestCheckoutAttempt: ", data, "LS: ", localStorage.getItem('guestCheckoutAttempt'));
    router.push({name:routeName});
}

const compilePhotos = () => {
    imageCollection.value = [0,1,3,4];
    let photos = '';
    if (product.value.photos != null && product.value.photos.trim().length>0){
        photos+=product.value.photos;
    }

    if (product.value.listing_category != null && product.value.listing_category.listing_photos != null && product.value.listing_category.listing_photos.trim().length>0){
        const deli = photos.trim().length>0? ',': '';
        photos+=deli+product.value.listing_category.listing_photos;
    }

    imageCollection.value  = photos.split(',');
    if (imageCollection.value.length>0) {
        imageCollection.value.forEach( (item, index) => {
            imageCollection.value[index] = 'http://localhost:8000/uploads/'+item;
        });
    }
}

const regroupAttribute = (attrs) => {
    
    // Get all value in a array of a given key
    const valueArray = attrs.map((attr)=>attr.name.toLowerCase());
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
            not_included: title==='NOT INCLUDED' || title==='NOT ALLOWED',
            data: group_src
        }
        groupAttributes.value.push(group);
    });
}

const submitForm = async () => {
    console.log('submitForm');
    if (product.value.id === undefined){
        toastMessage.value = "Oops! Something went wrong. Please refresh your page and try again.";
        showToast(3);
        return false;
    }

    if (dateValue.value.length<1 || subTotals.value.length===0) {
        toastMessage.value = "Please complete the booking details before you can proceed. e.g(dates and number of guest)";
        showToast(3);
        return false;
    }

    try {
        
        console.log('Submit Product ID: ',  id.value);
        console.log('Rate: ', product.value.rate, 'Selected Dates:', dateValue.value, "subtotals:", subTotals.value, "Totals:", totals.value);
        authStore.getToken();
        const formData = new FormData();
        let action_url = '/api/booking/submit/' + id.value;

        formData.append('product_id', product.value.id);
        formData.append('product_name', product.value.name);
        formData.append('product_photo', imageCollection.value.length>0? imageCollection.value[0]: '-');
        formData.append('product_category', product.value.listing_category.category);
        formData.append('product_address', product.value.listing_category.address);
        formData.append('qty', subTotals.value[0].raw.qty);
        formData.append('number_of_guest', no_of_guest.value);
        formData.append('from', dateValue.value[0]);
        formData.append('to', dateValue.value[1]);
        formData.append('days', subTotals.value[0].raw.qty);
        formData.append('service_fee_rate', subTotals.value[1].raw.rate);
        formData.append('service_fee', subTotals.value[1].raw.total);
        formData.append('rate', product.value.rate);
        formData.append('discount', 0);
        formData.append('total', totals.value.raw.total);

        const response = await axios.post(action_url, formData);        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        const booking = response.data.data;
        console.log("Booking", booking);
        let property_details = btoa(booking.product_id+"&"+booking.product_name+"&"+booking.rate);
        router.push({
            name: 'Booking', 
            params: { id: booking.id }, 
            query: {
                noofguest: booking.number_of_guest,
                from: booking.from,
                to: booking.to,
                days: booking.days,
                property: property_details,
                propertyid: booking.product_id,
                slug: btoa(product.value.slug)
            }
        });
        
    } catch (error) {
        console.log("error", error);
        toastMessage.value = error.message;
        showToast(3);
    }
}

</script>

<template>
<div class="pb-12 pt-10 md:pt-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <!-- Product Title -->
        <div class="mb-4 items-center justify-center">
            <h1 class="font-semibold text-3xl">{{ product.name }}</h1>
            <p>{{ product.listing_category?.address }}</p>
        </div>

        <!-- Gallery -->
        <div>
            <ProductImages :collection="imageCollection" />
        </div>

        <!-- Product Sub Title -->
        <div class="mb-4 items-center justify-center border-b-2 border-gray-400">
            <p class="font-semibold text-sm py-2">{{ product.listing_category?.name }}</p>
            <div class="pt-2 mb-4">
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-md font-semibold text-gray-700 mr-2 mb-2">{{ product.listing_category?.category }}</span>
                <span class="inline-block bg-[#F7BE38] rounded-full px-3 py-1 text-md font-semibold text-gray-700 mr-2 mb-2">Up to {{ product?.max_guest }} max guest allowed</span>
            </div>
            
            <div class="mb-4 border-t-1 border-gray-400">
                <h1 class="font-semibold text-2xl my-2">Description</h1>
                <p class="font-normal text-md">{{ product?.description }}</p>
            </div>
            
        </div>
        
        <!-- Product Details -->
        
        <div class="flex md:flex-row flex-wrap flex-col-reverse">
            <div class="w-full md:w-1/2 mt-4">
                <div class="bg-gray-200 px-10 py-4 text-sm rounded-lg">
                    <div class="mb-4 ">
                        <h1 class="font-semibold text-2xl mb-4">What this place offers</h1>
                    </div>

                    <div class="mb-4 border-t-2" v-for="item in groupAttributes">
                        <h1 class="font-bold text-xs my-2">{{ item.title }} </h1>
                        <div class="p-2 inline-grid gap-2 grid-cols-2 w-full">
                            
                            <div class="flex item-center " v-for="v in item.data">
                                <template v-if="!item.not_included">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="mr-2 h-4 w-4 text-primary dark:text-primary-400 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                </template>
                                <template v-else>
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="mr-2 h-4 w-4 text-primary dark:text-primary-400 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </template>
                                {{ v.value  }}
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="w-full md:w-1/2 mt-4 lg:pl-8">
                <div class="rounded-lg border-solid border border-gray-300">
                    <div class="mb-4 p-8">
                        <h1 class="font-bold text-md mb-4">₱{{ product.rate }} per Night</h1>
                        <div class="mb-4">
                            <form method="POST" @submit.prevent="submitForm()">
                                <div class="mb-4">
                                    <vue-tailwind-datepicker v-model="dateValue" :formatter="dateFormatter" />
                                </div>
                                <div class="w-full max-w-sm mb-4">
                                    <label for="max_guest" class="mb-3 block text-sm font-medium text-[#07074D]">Guest</label>
                                    <input type="number" id="max_guest" name="max_guest" min="1" v-model="no_of_guest" :max="product?.max_guest" class="w-full rounded-md border border-gray-500 bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" placeholder="Number of Guest">
                                </div>
                                <div v-if="allowCheckout">
                                    <button type="submit" class="flex w-full justify-center rounded-md bg-[#1A7D7A] px-3 py-4 text-lg font-semibold leading-6 text-white shadow-sm hover:bg-[#0F9C98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Pa Reserve</button>
                                </div>
                                <div v-else class="text-center">
                                    <p>Not a customer yet?</p>
                                    <a @click.prevent="guestCheckoutAttempt('Register')" class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline cursor-pointer">Sign up Now</a> or <a @click.prevent="guestCheckoutAttempt('Login')" class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline cursor-pointer">Log-in</a> to book.
                                </div>
                                
                            </form>
                        </div>

                        <div class="border-t border-gray-300">
                            <div class="p-2 inline-grid gap-2 grid-cols-2 w-full text-md">
                                <template v-for="item in subTotals">
                                    <div class="text-start font-semibold">{{ item?.label }}</div>
                                    <div class="text-end">₱{{ item?.value }}</div>
                                </template>
                            </div>

                            <div class="p-2 inline-grid gap-2 grid-cols-2 w-full text-md border-t border-gray-300" v-if="totals?.value !== undefined">
                                <div class="text-start font-bold">{{ totals?.label }}</div>
                                <div class="text-end">₱{{ totals?.value }}</div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<SimpleToast :toast-message="toastMessage" />
</template>