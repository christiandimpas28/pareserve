<script setup>
import { ref, watch  } from 'vue';
import axios from "axios";
import ImageGallery from './ImageGallery.vue';

const imagePreviewCollection = ref([]);
const imageUploadCollection = ref([]);
const galleryTitle = ref('Property Gallery');
const imageUploadTitle =ref('Image Upload Preview');

const props = defineProps({
  form: { 
    type: Object, 
    default: {
        id: 0,
        listing_category_id: 0,
        name: null,
        slug: null,
        description: null,
        max_guest: 2,
        enabled: 1,
        discount: 0.0,
        rate: 0.0,
        photos: [],
        product_attributes: []
    } 
  },
  parent: {
    type: Object,
    default: {
        id: 0,
        name: null,
    }
  },
  files: { type: Array, default: () => []},
  action: { type: String, default: 'POST' },
});

const emit = defineEmits(['submit']);

watch(() => props.form.name, (c, b) => { 
    if (c!=null && c.length>1) {
        props.form.slug = string_to_slug(c);
    }
});

watch(() => props.form.photos, (c, b) => { 
   if(props.form.id != 0 && props.form.photos !== undefined && props.form.photos != null && props.form.photos.length>0){
    const arr_product_photos = props.form.photos.split(',');
    imagePreviewCollection.value=[];
    console.log("product_photos", props.form.photos, "Is ARRAY", arr_product_photos);
    arr_product_photos.forEach( (item, index)=> {
        let img_name = item.replace(/^\s+|\s+$/gm,'');
        // console.log(index, "Item:", img_name);
        imagePreviewCollection.value.push({
            id: index,
            product_id: props.form.id,
            listing_category_id: props.form.listing_category_id,
            name: img_name,
            url: 'http://localhost:8000/uploads/' + img_name
        });
    });    
   }
});


const string_to_slug = (str) => {
    str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();
  
    // remove accents, swap ñ for n, etc
    var from = "àáãäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to   = "aaaaaeeeeiiiioooouuuunc------";

    for (var i=0, l=from.length ; i<l ; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes
    return str;
}

const onFileChange = (e) => {
    const files = e.target.files;
    console.log("onFileChange", files);
    imageUploadCollection.value = [];
    props.files.length = 0;
    for(let i=0; i<files.length; i++) {
        if (i>(3-imagePreviewCollection.value.length)) break;
        props.files.push(files[i]);
        imageUploadCollection.value.push({
            name: files[i].name,
            url: URL.createObjectURL(files[i])
        });
    }
}

// Emits

const submit = () => {
    // console.log("Emit Submit", props.form, props.files);
    imageUploadCollection.value =[];
    document.getElementById("photos").value=null;
    emit('save-data', { inputData: props.form, inputFiles: props.files});
    // this.$emit('save-data', fromData);
}

const deleteImage = async (item) => {
    console.log("Parent deleteImage", item);
    try {
        const response = await axios.delete('/api/partner/product/delete/photo/'+item.product_id, { data: item });        
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
        props.form.photos = rd.photos;
        console.log("rd", rd, "props.form", props.form);
        console.log("new_items", imagePreviewCollection.value);
    } catch (error) {
        console.log("deleteImage Error", error);
    }
}

const deleteProduct = () => {
    console.log("Delete Click - Triggered");
    console.log("Listing:", props.form.listing_category_id , "Product:", props.form.id, "Name:", props.form.name);
}

const close = () => {
    console.log("Close Click - Triggered");
    imagePreviewCollection.value = [];
    imageUploadCollection.value =[];
    props.files.length = 0;    
    document.getElementById("photos").value=null;
    emit('close-form');
}

</script>

<template>
    <div class="flex md:flex-row flex-wrap">
        
        <div class="w-full">
            <div class="mb-4">
                <ImageGallery :collection="imagePreviewCollection" :mode="1" :title="galleryTitle" @delete-image="deleteImage"/>
            </div>
            <form @submit.prevent="submit()" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" v-model="form.id" />
                <input type="hidden" name="listing_category_id" id="listing_category_id" v-model="form.listing_category_id" />
                <input type="hidden" name="slug" id="slug" v-model="form.slug" />
                <div class="mb-4">
                    <label for="name" class="mb-3 block text-base font-medium text-[#07074D]">Product Name</label>
                    <input type="text" v-model="form.name" name="name" id="name" required
                    placeholder="Product/Unit Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                    />
                    <p class="text-[#161616cc]">{{ form.slug }}</p>
                </div>
                <div class="mb-4">
                    <label for="description" class="mb-3 block text-base font-medium text-[#07074D]">Description</label>
                    <input type="text" v-model="form.description" name="description" id="description" required maxlength="800"
                    placeholder="Brief Product Description" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                    />
                </div>
                <div class="mb-4">
                    <p class="mt-6 mb-4 text-base font-medium">Now is the time to think economically and set your nightly rate.</p>
                    <div class="-mx-3 flex flex-wrap">
                        <div class="w-full px-3 sm:w-1/2">
                            <div class="mb-4">
                                <label for="rate" class="mb-3 block text-base font-medium text-[#07074D]">Rate</label>
                                <input type="number" v-model="form.rate" name="rate" id="rate" required maxlength="10"
                                placeholder="Nightly Rate" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                                />
                            </div>
                        </div>
                        <div class="w-full px-3 sm:w-1/2 hidden">
                            <div class="mb-4">
                                <label for="discount" class="mb-3 block text-base font-medium text-[#07074D]">Discount</label>
                                <input type="text" v-model="form.discount" name="discount" id="discount" disabled required maxlength="6"
                                placeholder="Percentage 0.0% or Fixed rate e.g.: 0.0" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="-mx-3 flex flex-wrap">
                        <div class="w-full px-3 sm:w-1/2">
                            <div class="mb-4">
                                <label for="max_guest" class="mb-3 block text-base font-medium text-[#07074D]">Max Guest</label>
                                <input type="number" v-model="form.max_guest" name="max_guest" id="max_guest" required
                                placeholder="Max Guest" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
                                />
                            </div>
                        </div>
                        <div class="w-full px-3 sm:w-1/2">

                        </div>
                        <!-- <div class="w-full px-3 sm:w-1/2">
                            <div class="flex items-center mb-4 mt-4">
                                <input id="checked-checkbox" type="checkbox" v-model="form.enabled" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-500 dark:border-gray-100">
                                <label for="checked-checkbox" class="ml-2 text-md text-base font-medium text-[#07074D]">Enable</label>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="mb-4">
                    <ImageGallery :collection="imageUploadCollection" :mode="0" :title="imageUploadTitle"  />
                </div>
                <div class="mb-8">
                    <label
                        for="photos"
                        class="mb-3 block text-base font-medium text-[#07074D]"
                        >Photos (Maximum of 4 items only)</label>
                    <p class="mt-4 mb-4 text-base font-medium">Make sure that your photos are clear and high quality. You may want to consider taking the opportunity to invest in a professional photographer.</p>
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
                    <!-- Create -->
                    <template v-if="action==='POST'">
                        <button class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                            Save
                        </button>
                    </template>

                    <!-- Edit -->
                    <template v-else>
                        <button class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                            Update
                        </button>
                        <button @click.prevent="deleteProduct()" class="text-white mr-4 bg-[#F77E7E] hover:bg-[#f53e3e] focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-[#F77E7E] dark:hover:bg-[#f53e3e] dark:focus:ring-[#F77E7E]">
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