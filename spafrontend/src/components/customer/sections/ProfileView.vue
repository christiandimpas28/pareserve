<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  form: { 
    type: Object, 
    default: {
        user_type: null,
        email: null,
        name: null,
        customerprofile: {
            id: 0,
            user_id: null,
            first_name: null,
            last_name: null,
            contact_no: null,
            billing_address: null,
            id_type: null,
            id_photo: null,
        }
    } 
  },
  header: { type: Object, default: { title: 'Profile', subTitle: '' } }
});

const identificationPhotoPreview = ref('http://localhost:8000/uploads/id-card.png');

onMounted(() => {
    if (props.form.customerprofile.id_photo != null && props.form.customerprofile.id_photo.length>0){
        identificationPhotoPreview.value = 'http://localhost:8000/uploads/' + props.form.customerprofile.id_photo;
    }
});

const emit = defineEmits(['edit']);

const edit = ()=> {
    emit('edit-form');
}

</script>
<template>
    <div class="md-4">
        <h2 class="text-title-md2 font-bold text-black white:text-dark">
            {{ header.title }}
            <button @click="edit()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-2 ">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </button>
        </h2>
        <p class="text-sm">Welcome {{ form.name }}, Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, </p>
    </div>

    <div class="md-4 p-5">
        <div class="w-46 sm:w-56 mx-auto">
            <img :src="identificationPhotoPreview" class="object-cover w-full " />
        </div>
    </div>

    <div class="grid am:grid-cols-1 grid-cols-3 gap-4 mt-5">
        <div class="flex flex-col items-center justify-center overflow-hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
            </svg>
            <p class="flex-wrap">{{ form.email }}</p>
        </div>
        <div class="flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>

            <p class="flex-wrap">{{ form.customerprofile.last_name }}, {{ form.customerprofile.first_name  }}</p>
        </div>
        <div class="flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
            </svg>
            <p class="flex-wrap">{{ form.customerprofile.contact_no }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 mt-5 mb-4">
        <div class="flex flex-col items-center justify-center overflow-hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="flex-wrap">{{ form.customerprofile.billing_address }}</p>
        </div>
    </div>

</template>