<script setup>
import { onMounted, watch, ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { initFlowbite } from 'flowbite';
import axios from "axios";
import SideBar from '../components/admin/sections/sidebar.vue'

const router = useRouter();
const authStore = useAuthStore();

const showMain = ref(true);
const componentTitle = ref('');
const collections = ref([]);
const totals = ref({
    count:0,
    servicefees: 0,
    bookings: 0
});

onMounted(() => {
    initFlowbite();
    authStore.getUser('Customer Dashboard');
    showMain.value= router.currentRoute.value.name==='Admin';
    componentTitle.value = router.currentRoute.value.meta.title;
    console.log("FETCHING...");
    fetchData();
});

const fetchData = () => {

    try {
        let api_domain = '';
        const endpoints = [
            'api/admin/guest/users/profile',
            'api/admin/partner/users/profile',
            'api/admin/merchants',
            'api/admin/transaction/totals',
            'api/admin/alarming/cases',
        ];

        const promise1 = axios(endpoints[0]);
        const promise2 = axios(endpoints[1]);
        const promise3 = axios(endpoints[2]);
        const promise4 = axios(endpoints[3]);
        const promise5 = axios(endpoints[4]);
        Promise.all([promise1, promise2, promise3, promise4, promise5]).then(
            (values) => {
                collections.value = [
                    { model: 'guest.users', collection: values[0].data.data, count: values[0].data.data.length, title: 'Guest Users' },
                    { model: 'partner.users', collection: values[1].data.data, count: values[1].data.data.length, title: 'Merchant Users' },
                    { model: 'merchants.data', collection: values[2].data.data, count: values[2].data.data.length, title: 'Partner Count' },
                    { model: 'totals.data', collection: values[3].data.data, count: values[3].data.data.length, title: 'Transaction Count' },
                    { model: 'cases.data', collection: values[4].data.data, count: values[4].data.data.length, title: 'Alarming Cases' },
                ]                
                console.log("Collections Data: ", collections);

                //Totals
                if (collections.value[3].collection.length>0){
                    const ct = ['Booked', 'Completed'];
                    collections.value[3].collection.forEach( (item, i) => {
                        let m = ct.includes(item.booking_status);
                        if (m) {
                            totals.value.count +=1;
                            totals.value.servicefees += parseFloat(item.service_fee);
                            totals.value.bookings += parseFloat(item.total);
                        }
                    });

                    console.log("Total Summary: ", totals.value);
                }
            },
            (error) => {
                // console.log("Response Error: ", error.response);
                if (error.response.statusText === "Unauthorized"){
                    router.push({ name: 'Error401'})
                }

                if (error.response.status === 403){
                    router.push({ name: 'Error403'})
                }

                if (error.response.status === 406){
                    console.log("error.response 406", error.response);
                    if (error.response.data !==undefined && error.response.data.id !== undefined){
                        router.push({ name: "LoginVerify", params: { id: btoa(error.response.data.id) }});
                    } else {
                        router.push({ name: 'Error406'})
                    }
                }
            }
        )

    } catch (error) {
        console.log("fetchData Error: ", error)
        // if (error.response.statusText === "Unauthorized"){
        //     router.push({ name: 'Error401'})
        // }
    }

}

const moneyFormat = (val, sfx = '') => {
    return sfx + parseFloat(val).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}


router.beforeEach((to, from) => { 
    showMain.value= to.name==='Admin';
    componentTitle.value = to.meta.title;
});
</script>

<template>
    <div class="relative fles min-h-screen">
        <SideBar />
        <div class="p-4 sm:ml-64">
            <div class="bg-white shadow p-4 flex justify-between ...">
                <div>
                    <span class="py-2 px-4 bg-teal-200 rounded-full font-bold text-sm">{{ componentTitle }}</span>
                </div>
                <div></div>
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <button id="dropdownDividerButton" data-dropdown-toggle="customerDropdownDivider" type="button" class="inline-flex items-center">
                        {{ authStore.user?.name }}
                        <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="customerDropdownDivider" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                        <div class="py-2">
                            <a href="#" @click.prevent="authStore.handleLogout()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4" v-show="showMain">
                <div class="mb-4">
                    <dl class="grid max-w-screen-xl rounded-lg grid-cols-5 gap-5 py-4 text-gray-100 sm:grid-cols-3 xl:grid-cols-5 white:text-dark bg-gray-900">
                        <div class="flex flex-col items-center justify-center" v-for="collection in collections">
                            <dt class="mb-2 text-3xl font-bold">{{ collection.count }}</dt>
                            <dd class="text-gray-500 dark:text-cyan-100 text-md font-semibold justify-center">{{ collection.title }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="grid grid-cols-2 gap-8 mb-8">

                    <!-- Col 1-->
                    <div class="relative overflow-x-auto">
                        <h1 class="flex justify-center p-2 font-semibold text-lg">Guest Users</h1>
                        <table class="w-full text-sm text-left text-gray-700 border-gray-600 rounded-lg ">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Guest Name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Date Verified
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Signup Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody v-if="collections.length>0">
                                <tr class="bg-white border-b" v-for="item in collections[0].collection">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap white:text-dark">
                                        {{ item.name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ item.email }}
                                    </td>
                                    <td class="px-6 py-4 truncate">
                                        {{ item.email_verified_at }}
                                    </td>
                                    <td class="px-6 py-4 truncate">
                                        {{ item.created_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Col 2-->
                    <div class="relative overflow-x-auto">
                        <h1 class="flex justify-center p-2 font-semibold text-lg">Merchant Users</h1>
                        <table class="w-full text-sm text-left text-gray-700 border-gray-600 rounded-lg ">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Merchant Name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Date Verified
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Signup Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody v-if="collections.length>0">
                                <tr class="bg-white border-b" v-for="item in collections[1].collection">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap white:text-dark">
                                        {{ item.name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ item.email }}
                                    </td>
                                    <td class="px-6 py-4 truncate">
                                        {{ item.email_verified_at }}
                                    </td>
                                    <td class="px-6 py-4 truncate">
                                        {{ item.created_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="p-2 mb-4">
                        <h1 class="flex justify-center font-semibold text-lg">Total Summary</h1>
                        <p class="flex justify-center">Confirmed Bookings</p>
                    </div>
                    <div class="grid grid-cols-3 gap-8">
                        <div class="flex flex-col items-center justify-center">
                            <dt class="mb-2 text-3xl font-bold">{{ totals.count }}</dt>
                            <dd class="text-gray-700 text-md font-semibold justify-center">Total Number of Confirmed Bookings</dd>
                        </div>
                        <div class="flex flex-col items-center justify-center">
                            <dt class="mb-2 text-3xl font-bold">{{ moneyFormat(totals.servicefees, '₱ ') }}</dt>
                            <dd class="text-gray-700 text-md font-semibold justify-center">Total Revenue</dd>
                        </div>
                        <div class="flex flex-col items-center justify-center">
                            <dt class="mb-2 text-3xl font-bold">{{ moneyFormat(totals.bookings, '₱ ') }}</dt>
                            <dd class="text-gray-700 text-md font-semibold justify-center">Total Confirmed Bookings</dd>
                        </div>
                    </div>
                </div>
            </div>
            <router-view 
                v-bind:merchants="collections[1]?.collection"
                v-bind:guest="collections[0]?.collection"
                v-bind:partners="collections[1]?.collection"
                v-bind:cases="collections[4]?.collection"
            >
            </router-view>
        </div>
    </div>

</template>