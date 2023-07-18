<script setup>
import axios from "axios";
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

import VueTailwindDatepicker from 'vue-tailwind-datepicker';
import ProductImages from './sections/ProductImages.vue';
import SimpleToast from '../SimpleToast.vue';
import Ratings from "./sections/Ratings.vue";

const router = useRouter()
const authStore = useAuthStore();


const fee_rate = 0.03;

const id = ref(0);
const slug = ref('');
const product = ref({});
const toastMessage = ref('Oops!');
const imageCollection = ref([]);
const groupAttributes = ref([]);
const highlights = ref([]);
const isFetching = ref(false);
const addOnsView = ref(false);
const max_extra = ref(0);
const pax_remain = ref(-1);

const no_of_guest = ref(1);
const addOnsRequest = ref({
    bed:0,
    breakfast: 0,
    adult: 0,
    children_age: 0
});

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

const deleteSubtotalItem = (key) => {
    const r = removeArrByKey(key);
    console.log("Remove Response: ", r);
    removeArrByName(subTotals.value, 'servicefee');
    recomputeFees(subTotals.value);
}

const recomputeFees = (arr) => {

    subTotals.value.sort(function(a, b) {
        return parseFloat(a.index) - parseFloat(b.index);
    });

    let subtotal = 0;
    arr.forEach( (item, index) => {
        console.log("name: ", item.name, " total: ", item.raw.total );
        if (item.name !== 'servicefee') {
            subtotal+=item.raw.total;
        }
    });

    let adminFee = subtotal * fee_rate;
    const adminFee_str = parseFloat(adminFee).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

    subTotals.value.push({
        index: 3,
        name: 'servicefee',
        label: 'Service Fee',
        value: adminFee_str,
        key: getSubtotalsKey(),
        raw: {
            rate: fee_rate,
            qty: 1,
            total: adminFee,
        }
    });

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
}

const getSubtotalsKey = () => {
    let maxKey = Math.max.apply(Math, subTotals.value.map(function (item) {
        return item.key;
    }));
    return maxKey+=1;
}

const extraPaxLimit = () => {
    const max_limit = parseInt(product.value.max_guest) - parseInt(product.value.min_guest);
    const adult = subTotals.value.find( ({ name }) => name === 'adult' );
    const children = subTotals.value.filter((item) => { return item.name==="children"} ); 
    
    let adult_count = 0
    let child_count = 0;
    
    let pax_count = 0;

    if (adult!=undefined){
        pax_count+=adult.raw.qty;
        adult_count = adult.raw.qty;
    }

    if (children!=undefined && Array.isArray(children)){
        pax_count+=children.length;
        child_count = children.length;
    }

    return { count: pax_count, limit: max_limit, adult: adult_count, child:child_count }
}

