<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  form: { 
    type: Object, 
    default: {
        book_id: 0,
        user_id: 0,
        product_id: 0,
        rating: 0,
        photos: [],
        review: null,
    } 
  },
  files: { type: Array, default: () => []},
  action: { type: String, default: 'POST' },
  header: { type: Object, default: { title: 'Write Review', subTitle: '' } }
});

const defaultColors = ['fill-gray-300','fill-gray-500'];
const colorColors = ['fill-[#f8de40]','fill-[#e7c930]'];

const colorValues= ref([
    defaultColors, defaultColors, defaultColors, defaultColors, defaultColors
]);

const photoUpload = ref([]);

const emit = defineEmits(['submit']);

const submit = () => {
    console.log('SUBMIT', props.form );
    // emit('ratenow', { inputData: props.form, inputFiles: props.files});
}

const rate = (val) => {
    props.form.rating = val;
    console.log("You rate: ", props.form.rating);
    colorValues.value.length = 0;
    for (let x=0; x < 5; x++){
        if ((x+1)<=val) {
            colorValues.value.push(colorColors);
        } else {
            colorValues.value.push(defaultColors);
        }
    }
}

const onFileChange = (e) => {
    const selected_files = e.target.files;
    // photoUpload.value.length = 0;
    // props.form.photos.length = 0;
    // const upload_count = (props.form.photos==null)?0:props.form.photos.length;
    if (props.form.photos==null) props.form.photos=[]
    if (selected_files.length>0 && props.form.photos.length<5) {
        let new_limit = 5-props.form.photos.length;
        new_limit = (new_limit>selected_files.length)? selected_files.length: new_limit;
        for(let i=0; i<new_limit; i++) {
            props.form.photos.push(selected_files[i]);
            photoUpload.value.push({
                name: selected_files[i].name,
                url: URL.createObjectURL(selected_files[i])
            });

        }
        
    }
    console.log("Selected files", photoUpload.value);
}


const resetUpload = () => {
    props.form.photos.length = 0;
    photoUpload.value.length = 0;
}

