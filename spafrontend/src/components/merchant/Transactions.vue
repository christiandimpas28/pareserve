<script setup>
import { toRef, ref } from 'vue';
import axios from "axios";

import TransactionItems from './sections/TransactionItems.vue';

const collection = ref([]);
const title = ref('Booking Details');
const subtitle = ref('');
const view = ref('TABLE');


const props = defineProps({
    transactions: { type: Array, default: [] },
})

toRef(() => props.transactions);

const checkout = async (data) => {
    console.log("Main Callback:", data);
    try {

        let action_url = '/api/partner/checkout/'+data.books_id;
        const response = await axios.post(action_url);        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }

        // console.log("checkout", response.data);

        let checkout_item = props.transactions.filter(function(item) {
            if (item.books_id === data.books_id) return true;
            return false;
        });

        if (checkout_item.length==1) {
            checkout_item[0].booking_status = "Completed";
        }
        
    } catch (error) {
        console.log("error", error);
    }
}

</script>

<template>
    
    <TransactionItems :collection="transactions" @checkout="checkout"/>
</template>