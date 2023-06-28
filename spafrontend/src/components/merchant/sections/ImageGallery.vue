<script setup>
import { computed } from 'vue';
const props = defineProps({
    collection: { type: Array, default: [] },
    mode: { type: Number, default: 0},
    title: { type: String, default: ''},
})

const uploadFilesCount = computed(() => {
    return props.collection.length;
});

const emit = defineEmits(['deleteImage']);

const deleteImage = (item) => {
    // console.log("Delete Image Triggered");
    // console.log("Image:", item);
    emit('delete-image', item);
}



</script>

<template>

  <div class="-m-1 flex flex-wrap md:-m-2">
    <div class="w-full justify-center mt-4" v-if="uploadFilesCount>0">
      <h1 class="mb-1 block text-base font-medium text-[#07074D] text-center">{{ title }}</h1>
    </div>
    
    <template v-if="mode==0">
      <div class="flex w-1/3 flex-wrap" v-for="item in collection">
        <div class="w-full md:p-1">
          <img
            alt="gallery"
            class="block h-full w-full rounded-lg object-cover object-center"
            :src="item?.url" />
        </div>
      </div>
    </template>

    <template v-if="mode==1">
      <div class="flex w-1/3 flex-wrap" v-for="item in collection">
        <div class="w-full relative md:p-1">
          <img
            alt="gallery"
            class="block h-full w-full rounded-lg object-cover object-center"
            :src="item?.url" />
          
          <button @click.prevent="deleteImage(item)" class="absolute top-1 right-1 text-white bg-[#F77E7E] hover:bg-[#F77E7E]/90 focus:ring-4 focus:outline-none focus:ring-[#F77E7E]/50 font-medium rounded-full text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#F77E7E]/50 mr-1 mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </template>
  </div>

</template>