</script>
<template>
    <!-- <div class="md-4">
        <h2 class="text-title-md2 font-bold text-black white:text-dark">
            {{ header.title }}
        </h2>
        <p>{{ header.subTitle }}</p>
    </div> -->
    <div class="md-4 mx-auto w-full max-w-[820px] min-w-[600px] bg-white">
        <form @submit.prevent="submit()" method="POST" enctype="multipart/form-data">
            <input type="hidden" v-model="form.user_id" id="user_id" name="user_id" />
            <input type="hidden" v-model="form.book_id" id="book_id" name="book_id" />
            <input type="hidden" v-model="form.product_id" id="product_id" name="product_id" />
            <input type="hidden" v-model="form.rating" id="rating" name="rating" />

            <div class="mb-4 pt-3">
                <label for="review" class="mb-3 block text-base font-medium text-gray-800" >Rate your experience</label>
                <ul class="flex items-center justify-start gap-1 pl-2 cursor-pointer">
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6" @click="rate(1)">
                            <rect x="1" y="1" width="22" height="22" rx="7.656" :class="colorValues[0][0]"/>
                            <path class="fill-gray-900" d="M7.055 7.313A1.747 1.747 0 1 0 8.8 9.059a1.747 1.747 0 0 0-1.745-1.746zM16.958 7.313A1.747 1.747 0 1 0 18.7 9.059a1.747 1.747 0 0 0-1.742-1.746z"/>
                            <path d="M23 13.938a14.69 14.69 0 0 1-12.406 6.531c-5.542 0-6.563-1-9.142-2.529A7.66 7.66 0 0 0 8.656 23h6.688A7.656 7.656 0 0 0 23 15.344z" :class="colorValues[0][1]"/>
                            <path class="fill-gray-900" d="M15.1 13.157a10.752 10.752 0 0 0-.291-1.243 6.685 6.685 0 0 0-.489-1.22 3.624 3.624 0 0 0-.872-1.109 2.354 2.354 0 0 0-.669-.386 2.217 2.217 0 0 0-1.548 0 2.423 2.423 0 0 0-.67.386 3.636 3.636 0 0 0-.872 1.109 6.728 6.728 0 0 0-.49 1.221 10.575 10.575 0 0 0-.29 1.242 16.869 16.869 0 0 0-.228 2.5c0 .438 0 .874.021 1.31a.133.133 0 0 0 .265.012c.062-.428.128-.853.2-1.276.133-.8.3-1.6.507-2.372a9.171 9.171 0 0 1 .84-2.171 2.8 2.8 0 0 1 .661-.8 1.3 1.3 0 0 1 1.654 0 2.812 2.812 0 0 1 .662.8 9.207 9.207 0 0 1 .841 2.172c.206.773.373 1.568.506 2.372.075.421.14.843.2 1.268a.133.133 0 0 0 .264-.012c.021-.433.025-.867.02-1.3a16.979 16.979 0 0 0-.222-2.503z"/>
                        </svg>
                    </li>

                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6" @click="rate(2)">
                            <rect x="1" y="1" width="22" height="22" rx="7.656" :class="colorValues[1][0]"/>
                            <path class="fill-gray-900" d="M7.055 7.313A1.747 1.747 0 1 0 8.8 9.059a1.747 1.747 0 0 0-1.745-1.746zM16.958 7.313A1.747 1.747 0 1 0 18.7 9.059a1.747 1.747 0 0 0-1.742-1.746z"/>
                            <path :class="colorValues[1][1]" d="M23 13.938a14.69 14.69 0 0 1-12.406 6.531c-5.542 0-6.563-1-9.142-2.529A7.66 7.66 0 0 0 8.656 23h6.688A7.656 7.656 0 0 0 23 15.344z"/>
                            <path class="fill-gray-900" d="M16.083 12.556A5.487 5.487 0 0 0 12 10.806a5.487 5.487 0 0 0-4.083 1.75c-.959 1.292-.147 2.667.885 2.583s2.781-.285 3.2-.285 2.167.2 3.2.285 1.84-1.291.881-2.583z"/>
                            <path d="M13.75 13.266c-1.344-.3-1.75.109-1.75.109s-.406-.406-1.75-.109a2.463 2.463 0 0 0-1.65 1.87 1.1 1.1 0 0 0 .207 0c1.031-.083 2.781-.285 3.2-.285s2.167.2 3.2.285a1.1 1.1 0 0 0 .207 0 2.463 2.463 0 0 0-1.664-1.87z" class="fill-[#f06880]"/>
                            <path :class="colorValues[1][1]" d="M13.965 15.91a9.842 9.842 0 0 0-1.965-.3 9.842 9.842 0 0 0-1.965.3c-.294.061-.3.3 0 .261s1.965-.13 1.965-.13 1.663.09 1.965.13.294-.2 0-.261z"/>
                            <path class="fill-gray-900" d="M19.686 6.658a2.954 2.954 0 0 0-.228-.385 4.467 4.467 0 0 0-.576-.675c-.108-.1-.217-.205-.332-.3s-.242-.174-.364-.26A3.4 3.4 0 0 0 17.8 4.8c-.134-.066-.263-.143-.4-.2a4.857 4.857 0 0 0-1.743-.4 3.732 3.732 0 0 0-1.334.177.174.174 0 0 0 .007.327c.406.139.784.271 1.157.41.494.184.973.367 1.442.576.121.043.233.107.351.158l.178.076c.06.025.114.059.174.085.116.054.23.112.35.161h.011c.114.06.229.119.348.175.247.105.476.244.735.355.128.06.254.124.386.186a.173.173 0 0 0 .224-.228zM9.691 4.38a3.729 3.729 0 0 0-1.334-.18 4.862 4.862 0 0 0-1.743.4c-.139.055-.269.132-.4.2a3.4 3.4 0 0 0-.384.231c-.122.086-.246.169-.363.26s-.224.2-.332.3a4.474 4.474 0 0 0-.577.675 2.948 2.948 0 0 0-.227.385.173.173 0 0 0 .227.236c.131-.062.258-.126.386-.186.259-.111.487-.25.734-.355.119-.056.235-.115.349-.175h.01c.12-.049.235-.107.35-.161.06-.026.115-.06.174-.085l.178-.076c.118-.051.231-.115.352-.158a24.71 24.71 0 0 1 1.441-.576c.373-.139.751-.271 1.158-.41a.174.174 0 0 0 .001-.325z"/>
                        </svg>
                    </li>

                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6" @click="rate(3)">
                            <rect x="1" y="1" width="22" height="22" rx="7.656" :class="colorValues[2][0]"/>
                            <path class="fill-gray-900" d="M7.055 7.313A1.747 1.747 0 1 0 8.8 9.059a1.747 1.747 0 0 0-1.745-1.746zM16.958 7.313A1.747 1.747 0 1 0 18.7 9.059a1.747 1.747 0 0 0-1.742-1.746z"/>
                            <path :class="colorValues[2][1]" d="M23 13.938a14.69 14.69 0 0 1-12.406 6.531c-5.542 0-6.563-1-9.142-2.529A7.66 7.66 0 0 0 8.656 23h6.688A7.656 7.656 0 0 0 23 15.344z"/>
                            <ellipse class="fill-gray-900" cx="12" cy="13.375" rx="5.479" ry=".297"/>
                            <ellipse :class="colorValues[2][1]" cx="12" cy="14.646" rx="1.969" ry=".229"/>
                        </svg>
                    </li>

                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6" @click="rate(4)">
                            <rect x="1" y="1" width="22" height="22" rx="7.656" :class="colorValues[3][0]"/>
                            <path class="fill-gray-900" d="M7.055 7.313A1.747 1.747 0 1 0 8.8 9.059a1.747 1.747 0 0 0-1.745-1.746zM16.958 7.313A1.747 1.747 0 1 0 18.7 9.059a1.747 1.747 0 0 0-1.742-1.746z"/>
                            <path :class="colorValues[3][1]" d="M23 13.938a14.69 14.69 0 0 1-12.406 6.531c-5.542 0-6.563-1-9.142-2.529A7.66 7.66 0 0 0 8.656 23h6.688A7.656 7.656 0 0 0 23 15.344z"/>
                            <path class="fill-gray-900" d="M16.6 12.25a8.622 8.622 0 0 1-4.6 1.271 8.622 8.622 0 0 1-4.6-1.271s-.451-.273-.169.273 1.867.93 1.882 1.133a4.862 4.862 0 0 0 5.782 0c.015-.2 1.6-.586 1.882-1.133s-.177-.273-.177-.273z"/>
                            <path :class="colorValues[3][1]" d="M14.422 14.961a4.8 4.8 0 0 1-4.844 0c-.424-.228-.476.164.352.656a4.093 4.093 0 0 0 2.07.656 4.093 4.093 0 0 0 2.07-.656c.83-.492.776-.884.352-.656z"/>
                        </svg>
                    </li>

                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6" @click="rate(5)">
                            <rect x="1" y="1" width="22" height="22" rx="7.656" :class="colorValues[4][0]"/>
                            <path d="M23 13.938a14.69 14.69 0 0 1-12.406 6.531c-5.542 0-6.563-1-9.142-2.529A7.66 7.66 0 0 0 8.656 23h6.688A7.656 7.656 0 0 0 23 15.344z" :class="colorValues[4][1]"/>
                            <path d="M21.554 5.693c-.063-.289-2.888-.829-4.871-.829a5.584 5.584 0 0 0-3.3.7A3.125 3.125 0 0 1 12 5.919a3.125 3.125 0 0 1-1.381-.352 5.584 5.584 0 0 0-3.3-.7c-1.983 0-4.808.54-4.871.829s-.113 1.217.088 1.381.439.025.477.6.477 2.976 1.808 3.767 3.741.163 4.6-.365A4.3 4.3 0 0 0 11.3 8.568c.138-.892.351-1.507.7-1.507s.565.615.7 1.507a4.3 4.3 0 0 0 1.883 2.51c.854.528 3.264 1.155 4.6.365s1.77-3.189 1.808-3.767.276-.439.477-.6.149-1.095.086-1.383z" class="fill-[#101820]"/>
                            <path d="M13.308 14.129a1.183 1.183 0 0 0 .434-.908 1.34 1.34 0 0 0-.984-1.2.312.312 0 0 0-.414.228v.005a.312.312 0 0 0 .2.355c.215.081.575.269.575.613 0 .425-.5.608-.516.616a.317.317 0 0 0-.21.3.311.311 0 0 0 .226.292.717.717 0 0 1 .5.68c0 .269-.366.453-.584.54a.31.31 0 0 0-.145.119l-.1.024.061.243a.311.311 0 0 0 .412.2c.366-.146.98-.486.98-1.123a1.285 1.285 0 0 0-.435-.984z" class="fill-gray-900"/>
                            <path d="M19.1 13.013a1.3 1.3 0 0 0-1.768.1l-.381.382-.382-.381a1.3 1.3 0 0 0-1.768-.1 1.249 1.249 0 0 0-.048 1.813l1.885 1.887a.441.441 0 0 0 .625 0l1.886-1.887a1.25 1.25 0 0 0-.049-1.814z" class="fill-[#f06880]"/>
                        </svg>
                    </li>
                    <li>
                        <span class="bg-gray-300 px-1 py-1 rounded-full text-sm ml-2">{{ form.rating }}/5</span>
                    </li>
                </ul>
            </div>
            <div class="mb-4 pt-3">
                <label for="review" class="mb-3 block text-base font-medium text-gray-800" >Review</label>
                <textarea id="review" maxlength="2000" v-model="form.review" required rows="6" class="w-[400px] block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-600 dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here..."></textarea>
            </div>
            <div class="mb-4">
                <div class="grid grid-cols-4 gap-2">
                    <div class="w-full md:p-1" v-for="item in photoUpload">
                        <img
                            alt="gallery"
                            class="block h-full w-full rounded-lg object-cover object-center"
                            :src="item?.url" />
                    </div>
                </div>
                <a @click="resetUpload()" class="text-sm cursor-pointer underline hover:no-underline text-cyan-600" v-if="form.photos?.length>0">Clear Uploads</a>
            </div>
            <div class="mb-4">
                <label
                    for="id_photo"
                    class="mb-3 block text-base font-medium text-[#07074D]"
                    >Photos (5 Max)</label
                >
                <input
                    class="relative m-0 block w-full min-w-0 flex-auto rounded border border-solid border-neutral-300 bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-neutral-700 transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-neutral-100 file:px-3 file:py-[0.32rem] file:text-neutral-700 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-neutral-200 focus:border-primary focus:text-neutral-700 focus:shadow-te-primary focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:file:bg-neutral-700 dark:file:text-neutral-100 dark:focus:border-primary"
                    ref="inputFile"
                    type="file"
                    id="ref_photo"
                    accept="image/*" 
                    @change="onFileChange"
                    multiple
                />
            </div>
            <div class="mb4">
                <button class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                    Save
                </button>
            </div>

        </form>
    </div>

</template>