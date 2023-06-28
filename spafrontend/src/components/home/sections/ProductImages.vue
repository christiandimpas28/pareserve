<script setup>
import { computed } from 'vue';
const props = defineProps({
    collection: { type: Array, default: [] },
});

const imageCount = computed(() => {
    return props.collection.length;
});

</script>

<template>
<div class="container mx-auto">
  <div class="-m-1 flex flex-wrap md:-m-2">
    <template v-if="imageCount==1">
        <div class="flex w-full flex-wrap">
            <img
            alt="gallery"
            class="block h-full w-full rounded-lg object-cover object-center"
            :src="collection[0]" />
        </div>
    </template>

    <template v-if="imageCount==2">
        <div class="flex w-1/2 flex-wrap">
            <div class="w-full p-1 md:p-2">
                <img
                alt="gallery"
                class="block h-full w-full rounded-lg object-cover object-center"
                :src="collection[0]"  />
            </div>
        </div>
        <div class="flex w-1/2 flex-wrap">
            <div class="w-full p-1 md:p-2">
                <img
                alt="gallery"
                class="block h-full w-full rounded-lg object-cover object-center"
                :src="collection[1]"  />
            </div>
        </div>
    </template>

    <template v-if="imageCount==3">
        <div class="flex w-1/2 flex-wrap">
            <div class="w-full p-1 md:p-2">
                <img
                alt="gallery"
                class="block h-full w-full rounded-lg object-cover object-center"
                :src="collection[0]"  />
            </div>
        </div>
        <div class="flex w-1/2 flex-wrap">
            <div class="w-full p-1 md:p-2">
                <img
                alt="gallery"
                class="block h-full w-full rounded-lg object-cover object-center"
                :src="collection[1]"  />
            </div>
            <div class="w-full p-1 md:p-2">
                <img
                alt="gallery"
                class="block h-full w-full rounded-lg object-cover object-center"
                :src="collection[2]"  />
            </div>
        </div>
    </template>

    <template v-if="imageCount>3">
        <div class="flex w-1/2 flex-wrap">
        <div class="w-full p-1 md:p-2">
            <img
            alt="gallery"
            class="block h-full w-full rounded-lg object-cover object-center"
            :src="collection[0]"  />
        </div>
        </div>
        
        <div class="flex w-1/2 flex-wrap">
        <div class="w-full p-1 md:p-2">
            <img
            alt="gallery"
            class="block h-full w-full rounded-lg object-cover object-center"
            :src="collection[1]"  />
        </div>
        <div class="w-1/2 p-1 md:p-2">
            <img
            alt="gallery"
            class="block h-full w-full rounded-lg object-cover object-center"
            :src="collection[2]"  />
        </div>
        <div class="w-1/2 p-1 md:p-2">
            <img
            alt="gallery"
            class="block h-full w-full rounded-lg object-cover object-center"
            :src="collection[3]"  />
        </div>
        </div>
    </template>
    </div>
</div>
    

</template>