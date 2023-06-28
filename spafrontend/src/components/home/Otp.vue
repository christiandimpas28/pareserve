<script setup>
import { ref } from 'vue';
import { onMounted } from 'vue';
import { useAuthStore } from "../../stores/auth"

const authStore = useAuthStore();
 onMounted(async ()=> {
    if (authStore.user == null) {
        await authStore.getUser('Otp');
    } 
    await authStore.otpInit();
 });

const form = ref({
  otp: "",
});


</script>

<template>
    <div class="grid grid-cols-6 gap-4">
        <div class="col-start-2 col-span-4 ...">
          <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm justify-center">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">OTP Verification</h2>
            <p>We've sent a verification to your email:</p>
            <p class="text-gray-500">{{ authStore.user?.email }}</p>

            <form class="space-y-6" @submit.prevent="authStore.otpVerify(form)">
              <div>
                <div class="mt-2">
                  <input id="otp" name="otp" v-model="form.otp" type="input" required="" class="text-2xl justify-center text-center block w-full p-4 text-gray-900 border border-gray-200 rounded-lg sm:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-100 dark:border-gray-600 dark:placeholder-gray-400 white:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                
                <!-- <div class="mt-2">
                  <p><span>Time Left:</span> {{ timerCount }}</p>
                  <p><span>Current Timestamp:</span> {{ cts }}</p>
                </div> -->
                <template v-if="authStore.otp?.message != null">
                <div class="mt-2">
                  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <!-- <strong class="font-bold">Holy smokes!</strong>  -->
                    <span class="block sm:inline">{{ authStore.otp?.message }}</span>
                  </div>
                </div>
                </template>

                <div class="mt-2">
                <template v-if="!authStore.otp?.otp_expired">
                  <button type="submit" class="flex w-full justify-center rounded-md bg-[#1A7D7A] px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-[#0F9C98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Verify</button>
                </template>
                </div>
              </div>
            </form>
            <template v-if="authStore.otp?.otp_expired">
              <button @click="authStore.otpRequest" class="flex w-full justify-center rounded-md bg-[#020808] px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-[#032b2b] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Request OTP</button>
            </template>
          </div>
        </div>
  </div>
</template>

<script>
export default {

  data() {
      return {
        timerEnabled: true,
        timerCount: 10,
        cts: null,
      }
  },
  watch: {

    timerEnabled(value) {
      if (value) {
        setTimeout(() => {
          this.timerCount--;
        }, 1000);
      }
    },

    timerCount: {
      handler(value) {

      if (value > 0 && this.timerEnabled) {
        setTimeout(() => {
          this.timerCount--;
        }, 1000);
      } else {
        console.log("Expired!")
      }
    },
      immediate: true // This ensures the watcher is triggered upon creation
    }
  },
  mounted() {
    this.getTimestampInSeconds();
  },
  methods: {
    play() {
      this.timerEnabled = true;
    },
    pause() {
      this.timerEnabled = false;
    },
    getTimestampInSeconds () {
      console.log("getTimestampInSeconds();")
      this.cts = Math.floor(Date.now() / 1000);
    }
  }
}
</script>