const addPax = () => {
    if (addOnsRequest.value.children_age == 0) return;
    
    const extra_pax_limit = extraPaxLimit();
    let pax_count = extra_pax_limit.count;
    let max_limit = extra_pax_limit.limit;

    // console.log("CHILD extra_pax_limit: ", extra_pax_limit);
    if (extra_pax_limit.child>0) pax_count +=1;
    // return;

    if (pax_count <= max_limit) {
        
        let extra_pax_rate = product.value.extra_pax_rate;
        if (product.value.free_below_age>0 && addOnsRequest.value.children_age < product.value.free_below_age) extra_pax_rate = 0;
        let subtotal =  parseFloat(extra_pax_rate) * 1; 
        const subtotal_str = parseFloat(subtotal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        removeArrByName(subTotals.value, 'servicefee');
        subTotals.value.push({
            index: 2,
            name: 'children',
            label: 'Child - (' + addOnsRequest.value.children_age + 'yr'+ (addOnsRequest.value.children_age>1?'s':'') +' old)',
            value: subtotal_str,
            key: getSubtotalsKey(),
            raw: {
                rate: product.value.extra_pax_rate,
                qty: 1,
                total: subtotal,
            },
            parameters: {
                free_below_age: product.value.free_below_age,
                age: addOnsRequest.value.children_age
            }
        });

        recomputeFees(subTotals.value);

    }

}

const removeArrByName = (arr, name) => {
   const requiredIndex = arr.findIndex(el => {
      return el.name === name;
   });
   if(requiredIndex === -1){
      return false;
   };
   return !!arr.splice(requiredIndex, 1);
};

const removeArrByKey = (key) => {
   const requiredIndex = subTotals.value.findIndex(el => {
      return el.key === key;
   });
   if(requiredIndex === -1){
      return false;
   };
   return !!subTotals.value.splice(requiredIndex, 1);
};

watch(()=> subTotals.value, (c,b)=>{
    console.log("Change detected.");
});

watch(
    () => addOnsRequest.value.bed, (c, b) => { 
        const max_limit = parseInt(product.value.max_guest) - parseInt(product.value.min_guest);
        const remain = max_limit - c;
        // console.log("ELSE: Current:", c);
        if (remain<1) addOnsRequest.value.bed = max_limit;

        if (product.value.extra_bed_rate >0) {
            const result = subTotals.value.find( ({ name }) => name === 'extrabed' );
            let subtotal =  parseFloat(product.value.extra_bed_rate) * addOnsRequest.value.bed; 
            const subtotal_str = parseFloat(subtotal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            removeArrByName(subTotals.value, 'servicefee');
            if (result===undefined){
                subTotals.value.push(
                {
                    index: 1,
                    name: 'extrabed',
                    label: 'Extra Bed (x1)',
                    value: subtotal_str,
                    key: getSubtotalsKey(),
                    raw: {
                        rate: product.value.extra_bed_rate,
                        qty: addOnsRequest.value.bed,
                        total: subtotal,
                    }
                });
            } else {
                if (addOnsRequest.value.bed==0){
                    removeArrByName(subTotals.value, 'extrabed');
                } else {
                    result.label = 'Extra Bed (x' + c + ')';
                    result.value = subtotal_str;
                    result.raw.rate=product.value.extra_bed_rate;
                    result.raw.qty=addOnsRequest.value.bed;
                    result.raw.total=subtotal;
                }            
            }

            recomputeFees(subTotals.value);
        }
    }
);

watch(() => addOnsRequest.value.breakfast, (c, b) => { 
    if (product.value.breakfast_rate > 0) {
        const result = subTotals.value.find( ({ name }) => name === 'breakfast' );
        let subtotal =  parseFloat(product.value.breakfast_rate) * addOnsRequest.value.breakfast; 
        const subtotal_str = parseFloat(subtotal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        removeArrByName(subTotals.value, 'servicefee');
        if (result===undefined){
            subTotals.value.push(
            {
                index: 1,
                name: 'breakfast',
                label: 'Breakfast (x1)',
                value: subtotal_str,
                key: getSubtotalsKey(),
                raw: {
                    rate: product.value.extra_bed_rate,
                    qty: addOnsRequest.value.bed,
                    total: subtotal,
                }
            });
        } else {
            if (addOnsRequest.value.breakfast==0){
                removeArrByName(subTotals.value, 'breakfast');
            } else {
                result.label = 'Breakfast (x' + c + ')';
                result.value = subtotal_str;
                result.raw.rate=product.value.extra_bed_rate;
                result.raw.qty=addOnsRequest.value.bed;
                result.raw.total=subtotal;
            }

            
        }
        recomputeFees(subTotals.value);
    }
});

watch(() => addOnsRequest.value.adult, (c, b) => { 

    const extra_pax_limit = extraPaxLimit();
    const pax_count = extra_pax_limit.count;
    const max_limit = extra_pax_limit.limit;

    let new_max_limit = max_limit-pax_count;
    if (b>c) {
        new_max_limit += (b-c);
        if (addOnsRequest.value.adult==0 && extra_pax_limit.child == pax_count ) new_max_limit-=1;
    }

    if (new_max_limit<1) {
        addOnsRequest.value.adult=extra_pax_limit.adult;
        return;
    }

    if (pax_count <= max_limit) {
        let extra_pax_rate = parseFloat(product.value.extra_pax_rate);
        let subtotal =  extra_pax_rate * addOnsRequest.value.adult;
        const subtotal_str = parseFloat(subtotal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        const result = subTotals.value.find( ({ name }) => name === 'adult' );
        removeArrByName(subTotals.value, 'servicefee');
        if (result===undefined){
            subTotals.value.push(
            {
                index: 1,
                name: 'adult',
                label: 'Adult (x1)',
                value: subtotal_str,
                key: getSubtotalsKey(),
                raw: {
                    rate: product.value.extra_pax_rate,
                    qty: addOnsRequest.value.adult,
                    total: subtotal,
                }
            });
        } else {
            if (addOnsRequest.value.adult==0){
                removeArrByName(subTotals.value, 'adult');
            } else {
                result.label = 'Adult (x' + c + ')';
                result.value = subtotal_str;
                result.raw.rate=product.value.extra_pax_rate;
                result.raw.qty=addOnsRequest.value.adult;
                result.raw.total=subtotal;
            }
        }
        recomputeFees(subTotals.value);
    }    
});

watch(() => dateValue.value, (c, b) => { 
    // console.log("before:", b);
    // console.log("after:", c);
    // console.log("Date Diff:", );
    let days = DateDiff.inDays(new Date(c[0]), new Date(c[1]));

    if (product.value && product.value.rate!== undefined) {
        subTotals.value.length=0;
        days = days==0? 1 : days;
        let subtotal =  parseFloat(product.value.rate) * parseInt(days); 
        let adminFee = subtotal * fee_rate;
        const adminFee_str = parseFloat(adminFee).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const subtotal_str = parseFloat(subtotal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        subTotals.value.push(
            {
                index: 0,
                name: 'rate',
                label: '₱'+ (product.value.rate).toLocaleString() +'x'+ days+' Night'+ (days==1?'':'s'),
                value: subtotal_str,
                key: 1,
                raw: {
                    rate: product.value.rate,
                    qty: days,
                    total: subtotal,
                }
            },
            {
                index: 3,
                name: 'servicefee',
                label: 'Service Fee',
                value: adminFee_str,
                key: 2,
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
        max_extra.value = product.value.max_guest - product.value.min_guest;
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

        if (title==='HIGHLIGHTS'){
            highlights.value = group_src;
        }
    });
}

const submitForm = async () => {
    // console.log('submitForm');
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

    // console.log("subTotals:", subTotals.value);
    // console.log("Total:", totals.value);
    // return;

    try {
        
        console.log('Submit Product ID: ',  id.value);
        console.log('Rate: ', product.value.rate, 'Selected Dates:', dateValue.value, "subtotals:", subTotals.value, "Totals:", totals.value, " jstring:", JSON.stringify(subTotals.value));

        const fee = subTotals.value.find( ({ name }) => name === 'servicefee' );

        // console.log("SERVICE FEE: ", fee);
        // return;

        // return;
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
        formData.append('service_fee_rate', (fee!=undefined)? fee.raw.rate:0);
        formData.append('service_fee', (fee!=undefined)? fee.raw.total:0);
        formData.append('rate', product.value.rate);
        formData.append('subtotals', JSON.stringify(subTotals.value));
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
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
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
            <div class="grid grid-cols-4 gap-1">
                <div class="col-span-3">
                    <p class="font-semibold text-lg py-2">{{ product.listing_category?.name }}</p>
                </div>
                <div class="item-end">
                    <Ratings :rating="5"></Ratings>
                </div>
            </div>
            
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
        
        <div class="flex md:flex-row flex-wrap flex-col-reverse mb-4">
            <div class="w-full md:w-1/2 mt-4">
                <div class="bg-gray-200 px-10 py-4 text-sm rounded-lg">
                    <div class="mb-4 ">
                        <h1 class="font-semibold text-2xl mb-4">What this place offers</h1>
                    </div>

                    <div class="mb-4 border-t-2" v-for="item in groupAttributes">
                        <template v-if="item.title !== 'HIGHLIGHTS'">
                        <h1 class="font-bold text-xs my-2">{{ item.title }} </h1>
                        <div class="p-2 inline-grid gap-2 grid-cols-3 w-full">
                            
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
                        </template>
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
                                    <input type="number" id="max_guest" name="max_guest" min="1" v-model="no_of_guest" :max="product?.min_guest" class="w-full rounded-md border border-gray-500 bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" placeholder="Number of Guest">
                                </div>
                                
                                <div class="w-full max-w-sm mb-4">
                                    <div class="text-center mb-2">
                                        <a @click.prevent="addOnsView=!addOnsView" class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline cursor-pointer" v-if="subTotals.length>0">Click here for more request</a>
                                    </div>
                                    <div class="w-full text-sm p-4 bg-gray-200 rounded-lg" v-show="addOnsView">
                                        <h5 class="font-semibold mb-2 text-center">Add-Ons</h5>
                                        <div class="inline-grid gap-2 grid-cols-2 w-full mb-4">
                                            <div class="text-start flex items-center">
                                                <span>Bed</span>
                                            </div>
                                            <div class="text-center">
                                                <input type="number" id="extra_bed" name="extra_bed" min="0" v-model="addOnsRequest.bed" class="w-[52px] text-center rounded-md border border-gray-500 bg-white p-1 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" placeholder="Qty">
                                            </div>

                                            <div class="text-start flex items-center">
                                                <span>Breakfast</span>
                                            </div>
                                            <div class="text-center">
                                                <input type="number" id="breakfast" name="breakfast" min="0" v-model="addOnsRequest.breakfast" class="w-[52px] text-center rounded-md border border-gray-500 bg-white p-1 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" placeholder="Qty">
                                            </div>
                                        </div>

                                        <div class="">
                                            <div class="py-4">
                                                <div class="bg-gray-300 p-4 rounded-md items-center">
                                                    <ul class="space-y-2 list-disc px-5">
                                                        <li>
                                                            <strong class="text-green-900">Maximum extra guest allowed</strong> - Up to {{max_extra}}. Including children.
                                                        </li>
                                                        <li v-if="product?.free_below_age>0">
                                                            <strong class="text-green-900">FREE</strong> - Children with age below {{ product?.free_below_age }}
                                                        </li>
                                                    </ul>
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="inline-grid gap-2 grid-cols-2 w-full">

                                            <div class="text-start flex items-center">
                                                <span>Extra Pax - Adult</span>
                                            </div>
                                            <div class="text-center">
                                                <input type="number" id="adult" name="adult" min="0" v-model="addOnsRequest.adult" class="w-[52px] text-center rounded-md border border-gray-500 bg-white p-1 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" placeholder="Qty">
                                            </div>

                                            <div class="text-start flex items-center">
                                                <span>Extra Pax - Children</span>
                                            </div>
                                            <div class="text-center">
                                                <span class="mr-2">Age</span>
                                                <input type="number" id="children_age" name="children_age" min="0" max="14" v-model="addOnsRequest.children_age" class="w-[52px] mr-2 text-center rounded-md border border-gray-500 bg-white p-1 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" placeholder="Age">
                                                <button @click.prevent="addPax()" class="px-2 py-1 font-bold text-sm rounded-full bg-cyan-300 hover:bg-cyan-600">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="allowCheckout">
                                    <button type="submit" class="flex w-full justify-center rounded-md bg-[#1A7D7A] px-3 py-4 text-lg font-semibold leading-6 text-white shadow-sm hover:bg-[#0F9C98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Pa Reserve</button>
                                </div>
                                <div v-else class="text-center">
                                    <p>Not a customer yet?</p>
                                    <a @click.prevent="guestCheckoutAttempt('Register')" class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline cursor-pointer">Sign up Now</a> or <a @click.prevent="guestCheckoutAttempt('Login')" class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline cursor-pointer">Log-in</a> to book.
                                </div>
                                
                                <div class="" v-if="highlights.length>0">
                                    <div class="py-2">
                                        <div class="bg-green-300 p-4 rounded-md items-center text-sm">
                                            <ul class="space-y-2 list-disc px-4">
                                                <li v-for="item in highlights" class="flex item-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" class="mr-2 h-4 w-4 text-primary dark:text-primary-400 text-green-800">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                                    {{ item.value }}
                                                </li>
                                            </ul>
                                            
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                        <div class="border-t border-gray-300">
                            <div class="p-2 inline-grid gap-2 grid-cols-2 w-full text-sm">
                                <template v-for="item in subTotals">
                                    <div class="text-start font-semibold items-center">
                                        <span>{{ item?.label }}</span>
                                        <button @click.prevent="deleteSubtotalItem(item?.key)" class="ml-1 text-dark-400 hover:text-red-400 content-stretch bg-red-400 hover:bg-red-600 p-1 rounded-full" v-if="item?.name==='children'">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>

                                        </button>
                                    </div>
                                    <div class="text-end">
                                        ₱{{ item?.value }}
                                    </div>
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

        <div class="flex">
            <h1 class="font-semibold text-2xl my-2">Product Reviews</h1>
        </div>
    </div>
</div>
<SimpleToast :toast-message="toastMessage" />
</template>