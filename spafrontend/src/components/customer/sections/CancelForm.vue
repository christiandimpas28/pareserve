<script setup>
const props = defineProps({
  form: { 
    type: Object, 
    default: {
        id: 0,
        book_id: 0,
        user_id: 0,
        remarks: null,
        refunded: 0,
        refund_status: 'Pending'
    } 
  },
  header: { type: Object, default: { title: 'What happened?', subTitle: '' } },
  mode: { type: Number, default: 0 }
});
</script>
<template>
    <div class="md-4 mx-auto w-full max-w-[820px] min-w-[600px] bg-white">
        <template v-if="mode==0">
            <div class="p-6 border-gray-400 mb-4">
                CANCELLATION POLICIES
            </div>
            <div class="mb-4">
                <form @submit.prevent="$emit('submitreport', props.form)" method="POST" enctype="multipart/form-data">
                    <input type="hidden" v-model="form.user_id" id="user_id" name="user_id" />
                    <input type="hidden" v-model="form.book_id" id="book_id" name="book_id" />
                    <div class="mb-4 pt-3">
                        <label for="remarks" class="mb-3 block text-base font-medium text-gray-800" >Remarks</label> 
                        <textarea id="remarks" maxlength="1000" minlength="10" v-model="form.remarks" required rows="6" class="w-[400px] block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-600 dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Briefly explain why."></textarea>
                    </div>
                </form>
            </div>
            <div class="flex mb4 justify-end">
                <button id="btnCancelProceed" class="text-white mr-4 bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                    Proceed to Cancel Booking
                </button>
            </div>
        </template>
        <template v-else>
            <div class="mb-4 pt-3">
                <p class="mb-4 text-base font-medium text-gray-800">Refund Status: <strong>{{ form.refund_status }}</strong></p>
                <!-- <p class="mb-4 text-base font-medium text-gray-800">Status: <strong>{{ settled(form.settled) }}</strong></p> -->
            </div>
            <div class="mb-4">
                <div class="p-6 rounded-lg bg-gray-200 mb-6">
                    <p>{{ form.remarks }}</p>
                </div>
            </div>
            <div class="flex mb4 justify-end">
                <button id="btnCancelOk" class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                    OK
                </button>
            </div>
        </template>
    </div>
</template>