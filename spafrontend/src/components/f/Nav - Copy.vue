<script setup>
import { useAuthStore } from "../../stores/auth"

const authStore = useAuthStore();
</script>

<template>

<nav class="bg-white border-gray-200">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
    <router-link :to="{name: 'Home'}" class="flex items-center">
        <svg class="h-20 mr-3" width="40" >
          <g id="SvgjsG6528" featurekey="monogramFeature-0" fill="#000">
            <path d="M14.88 60 c-9.66 0 -14.88 -1.2 -14.88 -12.54 l0 -20.22 c0 -5.4 3.84 -9.24 9.3 -9.24 c6.42 0 17.4 -0.78 20.58 8.04 c0.96 2.7 0.84 5.94 0.18 8.64 c5.46 1.98 7.62 5.82 7.62 13.2 c0 5.16 -2.52 8.7 -7.5 10.56 c-4.62 1.74 -10.32 1.56 -15.3 1.56 z M9.3 22.14 c-2.4 0 -5.16 1.32 -5.16 5.1 l0 20.04 c0 8.04 2.1 8.58 10.74 8.58 c9.9 0 18.66 -0.06 18.66 -7.98 c0 -5.64 -1.2 -7.86 -4.86 -9.3 c-1.74 4.14 -5.22 8.4 -9.84 8.4 c-5.34 0 -8.46 -5.64 -5.7 -9.96 c1.74 -2.58 5.94 -4.98 12.96 -3.54 c0.48 -2.46 0.42 -4.44 -0.12 -6.06 c-2.04 -5.7 -10.08 -5.28 -16.68 -5.28 z M16.62 39.3 c-1.14 1.8 0.66 4.2 3.18 3.42 c1.74 -0.48 3.72 -2.88 4.86 -5.28 c-2.88 -0.48 -6.6 -0.24 -8.04 1.86 z"></path>
          </g>
        </svg>
        <!-- <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Bukeng</span> -->
    </router-link>
    <div class="flex md:order-2">
      <router-link :to="{name: 'ListingSetup'}" class="text-dark hover:border-2 mr-3 rounded-lg border-2 border-white bg-[#1A7D7A] px-4 py-2 text-center text-sm font-medium text-white hover:border-[#1A7D7A] hover:bg-white hover:text-[#1A7D7A]">List your property</router-link>
      <button data-collapse-toggle="navbar-dropdown" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-cta" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
      </button>
    </div>
    <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-dropdown">
      <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-100 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:white:bg-gray-900 dark:border-gray-700">
        <li>
          <router-link :to="{name: 'Home'}" class="block py-2 pl-3 pr-4 text-white bg-[#1A7D7A] rounded md:bg-transparent md:text-[#1A7D7A] md:p-0 md:dark:text-[#0F9C98]" aria-current="page">Home</router-link>
        </li>
        <li>
          <router-link :to="{name: 'Login'}" class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-[#1A7D7A] md:p-0 md:dark:hover:text-[#0F9C98] dark:text-dark dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Login</router-link>
        </li>
        <li>
          <router-link :to="{name: 'Register'}" class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-[#1A7D7A] md:p-0 md:dark:hover:text-[#0F9C98] dark:text-dark dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Register</router-link>
        </li>
        <li>
            <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar" class="flex items-center justify-between w-full py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-[#1A7D7A] md:p-0 md:w-auto dark:text-dark md:dark:hover:text-[#1A7D7A] dark:focus:text-dark dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent">
              Dropdown <svg class="w-5 h-5 ml-1" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
            <!-- Dropdown menu -->
            <div id="dropdownNavbar" class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">
                  <li>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Dashboard</a>
                  </li>
                  <li>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Settings</a>
                  </li>
                  <li>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Earnings</a>
                  </li>
                </ul>
                <div class="py-1">
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">Sign out</a>
                </div>
            </div>
        </li>
      </ul>
    </div>
  </div>
</nav>


</template>