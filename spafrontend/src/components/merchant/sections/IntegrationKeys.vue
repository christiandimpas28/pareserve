<script setup>
import { computed, watch, ref,toRef } from 'vue';
const props = defineProps({
  form: { 
        type: Object, 
        default: {
            id: null,
            private_key: null,
            public_key: null,
            enabled: null,
            remarks: null,
        } 
  },
  merchant_id: { type: Number, default: null }
});

// watch(() => props.form?.id, (c, b) => { 
//     console.log("b:", b, ", c:",c);
// });
const noKeys = computed(() => {
    if (props.form?.id === undefined) return true
    return false
});

const isEnabled = computed(() => {
    if (props.form?.enabled === 1) return true
    return false
});

toRef(() => props.form);

const emit = defineEmits(['submit']);

const submit = () => {
    // console.log("props.merchant_id", props.merchant_id)
    emit('request-data', props.merchant_id );
}

const copyText = (el_id) => {
    const element = document.getElementById(el_id);
    element.select();
    element.setSelectionRange(0, 99999);
    document.execCommand('copy');
}

</script>

<template>
    <h2 class="text-title-md2 font-bold text-black white:text-dark">Integration </h2>
    <div class="my-4">
        <form @submit.prevent="submit()" method="POST" v-show="noKeys" >
            <button type="submit" id="submit" class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-2 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">Request Integration Keys</button>
        </form>
    </div>
    <div class="mb-4 " v-show="!noKeys">
        <div class="border-l-4 border-blue-600 px-2 mb-6" v-show="isEnabled">
            <h2 class="font-semibold ">Important! </h2>
            <p>The private key should always be kept secure and do not f*ckn share these keys to anyone. Otherwise you're dooomed!</p>
        </div>

        <div class="border-l-4 border-red-600 px-2 mb-6" v-show="!isEnabled">
            <h2 class="font-semibold ">Alert! </h2>
            <p>Remarks: {{ form?.remarks }}</p>
        </div>

        <div class="mb-4" >
            <h2 class="font-semibold text- mb-2 flex">
                Private Key
                <button @click="copyText('private_key')" class="ml-2 p-1 rounded-md hover:bg-blue-500 border border-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                    </svg>
                </button>

            </h2> 
            
            <div class="mb-4">
                <textarea :value="form?.private_key" readonly="readonly" id="private_key"  rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-400 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-400 dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
            </div>
            <h2 class="font-semibold text- mb-2">
                Public Key
                <button @click="copyText('public_key')" class="ml-2 p-1 rounded-md hover:bg-blue-500 border border-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                    </svg>
                </button>
            </h2>
            <div class="mb-4">
                <textarea :value="form?.public_key" readonly="readonly" id="public_key"  rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-400 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-400 dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
            </div>
        </div>
    </div>
</template>