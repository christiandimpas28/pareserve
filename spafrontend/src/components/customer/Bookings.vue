<script setup>
import { toRef, ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import TransactionItems from './sections/TransactionItems.vue';

const authStore = useAuthStore();

const collection = ref([]);
const title = ref('Booking Details');
const subtitle = ref('');
const view = ref('TABLE');


const props = defineProps({
    transactions: { type: Array, default: [] },
})

toRef(() => props.transactions);

</script>

<template>
    <div class="p-4">
        <TransactionItems :collection="transactions"></TransactionItems>
    </div>
</template>