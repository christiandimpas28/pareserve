<script setup>
import { ref, toRef  } from 'vue'
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

    console.log('Today Date:', todays_date, ' Checkout:', checkout_date);
    console.log("Condition: ", todays_date >= checkout_date);
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
                            <button @click="checkout(item)" class="font-normal text-xs px-2 py-2 bg-green-300 rounded-lg border-b-2 border-green-600" v-if="isCheckout(item)">Check-out</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    </div>
</template>