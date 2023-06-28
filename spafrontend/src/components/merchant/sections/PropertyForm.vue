<script setup>
import { computed, ref, toRef, watch  } from 'vue'
import axios from "axios";
import ImageGallery from './ImageGallery.vue';

const imagePreviewCollection = ref([]);
const imageUploadCollection = ref([]);
const galleryTitle = ref('Property Gallery');
const imageUploadTitle = ref('Image Upload Preview');

const props = defineProps({
  form: { 
    type: Object, 
    default: {
        id: 0,
        name: null,
        category: null,
        description: null,
        address: null,
        city: null,
        listing_photos: null,
        merchant_id: null,
        products: [],
    } 
  },
  files: { type: Array, default: () => []},
  action: { type: String, default: 'POST' },

});

toRef(() => props.action);
toRef(() => props.form);
const emit = defineEmits(['submit']);

// const selected = ref(props.selected)

watch(() => props.form, (c, b) => { 
   if(props.form.id != 0 && props.form.listing_photos !== undefined && props.form.listing_photos != null && props.form.listing_photos.length>0){
    const arr_listing_photos = props.form.listing_photos.split(',');
    imagePreviewCollection.value=[];
    // console.log("listing_photos", props.form.listing_photos, "Is ARRAY", Array.isArray(arr_listing_photos));
    arr_listing_photos.forEach( (item, index)=> {
        let img_name = item.replace(/^\s+|\s+$/gm,'');
        // console.log(index, "Item:", img_name);
        imagePreviewCollection.value.push({
            id: index,
            listing_id: props.form.id,
            merchant_id: props.form.merchant_id,
            name: img_name,
            url: 'http://localhost:8000/uploads/' + img_name
        });
    });    
   }
});


const submit = () => {
    // console.log("Emit Submit", props.form, props.files);
    imageUploadCollection.value =[];
    document.getElementById("photos").value=null;
    emit('save-data', { inputData: props.form, inputFiles: props.files});
    // this.$emit('save-data', fromData);
}

const deleteListing = () => {
    console.log("Delete Triggered");
    console.log("Merchant:", props.form.merchant_id , "Listing:", props.form.id);
}

const deleteImage = async (item) => {
    console.log("Parent deleteImage", item);
    try {
        const response = await axios.delete('/api/partner/listings/delete/photo/'+item.listing_id, { data: item });        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(message);
        }
        const rd = await response.data.data;

        // console.log("deleteImage Success", rd, "gallery", imagePreviewCollection.value);
        const new_items = imagePreviewCollection.value.filter(function(image) {
            if (image['name'] === item.name) return false;
            return true;
        });
        imagePreviewCollection.value = new_items
        console.log("new_items", imagePreviewCollection.value);
    } catch (error) {
        console.log("deleteImage Error", error);
    }
}

const close = () => {
    // console.log("Close Click - Triggered");
    imageUploadCollection.value =[];
    props.files.length = 0;    
    document.getElementById("photos").value=null;
    emit('close-form');
}

const onFileChange = (e) => {
    const files = e.target.files;
    // console.log("onFileChange Form", props.form);
    // console.log(files.length, isArray(files), files);
    imageUploadCollection.value = [];
    props.files.length = 0;
    for(let i=0; i<files.length; i++) {
        
        // console.log(files[i]);
        // console.log(URL.createObjectURL(files[i]));
        if (i>(5-imagePreviewCollection.value.length)) break;
        props.files.push(files[i]);
        imageUploadCollection.value.push({
            name: files[i].name,
            url: URL.createObjectURL(files[i])
        });
    }    
}

const cleaner = () => {
    imageUploadCollection.value = [];
    props.files.length = 0;    
    document.getElementById("photos").value=null;
    // console.log('Cleaner Activated');
}

defineExpose({
    cleaner
});
</script>

<template>
<div class="mx-auto max-w-lg justify-center">
    <div class="mx-auto w-full max-w-lg">
        <h2 class="text-title-lg font-bold text-black white:text-dark">
            Yosh! List your property today.
        </h2>
        <p class="text-md text-gray-700 mb-8">
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s...
        </p>
    </div>
    <div class="mx-auto w-full max-w-lg bg-white">
        <div class="mb-4">
            <ImageGallery :collection="imagePreviewCollection" :mode="1" :title="galleryTitle" @delete-image="deleteImage"/>
        </div>
        <form @submit.prevent="submit()" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id" v-model="form.id" />
            <input type="hidden" name="merchant_id" id="merchant_id" v-model="form.merchant_id" />
            <div class="mb-4">
                <label for="name" class="mb-3 block text-base font-medium text-[#07074D]">Property Name</label>
                <input type="text" v-model="form.name" name="name" id="name" required
                placeholder="Property Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                />
            </div>
            <div class="mb-4">
                <label for="category" class="mb-3 block text-base font-medium text-[#07074D]">Category</label>
                <input type="text" v-model="form.category" name="category" id="category" required
                placeholder="Category" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                />
            </div>
            <div class="mb-4">
                <label for="description" class="mb-3 block text-base font-medium text-[#07074D]">Description</label>
                <input type="text" v-model="form.description" name="description" id="description" required maxlength="800"
                placeholder="Brief Description of your property" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                />
            </div>
            <div class="mb-4">
                <label for="address" class="mb-3 block text-base font-medium text-[#07074D]">Address</label>
                <input type="text" v-model="form.address" name="address" id="address" required  maxlength="1000"
                placeholder="Property Address" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                />
            </div>
            <div class="mb-4">
                <label for="itcy" class="mb-3 block text-base font-medium text-[#07074D]">City</label>
                <input type="text" v-model="form.city" name="city" id="city" required
                placeholder="City" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                />
            </div>
            <div class="mb-4">
                <ImageGallery :collection="imageUploadCollection" :mode="0" :title="imageUploadTitle"  />
            </div>
            <div class="mb-8">
                <label
                    for="photos"
                    class="mb-3 block text-base font-medium text-[#07074D]"
                    >Photos (Maximum of 6 items only)</label
                >
                <input
                    class="relative m-0 block w-full min-w-0 flex-auto rounded border border-solid border-neutral-300 bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-neutral-700 transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-neutral-100 file:px-3 file:py-[0.32rem] file:text-neutral-700 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-neutral-200 focus:border-primary focus:text-neutral-700 focus:shadow-te-primary focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:file:bg-neutral-700 dark:file:text-neutral-100 dark:focus:border-primary"
                    ref="inputFile"
                    type="file"
                    id="photos"
                    accept="image/*" 
                    @change="onFileChange"
                    multiple />
            </div>
            <div class="mb-4">
                <template v-if="action==='POST'">
                    <button class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                        Save
                    </button>
                </template>
                <template v-if="action==='PUT'">
                    <button class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                        Update
                    </button>
                    <button @click.prevent="deleteListing()" class="text-white mr-4 bg-[#F77E7E] hover:bg-[#f53e3e] focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-[#F77E7E] dark:hover:bg-[#f53e3e] dark:focus:ring-[#F77E7E]">
                        Delete
                    </button>
                </template>
                <button @click.prevent="close()" class="text-slate-400 hover:text-sky-400 content-stretch">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
    

</div>

</template>

<!-- <script>
// export default {
// name: 'PropertyForm',
//     methods: {
//         submit(fromData) {
//             this.$emit('save-data', fromData);
//         },
//         showToast(mode) {
//             this.$emit('show-toast', mode);
//         }
//     },
// };
</script> -->
