<script setup>
import { onMounted, computed, watch, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const router = useRouter();

const userViewState = ref(0);

onMounted(() => {
  console.log("onMounted Nav", authStore.user);
  userViewState.value=1;
});

watch(() => authStore.user, (c, b) => { 
  console.log("Watch User 0: ", b);
  console.log("Watch User 1: ", c);
  if (c !== null && c.id !== undefined) {
    userViewState.value=2;
  } else {
    userViewState.value=1;
  }
});

const userAccount = () => {
  console.log("userAccount Clicked!", authStore.user);
  if (authStore.user == null) return false;

  if (authStore.user.user_type.toUpperCase() === 'CUSTOMER') {
    router.push({name: 'CustomerProfile'});
  }

  if (authStore.user.user_type.toUpperCase() === 'PARTNER') {
    router.push({name: 'Partner'});
  }
}

// const userViewState = computed(() => {
//     // return !(authStore.user==null || authStore.user!=null && authStore.user.user_type.toUpperCase()!='CUSTOMER');
//     return (booking.value !==null && booking.value.booking_status && (booking.value.booking_status.toUpperCase() === 'PENDING' || booking.value.booking_status.toUpperCase() === 'CANCELLED'));
// });

console.log("NAVI");
    
</script>

<template>
<nav class="bg-white border-gray-200">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
    <router-link :to="{name: 'Listings'}" class="flex items-center">
      <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
 width="50px" height="50px" viewBox="0 0 300.000000 300.000000"
 preserveAspectRatio="xMidYMid meet">

<g transform="translate(0.000000,300.000000) scale(0.100000,-0.100000)"
fill="#000000" stroke="none">
<path d="M1380 2984 c-14 -2 -63 -9 -110 -15 -47 -6 -137 -27 -200 -46 -537
-161 -933 -608 -1037 -1168 -23 -124 -23 -387 1 -515 112 -613 569 -1074 1196
-1207 120 -25 418 -24 545 1 175 35 384 117 519 204 176 113 354 291 468 468
86 135 169 344 204 519 25 127 26 425 1 545 -101 477 -386 854 -806 1065 -98
49 -284 110 -396 130 -88 15 -335 28 -385 19z m-200 -895 c78 -12 172 -54 223
-98 20 -18 51 -62 69 -99 29 -59 33 -77 36 -168 5 -128 -18 -215 -78 -295 -44
-59 -130 -122 -188 -139 -72 -22 -197 -24 -273 -5 -39 9 -74 13 -78 8 -5 -4
-20 -44 -36 -88 -36 -104 -99 -240 -130 -281 -38 -50 -88 -76 -145 -76 -93 1
-170 71 -170 154 0 51 18 70 65 66 37 -3 40 -5 46 -40 3 -20 11 -44 19 -52 17
-21 73 -20 92 2 21 23 63 109 87 174 11 29 52 143 92 253 87 241 135 349 178
394 29 31 35 33 77 28 65 -7 70 -22 25 -73 -21 -24 -52 -69 -68 -101 -40 -75
-96 -234 -86 -243 33 -30 204 -39 275 -14 142 48 226 228 184 394 -21 83 -57
127 -131 165 -49 24 -73 30 -150 33 -146 7 -265 -37 -360 -132 -72 -72 -108
-152 -109 -241 -1 -61 3 -78 32 -130 l33 -60 -33 -27 c-18 -16 -38 -28 -45
-28 -23 0 -70 78 -88 144 -53 194 50 403 250 508 127 66 254 88 385 67z m1030
-28 c149 -56 230 -177 230 -341 0 -188 -103 -337 -254 -365 -28 -6 -36 -11
-31 -23 3 -9 22 -72 41 -141 41 -143 55 -171 87 -171 29 0 136 112 207 217 30
45 61 85 69 88 22 9 61 -23 61 -49 0 -30 -80 -152 -157 -239 -117 -130 -169
-155 -248 -117 -71 34 -99 91 -140 281 -22 103 -57 145 -138 164 l-58 13 -10
-26 c-94 -249 -157 -380 -207 -424 -68 -61 -164 -75 -242 -35 -56 28 -81 63
-87 120 -10 87 15 123 76 111 30 -6 31 -9 31 -55 0 -83 56 -113 127 -69 61 37
94 103 208 420 124 344 186 457 261 476 31 8 42 -2 50 -42 5 -25 0 -38 -23
-65 -16 -19 -37 -46 -46 -61 -20 -32 -107 -233 -107 -247 0 -14 93 -31 173
-31 88 0 141 22 186 76 43 53 64 129 59 219 -4 96 -39 155 -118 199 -52 29
-60 31 -165 31 -100 0 -115 -2 -161 -27 -98 -52 -164 -156 -164 -260 0 -21 12
-62 27 -93 l27 -55 -20 -27 c-31 -42 -56 -41 -87 4 -117 170 -40 425 160 527
98 49 278 58 383 17z"/>
</g>
</svg>
    </router-link>
    <div class="flex md:order-2">
      <template v-if="userViewState<2">
        <router-link :to="{name: 'ListingSetup'}" class="text-dark hover:border-2 mr-3 rounded-lg border-2 border-white bg-[#1A7D7A] px-4 py-2 text-center text-sm font-medium text-white hover:border-[#1A7D7A] hover:bg-white hover:text-[#1A7D7A]">List your property</router-link>
      </template>
      <template v-if="userViewState==2">
        <button @click="authStore.handleLogout()"  class="text-dark hover:border-2 mr-3 rounded-lg border-2 border-white bg-[#1A7D7A] px-4 py-2 text-center text-sm font-medium text-white hover:border-[#1A7D7A] hover:bg-white hover:text-[#1A7D7A]">Logout</button>
      </template>
      
      <button data-collapse-toggle="navbar-dropdown" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-cta" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
      </button>
    </div>
    <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-dropdown">
      <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-100 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:white:bg-gray-900 dark:border-gray-700">
        <template v-if="userViewState==1">
        <li>
          <router-link :to="{name: 'Login'}" class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-[#1A7D7A] md:p-0 md:dark:hover:text-[#0F9C98] dark:text-dark dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Login</router-link>
        </li>
        <li>
          <router-link :to="{name: 'Register'}" class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-[#1A7D7A] md:p-0 md:dark:hover:text-[#0F9C98] dark:text-dark dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Register</router-link>
        </li>
        </template>
        <li v-if="userViewState==2">
          <a @click.prevent="userAccount()" class="cursor-pointer">{{ authStore.user?.name }}</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

</template>