<script setup>
import { watch, toRef  } from 'vue'
const props = defineProps({
  form: { type: Object, default: {
        id: 0,
        name: null,
        bus_contact_name: null,
        bus_contact_no: null,
        bus_email: null,
        bus_address: null,
    } 
  },
  formState: { type: Number, default: -1 },
  errorMsg: { type: String, default: 'Oops! Something went wrong.' }
});

toRef(() => props.formState);
toRef(() => props.errorMsg);
// const formState.value = computed(() => props.formState);
// toRefs 
watch(() => {
  console.log("Watch The initial formState", props.formState);
  console.log("Watch The initial errorMsg", props.errorMsg);
});

</script>




<template>
<div class="flex items-center justify-center p-8">
  <!-- Author: FormBold Team -->
  <!-- Learn More: https://formbold.com -->
  
  <div class="mx-auto w-full max-w" v-if="formState==0">
    <h1 class="text-2xl sm:text-xl font-medium flex justify-center">
      Set up your Business Profile.
    </h1>
    <p>
      Lorem Ipsum is simply dummy text of the printing and typesetting industry.
    </p>
    <div class="flex justify-center">
      <button class="text-white my-4 bg-teal-700 hover:bg-teal-800 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-teal-600 dark:hover:bg-teal-700 dark:focus:ring-teal-800" @click.prevent="formState=2">
        Start
      </button>
    </div>
  </div>  

  <div v-if="formState==1">
    <div class="items-center justify-center w-full text-center">
        <h1 class="text-3xl font-medium">
          {{ form.name }}
          <button @click="formState=2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-2 ">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
          </button>
        </h1>
        <p>{{ form.bus_address }}</p>
    </div>

    <div class="grid grid-cols-3 gap-4 mt-10">
        <div class="flex flex-col items-center justify-center overflow-hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
            </svg>
            <p class="flex-wrap h-24">{{ form.bus_email }}</p>
        </div>
        <div class="flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>

            <p class="flex-wrap h-24">{{ form.bus_contact_name }}</p>
        </div>
        <div class="flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
            </svg>
            <p class="flex-wrap h-24">{{ form.bus_contact_no }}</p>
        </div>
    </div>
  </div>

  <div class="mx-auto w-full max-w-[550px] bg-white" v-if="formState==2">
    <form @submit.prevent="submit(form)" method="POST">
      <div class="mb-5">
        <label
          for="name"
          class="mb-3 block text-base font-medium text-[#07074D]"
        >
            Business Name
        </label>
        <input
          type="text" 
          v-model="form.name"
          name="name"
          id="name"
          placeholder="Business/Company Name"
          class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
          required
        />
      </div>
      <div class="mb-5">
        <label
          for="bus_address"
          class="mb-3 block text-base font-medium text-[#07074D]"
        >
          Business / Company Address
        </label>
        <input
          type="text"
          v-model="form.bus_address"
          name="bus_address"
          id="bus_address"
          placeholder="Enter your business/company address"
          class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
          required
        />
      </div>
      <div class="mb-5">
        <label
          for="email"
          class="mb-3 block text-base font-medium text-[#07074D]"
        >
          Email Address
        </label>
        <input
          type="email"
          v-model="form.bus_email"
          name="email"
          id="email"
          placeholder="Enter your email"
          class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
          required
        />
      </div>
      
      <div class="mb-5 pt-3">
        <label
          class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl"
        >
          Primary Contacts
        </label>
        <div class="-mx-3 flex flex-wrap">
          <div class="w-full px-3 sm:w-1/2">
            <div class="mb-5">
              <input
                type="text"
                v-model="form.bus_contact_name"
                name="bus_contact_name"
                id="bus_contact_name"
                placeholder="Enter Contact Name"
                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                required
              />
            </div>
          </div>
          <div class="w-full px-3 sm:w-1/2">
            <div class="mb-5">
              <input
                type="text"
                v-model="form.bus_contact_no"
                name="bus_contact_no"
                id="bus_contact_no"
                placeholder="Enter Contact Number"
                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                required
              />
            </div>
          </div>
        </div>
      </div>

      <div>
        <button class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
          Save
        </button>
        <button @click.prevent="()=>{ formState = form.id!==undefined? 1: 0; }" class="text-slate-400 hover:text-sky-400 content-stretch">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </form>
  </div>

</div>

<div id="toast-success" class="hidden fixed right-10 bottom-10 px-5 py-4 flex items-center w-full max-w-xs mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
  <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
      <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
      <span class="sr-only">Check icon</span>
  </div>
  <div class="ml-3 text-sm font-normal">Your profile details have been saved.</div>
  <!-- <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-success" aria-label="Close">
      <span class="sr-only">Close</span>
      <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
  </button> -->
</div>

<div id="toast-danger" class="hidden fixed right-10 bottom-10 px-5 py-4 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
  <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
      <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
      <span class="sr-only">Error icon</span>
  </div>
  <div class="ml-3 text-sm font-normal">{{ errorMsg }}</div>
  <!-- <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-danger" aria-label="Close">
      <span class="sr-only">Close</span>
      <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
  </button> -->
</div>


</template>

<script>
  export default {
    name: 'ProfileForm',
    // mounted() {
    //   console.log("mounted this.form" , this.form);
    // },
    methods: {
      submit(fromData) {
        // console.log("Emit Submit Sate ", this.formState);
        this.$emit('save-data', fromData);
      },
      showToast(mode) {
        // this.formState = state;
        this.$emit('show-toast', mode);
      }
    },
  };

// https://formbold.com/templates/appointment-form - Form Template
// https://www.youtube.com/watch?v=Fb_uMTP4sk4 -Vue or https://tallpad.com/series/vuejs-misc 
// https://tallpad.com/series/vuejs-misc/lessons/create-reusable-form-components-using-vuejs-3-multiple-v-models

</script>
