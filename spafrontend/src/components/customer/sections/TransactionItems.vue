<script setup>
import { toRef,ref  } from 'vue'
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

</script>
<template>
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
                Ref #: {{ item.booking_reference }}
            </div>
            <h4 class="absolute top-4 right-0 p-3 bg-gray-100 font-semibold">
                {{ item.booking_status }}
            </h4>
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
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b dark:border-neutral-500" v-for="item in collection">
                        <td class="whitespace-nowrap px-6 py-4 truncate">{{ item.product_name }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ item.from }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ item.to }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ item.days }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ formatFigure(item.total) }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ item.booking_reference }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ item.booking_status }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ item.payment_status }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ item.created_at }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>