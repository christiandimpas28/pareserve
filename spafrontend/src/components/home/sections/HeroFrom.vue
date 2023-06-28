<script setup>
const props = defineProps({
  form: { type: Object, default: {
        query: null,
        start: null,
        end: null,
    } 
  },
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
    <section class="pt-18 sm:pt-10 w-full h-[460px] flex flex-col sm:flex-row justify-evenly bg-[url('https://images.pexels.com/photos/7130472/pexels-photo-7130472.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1')] bg-cover bg-center">
        <div class="basis-1/2 lg:px-16 sm:px-4 relative">
            <h1 class="text-4xl text-white text-left px-16 pt-12 pb-4 font-bold">
                Pa reserve namo Mam, Ser
                <br />
                <span class="text-2x1 font-normal text-transparent bg-clip-text bg-gradient-to-br from-[#28c71e] to-[#ffffff]">
                    Lorem Ipsum is simply dummy text of the iut ell ew..
                </span>
            </h1>
        </div>
        <div class="basis-1/2 mx-auto">
            <div class="border rounded-lg shadow-xl shadow-zinc-900 border-gray-300 h-[280px] w-[400px] sm:w-2/2 2x1:w-2/2 sm:-h-4/6 justify-center items-center bg-gray-50">
                <div class="flex flex-col gap-2 w-3/4 item-center mx-auto pt-6">
                    <h1 class="text-gray-900 text-xl font-semibold mt-4">Search locations, hotels, homes, resorts, and much more...</h1>
                    <p class="text-gray-700 text-sm font-normal">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard..</p>
                    <form @submit.prevent="submit()" method="POST">   
                        <label for="query" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="search" id="query" name="query" v-model="form.query" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-200 dark:border-gray-600 dark:placeholder-gray-400  dark:focus:ring-cyan-500 dark:focus:border-cyan-500" placeholder="Search" required>
                            <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">Search</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
</template>