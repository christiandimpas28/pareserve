<script setup>
import { ref } from 'vue'

const props = defineProps({
  form: { 
    type: Object, 
    default: {
        book_id: 0,
        user_id: 0,
        product_id: 0,
        photos: [],
        remarks: null,
        related_to: null,
        other: null,
        settled: 0,
    } 
  },
  header: { type: Object, default: { title: 'What happened?', subTitle: '' } },
  errors: { type: Array,default: ()=> [ { remarks: [] } ] }
});

const photoUpload = ref([]);
const preProbs = ref([
    'Unresponsive host', 'Inaccurate', 'Unfair', 'Missing promised amenities', 'Privacy', 'Safety', 'Unacceptable element', 'Other'
]);

const onFileChange = (e) => {
    const selected_files = e.target.files;
    // photoUpload.value.length = 0;
    // props.form.photos.length = 0;
    // const upload_count = (props.form.photos==null)?0:props.form.photos.length;
    if (props.form.photos==null) props.form.photos=[];
    if (selected_files.length>0 && props.form.photos.length<10) {
        let new_limit = 10-props.form.photos.length;
        new_limit = (new_limit>selected_files.length)? selected_files.length: new_limit;
        for(let i=0; i<new_limit; i++) {
            props.form.photos.push(selected_files[i]);
            photoUpload.value.push({
                name: selected_files[i].name,
                isIamge: isFileImage(selected_files[i].name),
                url: URL.createObjectURL(selected_files[i])
            });

        }
        
    }
    console.log("Selected files", photoUpload.value);
}

const getFileExtension = (filename) => {
    // get file extension
    const extension = filename.substring(filename.lastIndexOf('.') + 1, filename.length);
    return extension;
}

const isFileImage = (filename) => {
    const imagesExt = Array('gif', 'jpg', 'jpeg', 'tif', 'png');
    const fileExt = getFileExtension(filename);

    const exist = imagesExt.find( (ext) => {
        return ext.toLowerCase() === fileExt.toLowerCase();
    });

    if (exist == undefined) return false;
    return true;
}

const resetUpload = () => {
    props.form.photos.length = 0;
    photoUpload.value.length = 0;
}


</script>
<template>
    <div class="md-4 mx-auto w-full max-w-[820px] min-w-[600px] bg-white">
        <form @submit.prevent="$emit('submitreport', props.form)" method="POST" enctype="multipart/form-data">
            <input type="hidden" v-model="form.user_id" id="user_id" name="user_id" />
            <input type="hidden" v-model="form.book_id" id="book_id" name="book_id" />
            <input type="hidden" v-model="form.product_id" id="product_id" name="product_id" />

            <div class="mb-4 pt-3">
                <p class="mb-4 text-base font-medium text-gray-800">What's the problem related to? <span class="py-1 px-3 mt-2 rounded-full bg-gray-200 font-normal text-sm ">Answer: <strong>{{ form.related_to }}</strong></span></p>
                <div class="flex flex-wrap text-sm mb-4">
                    <a @click="form.related_to=rel" v-for="rel in preProbs" class="[&.active]:bg-cyan-400 whitespace-nowrap py-1 px-4 mr-2 mt-2 rounded-full bg-gray-300 hover:bg-cyan-400 hover:text-gray-800 cursor-pointer">{{ rel }}</a>
                </div>
                <div class="mb-4" v-if="form.related_to==='Other'">
                    <input type="text"
                        v-model="form.other"
                        name="related_to"
                        id="related_to"
                        placeholder="Other"
                        class="rounded-md border border-[#e0e0e0] bg-white py-2 px-2 text-base font-medium text-[#6B7280] outline-none focus:ring-blue-500 focus:border-blue-500"
                        maxlength="80"
                        required
                    />
                </div>
                
            </div>

            <div class="mb-4 pt-3">
                <label for="remarks" class="mb-3 block text-base font-medium text-gray-800" >Remarks</label>
                <div v-if="errors.remarks">
                    <span class="text-red-400 text-sm m-2 p-2 bg-red-50 rounded-md">{{ errors.remarks[0] }}</span>
                </div> 
                <textarea id="remarks" maxlength="5000" v-model="form.remarks" required rows="6" class="w-[400px] block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-600 dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Briefly explain what happened."></textarea>
            </div>
            <div class="mb-4">
                <div class="grid grid-cols-4 gap-2">
                    <div class="w-full md:p-1" v-for="item in photoUpload">
                        <img
                            v-if="item?.isIamge"
                            alt="gallery"
                            class="block h-full w-full rounded-lg object-cover object-center"
                            :src="item?.url" />
                        <span v-if="!item?.isIamge" class="px-4 py-2 rounded-full bg-gray-200">{{ item?.name }}</span>
                    </div>
                </div>
                <a @click="resetUpload()" class="text-sm cursor-pointer underline hover:no-underline text-cyan-600" v-if="form.photos?.length>0">Clear Uploads</a>
            </div>
            <div class="mb-4">
                <label
                    for="ref_photo"
                    class="mb-3 block text-base font-medium text-[#07074D]"
                    >Upload Supporting docs, screenshots, pictures ...   (10 Max) - Optional</label
                >
                <input
                    class="relative m-0 block w-full min-w-0 flex-auto rounded border border-solid border-neutral-300 bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-neutral-700 transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-neutral-100 file:px-3 file:py-[0.32rem] file:text-neutral-700 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-neutral-200 focus:border-primary focus:text-neutral-700 focus:shadow-te-primary focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:file:bg-neutral-700 dark:file:text-neutral-100 dark:focus:border-primary"
                    ref="inputFile"
                    type="file"
                    id="ref_photo"
                    accept="*" 
                    @change="onFileChange"
                    multiple
                />
            </div>
            <div class="flex mb4 justify-end">
                <button class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                    Submit
                </button>
            </div>
        </form>

        
    </div>
</template>