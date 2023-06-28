<script setup>
    // https://formbold.com/templates/appointment-form - Form Template
    // https://www.youtube.com/watch?v=Fb_uMTP4sk4 -Vue or https://tallpad.com/series/vuejs-misc 
    // https://tallpad.com/series/vuejs-misc/lessons/create-reusable-form-components-using-vuejs-3-multiple-v-models

    const props = defineProps({
      data: Object,
    });
</script>


<template>
<div class="flex items-center justify-center p-12">
  <!-- Author: FormBold Team -->
  <!-- Learn More: https://formbold.com -->
  <div class="mx-auto w-full max-w-[550px] bg-white">
    <form @submit.prevent="submit(data)" method="POST">
      <div class="mb-5">
        <label
          for="name"
          class="mb-3 block text-base font-medium text-[#07074D]"
        >
            Business Name
        </label>
        <input
          type="text"
          @input="$emit('update:name', $event.target.value)"
          :value="name" 
          name="name"
          id="name"
          placeholder="Business/Company Name"
          class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
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
          @input="$emit('update:bus_address', $event.target.value)"
          :value="bus_address" 
          name="bus_address"
          id="bus_address"
          placeholder="Enter your business/company address"
          class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
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
          @input="$emit('update:bus_email', $event.target.value)"
          :value="bus_email" 
          name="email"
          id="email"
          placeholder="Enter your email"
          class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
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
                @input="$emit('update:bus_contact_name', $event.target.value)"
                :value="bus_contact_name" 
                name="bus_contact_name"
                id="bus_contact_name"
                placeholder="Enter Contact Name"
                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
              />
            </div>
          </div>
          <div class="w-full px-3 sm:w-1/2">
            <div class="mb-5">
              <input
                type="text"
                @input="$emit('update:bus_contact_no', $event.target.value)"
                :value="bus_contact_no" 
                name="bus_contact_no"
                id="bus_contact_no"
                placeholder="Enter Contact Number"
                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
              />
            </div>
          </div>
        </div>
      </div>

      <div>
        <button
          class="hover:shadow-form w-full rounded-md bg-[#6A64F1] py-3 px-8 text-center text-base font-semibold text-white outline-none"
        >
          Save
        </button>
      </div>
    </form>
  </div>
</div>
</template>

<script>
 export default {
    name: 'ProfileForm',
    props: {
      data: {
        type: Object
      },
        name: {
            type: String,
            default: '',
            required: true,
        },
        bus_contact_name: {
            type: String,
            default: '',
            required: true,
        },
        bus_contact_no: {
            type: String,
            default: '',
            required: true,
        },
        bus_email: {
            type: String,
            default: '',
            required: true,
        },
        bus_address: {
            type: String,
            default: '',
            required: true,
        }
    },
    methods: {
      submit(obj) {
        this.$emit('save-data');
      }
    },
 };


</script>
