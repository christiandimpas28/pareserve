<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  form: { 
    type: Object, 
    default: {
        id: 0,
        user_id: null,
        first_name: null,
        last_name: null,
        contact_no: null,
        billing_address: null,
        id_type: null,
        id_photo: null,
    } 
  },
  files: { type: Array, default: () => []},
  action: { type: String, default: 'POST' },
  header: { type: Object, default: { title: 'Profile', subTitle: '' } }
});

const identificationPhotoPreview = ref('http://localhost:8000/uploads/id-card.png');
const identificationPhotoUpload = ref({});

const emit = defineEmits(['submit', 'close']);


onMounted(() => {
    console.log("Mountef");
    if (props.form.id_photo != null && props.form.id_photo.length>0){
        const el = document.getElementById('id_photo');
        el.removeAttribute('required');
        identificationPhotoPreview.value = 'http://localhost:8000/uploads/' + props.form.id_photo;
    }
});



const submit = () => {
    // identificationPhotoUpload.value =[];
    // document.getElementById("id_photo").value=null;
    emit('save-data', { inputData: props.form, inputFiles: props.files});
    // console.log('SUBMIT');
}

const close = () => {
    console.log("Close Click - Triggered");
    identificationPhotoUpload.value = [];
    document.getElementById("id_photo").value=null;
    props.files.length = 0;    
    emit('close-form');
}

const onFileChange = (e) => {
    const selected_files = e.target.files;
    // console.log("onFileChange", selected_files);
    identificationPhotoUpload.value = [];
    props.files.length = 0;
    if (selected_files.length>0) {
        props.files.push(selected_files[0]);
        identificationPhotoUpload.value = {
            name: selected_files[0].name,
            url: URL.createObjectURL(selected_files[0])
        }
    }
    // console.log("Selected ID", identificationPhotoUpload.value);
}
</script>

<template>
    <div class="md-4">
        <h2 class="text-title-md2 font-bold text-black white:text-dark">
            {{ header.title }}
        </h2>
        <p>{{ header.subTitle }}</p>
    </div>

    <div class="md-4 p-5">
        <div class="w-46 sm:w-56 mx-auto">
            <img :src="identificationPhotoPreview" class="object-cover w-full " />
        </div>
    </div>
    <div class="md-4 mx-auto w-full max-w-[550px] bg-white">
        <form @submit.prevent="submit()" method="POST" enctype="multipart/form-data">
            <input type="hidden" v-model="form.user_id" id="user_id" name="user_id" />
            <div class="mb-4 pt-3">
                <label class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl">Full Name</label>
                <div class="-mx-3 flex flex-wrap">
                    <div class="w-full px-3 sm:w-1/2">
                        <div class="mb-5">
                            <input
                                type="text"
                                v-model="form.first_name"
                                name="first_name"
                                id="first_name"
                                placeholder="First Name"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                                required
                            />
                        </div>
                    </div>

                    <div class="w-full px-3 sm:w-1/2">
                        <div class="mb-5">
                            <input
                                type="text"
                                v-model="form.last_name"
                                name="last_name"
                                id="last_name"
                                placeholder="last Name"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                                required
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4 pt-3">
                <label for="contact_no" class="mb-3 block text-base font-medium text-[#07074D]" >Contact No.</label>
                <input type="text"
                    v-model="form.contact_no"
                    name="contact_no"
                    id="contact_no"
                    placeholder="Enter your contact number"
                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                    required
                />
            </div>

            <div class="mb-4 pt-3">
                <label for="billing_address" class="mb-3 block text-base font-medium text-[#07074D]" >Billing Address</label>
                <input type="text"
                    v-model="form.billing_address"
                    name="billing_address"
                    id="billing_address"
                    placeholder="Enter your billing address"
                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
                    required
                />
            </div>

            <div class="mb-4 pt-3">
                <label for="id_type" class="mb-3 block text-base font-medium text-[#07074D]" >Choose Identification Card Type</label>
                <select id="id_type" v-model="form.id_type" required class="bg-gray-50 border border-[#e0e0e0] text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 white:bg-gray-700 dark:border-[#e0e0e0] dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="Passport">Passport</option>
                    <option value="National ID">National ID</option>
                    <option value="SSS">SSS</option>
                    <option value="Philhealth">Philhealth</option>
                    <option value="Voters ID">Voter's ID</option>
                    <option value="Cedula">Cedula</option>
                    <option value="Company ID">Company ID</option>
                    <option value="Student ID">Voter's ID</option>
                </select>
            </div>

            <div class="md-4 p-5">
                <div class="w-46 sm:w-56 mx-auto">
                    <img :src="identificationPhotoUpload.url" class="object-cover w-full " />
                </div>
            </div>

            <div class="mb-4">
                <label
                    for="id_photo"
                    class="mb-3 block text-base font-medium text-[#07074D]"
                    >Identification Card Image</label
                >
                <input
                    class="relative m-0 block w-full min-w-0 flex-auto rounded border border-solid border-neutral-300 bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-neutral-700 transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-neutral-100 file:px-3 file:py-[0.32rem] file:text-neutral-700 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-neutral-200 focus:border-primary focus:text-neutral-700 focus:shadow-te-primary focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:file:bg-neutral-700 dark:file:text-neutral-100 dark:focus:border-primary"
                    ref="inputFile"
                    type="file"
                    id="id_photo"
                    accept="image/*" 
                    @change="onFileChange"
                    required
                />
            </div>

            <div class="mb-4">
                    <!-- Create -->
                    <template v-if="action==='POST'">
                        <button type="submit" class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                            Save
                        </button>
                    </template>

                    <!-- Edit -->
                    <template v-else>
                        <button type="submit" class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                            Update
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
</template>