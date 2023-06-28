<script setup>
const props = defineProps({
    totals: { 
        type: Object, 
        default: {
            counts: {
                all: 0,
                pending: 0,
                payment_submitted: 0,
                cancelled: 0,
                booked:0
            },
            figures: {
                all: 0,
                booked: 0,
                cancelled: 0,
            }
        } 
    },
    title: { type: String, default: 'Transaction Summary'},
    subtitle: { type: String, default: 'Count summary....'},
})

const formatFigure = (val) => {
    if (val==undefined) return "₱0.00";
    return "₱" + val.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

</script>
<template>
    <div class="mb-4">
        <h1 class="font-semibold text-md">{{ title }}</h1>
        
    </div>
    <div class="mb-4 p-4 inline-grid gap-8 grid-cols-4 w-full text-md bg-gray-10">
        <div class="items-center content-center text-center border-b-2 border-blue-400 mx-4">
            <h1 class="font-semibold text-2xl">{{ totals.counts?.all }}</h1>
            <p class="p-2 font-bold text-sm">All</p>
        </div>
        <div class="items-center content-center text-center border-b-2 border-yellow-200">
            <h1 class="font-semibold text-2xl">{{ totals.counts?.pending }}</h1>
            <p class="p-2 font-bold text-sm">Pending</p>
        </div>
        <div class="items-center content-center text-center border-b-2 border-red-400">
            <h1 class="font-semibold text-2xl">{{ totals.counts?.cancelled }}</h1>
            <p class="p-2 font-bold text-sm">Cancelled</p>
        </div>
        <div class="items-center content-center text-center border-b-2 border-cyan-600">
            <h1 class="font-semibold text-2xl">{{ totals.counts?.booked }}</h1>
            <p class="p-2 font-bold text-sm">Booked/Completed</p>
        </div>
    </div>

    <div class="mb-4 p-2 inline-grid gap-3 grid-cols-6 lg:grid-cols-6 md:grid-cols-6 sm:grid-cols-3 w-full text-md">
        <div class="col-span-3">
            <img src="http://localhost:8000/uploads/totals.png" class="h-auto max-w-full rounded-lg"/>
        </div>
        
        <div class="p-6">
            <h1 class="font-semibold text-lg">{{ formatFigure(totals.figures?.booked) }}</h1>
            <div>Booked</div>
        </div>
        <div class="p-6">
            <h1 class="font-semibold text-lg">{{ formatFigure(totals.figures?.cancelled) }}</h1>
            <div>Cancelled</div>
        </div>
        <div class="p-6">
            <h1 class="font-semibold text-lg">{{ formatFigure(totals.figures?.all) }}</h1>
            <div>Total</div>
        </div>
    </div>
</template>