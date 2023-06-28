<script setup>
import { toRef, watch } from 'vue';

const props = defineProps({
  form: { type: Object, default: {
        query: null,
        start: null,
        end: null,
    } 
  },
});

toRef(() => props.form);

watch(() => props.form, (c, b) => { 
    if (c!=null) {
        console.log("Watch: ", c);
    }
});

const emit = defineEmits(['submit']);

const submit = () => {
    // const dateStart = document.getElementById("date-start");
    // const dateEnd = document.getElementById("date-end");
    // props.form.start = dateStart.value;
    // props.form.end = dateEnd.value;
    console.log("Emit Search", props.form);
    emit('search-form', props.form );
}

</script>

<template>
    <div class="flex flex-col gap-2 w-3/4 item-center mx-auto p-8 bg-gray-800 rounded-md mb-10">
        <h1 class="text-gray-100 text-xl font-semibold">Search locations, hotels, homes, resorts, and much more...</h1>
        <p class="text-gray-300 text-sm font-normal">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, </p>
        <form @submit.prevent="submit()" method="POST">   
            <label for="query" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="search" id="query" name="query" v-model="form.query" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-cyan-500 dark:focus:border-cyan-500" placeholder="Search cities, locations, hotels, or Property type..." required>
                <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">Search</button>
            </div>
        </form>
        
    </div>

</template>