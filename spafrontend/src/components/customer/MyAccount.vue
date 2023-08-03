<script setup>
import { onMounted, watch, ref } from 'vue';
import { useAuthStore } from '../../stores/auth';
import SimpleToast from '../SimpleToast.vue';
import axios from "axios";

const authStore = useAuthStore();

const toastMessage = ref('Oops!');
const pwd = ref({
                    oldPassword: null,
                    newPassword: null
            });

onMounted(async () => {
    authStore.getUser('Customer User');
});

const submit = async () => {
    try {
        const response = await axios.post('/change-password', {
            old_password: pwd.value.oldPassword,
            new_password: pwd.value.newPassword,
        });
        
        if (!response) {
            const message = 'An error has occured: ${response.status}';
            throw new Error(response);
        }

        toastMessage.value = response.data.message;
        showToast(1);
        authStore.handleLogout();
    } catch (error) {
        console.log("error", error.response.data);
        toastMessage.value = error.response.data.message;
        showToast(3);
    }
}

const showToast = (mode) => {
    let el = document.getElementById("toast-success");
    if (mode==2) el = document.getElementById("toast-warning");
    if (mode==3) el = document.getElementById("toast-danger");
    
    el.classList.remove("hidden"); 
    setTimeout(function () {
        el.classList.add("hidden");
    }, 5000);
}

</script>

<template>
    <div class="pb-12 pt-10 md:pt-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div class="mx-auto w-full max-w mb-4">
                <h1 class="text-3xl sm:text-xl font-bold flex justify-start">
                    User Account
                </h1>
                <p class="flex justify-start">
                    Our secure platform complies with industry-leading standards, meeting and exceeding data protection regulations.
                </p>
            </div>

            <div class="mx-auto w-full max-w mb-8 mt-8">
                <h1 class="text-lg font-medium flex justify-start">
                    {{ authStore.user?.name }}
                </h1>
                <p>{{ authStore.user?.email }}</p>
            </div>

            <div class="mx-auto w-full max-w mb-8 mt-8 bg-gray-200 rounded-lg p-8">
                <h1 class="text-md font-medium flex justify-start">
                   Manage Password
                </h1>
                <div>
                    <form @submit.prevent="submit()" method="POST" >
                    <div class="py-4 inline-grid gap-2 grid-cols-2 w-full text-md">
                        <div>
                            <label for="old_pwd" class="mb-3 block text-base font-medium text-[#07074D]" >Old Password</label>
                            <input type="password" v-model="pwd.oldPassword" name="old_pwd" id="old_pwd" placeholder="Old Password" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                        </div>
                        <div>
                            <label for="new_pwd" class="mb-3 block text-base font-medium text-[#07074D]" >New Password</label>
                            <input type="password" v-model="pwd.newPassword" name="new_pwd" id="new_pwd" placeholder="New Password" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                        </div>
                    </div>
                    <dvi>
                        <button type="submit" class="justify-end rounded-md bg-[#1A7D7A] px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-[#0F9C98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Update</button>
                    </dvi>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <SimpleToast :toast-message="toastMessage" />
</template>