<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
authStore.authErrors = [];
const form = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    user_type: "Partner",
});

</script>

<template>
<section class="text-gray-600 body-font bg-gray-800 dark:bg-slate-900">
    <div class="container mx-auto flex md:px-24 md:py-10 md:flex-row flex-col items-center">
        <div
            class="lg:flex-grow mt-5 md:mt-0   md:w-1.5/2 lg:pr-24 md:pr-16 flex flex-col md:items-start md:text-left mb-16 md:mb-0 items-center text-center">
            <h1
                class="text-2xl font-extrabold leading-9 tracking-tight mb-3 text-gray-900 dark:text-gray-100 sm:text-4xl sm:leading-10 md:text-5xl md:leading-normal">
                Ready to get started Mam,Ser?
            </h1>
            <p class="mb-8 md:pl-0  pl-2 pr-2 leading-relaxed dark:text-gray-300">
                Let's Goooo! Join us today and unlock access to high-value guests. <br/> Signing up is free, fast, and easy.
            </p>
            <div class="flex justify-center">
                <a href="#"
                    class="inline-flex text-white bg-emerald-600 border-0 py-2 px-6 focus:outline-none hover:bg-emerald-600 rounded text-lg" v-on:click.prevent="scrollToElement('register')">Start 
                    Here
                </a>
                <a href="#"
                    class="ml-4 inline-flex text-gray-700 bg-gray-100 border-0 py-2 px-6 focus:outline-none hover:bg-gray-200 rounded text-lg" v-on:click.prevent="scrollToElement('learnmore')">Learn more <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <div class="lg:max-w-lg lg:w-full mb-5 md:mb-0 md:w-1/2 w-3/6">
            <img class="object-cover object-center rounded" alt="hero" src1="https://www.svgrepo.com/show/490900/hot-air-balloon.svg" src="https://smartaction.ai/wp-content/uploads/2020/02/travel_and_hospitality_hero.svg">
        </div>
    </div>
</section>

<section class="mx-auto mt-10 w-full px-4 sm:px-6 lg:mt-20 lg:max-w-5xl lg:px-8 xl:max-w-container" ref="learnmore">
    <div class="mx-auto max-w-[60rem] text-center">
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">Bring the right guests within reach</h2>
        <p class="mt-4 text-lg text-slate-700">Connect with millions of people whose purpose, taste and budget make your property the perfect place to stay.</p>
    </div>
</section>

<section class="mx-auto mt-10 w-full px-4 sm:px-6 lg:mt-20 lg:max-w-5xl lg:px-8 xl:max-w-container">
    <div class="relative z-20 sm:z-auto my-8">
        <div class="mx-auto max-w-container px-4 pb-16 sm:px-6 lg:px-8">
            
            <div class="relative mx-auto grid max-w-[40rem] grid-cols-1 gap-8 lg:max-w-none lg:grid-cols-3">
                <div class="flex">
                    <div class="ml-6">
                        <h2 class="text-3xl font-bold tracking-tight text-slate-900">Why PaReserve?</h2>
                        <p>Benefits of working with us</p>
                    </div>
                </div>
                <div class="flex">
                    <div class="ml-6">
                        <h2 class="text-sm font-semibold leading-6 text-slate-900">List any type of property</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-700">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                    </div>
                </div>
                <div class="flex">
                    <div class="ml-6">
                        <h2 class="text-sm font-semibold leading-6 text-slate-900">Get bookings fast</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-700">Our statistics show that the majority of new listings receive a booking within the first 3 months of joining our community.</p>
                    </div>
                </div>
                <div class="flex">
                    <div class="ml-6">
                        <h2 class="text-sm font-semibold leading-6 text-slate-900">Step-by-step guidance</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-700">You’ll learn how our platform works, best practices, and things to watch out for.</p>
                    </div>
                </div>
                <div class="flex">
                    <div class="ml-6">
                        <h2 class="text-sm font-semibold leading-6 text-slate-900">Support</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-700">Our experts around the world provide on-the-ground insight and expertise.</p>
                    </div>
                </div>
                <div class="flex">
                    <div class="ml-6">
                        <h2 class="text-sm font-semibold leading-6 text-slate-900">Endorsement</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-700">Our endorsement puts you in the company of some of the world's top travel and leisure brands.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
        
<div class="container my-24 px-6 mx-auto">

<!-- Section: Design Block -->
<section class="mb-32 text-gray-800">
  <div class="flex flex-wrap">
    <div class="grow-0 shrink-0 basis-auto mb-6 md:mb-0 w-full md:w-6/12 px-3 lg:px-6 mt-32">
      
      <h2 class="text-3xl font-bold mb-6">Sign up and start welcoming guests today!</h2>
      <p class="text-gray-500 mb-6">
        Lorem ipsum dolor sit amet consectetur adipisicing elit.
        Laudantium, modi accusantium ipsum corporis quia asperiores
        dolorem nisi corrupti eveniet dolores ad maiores repellendus enim
        autem omnis fugiat perspiciatis? Ad, veritatis.
      </p>
      
    </div>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm" ref="register">
        <h3 class="text-2xl font-bold mb-6">Welcome to PaReserve</h3>
        <p>Create an account to list and manage your property.</p>
        <form class="space-y-6 mt-4" @submit.prevent="authStore.handleRegisterPartner(form)">
            <div>
                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                <div class="mt-2">
                    <input id="name" v-model="form.name" name="name" placeholder="Company or Business name " type="text" autocomplete="name" class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    <div v-if="authStore.errors.name">
                        <span class="text-red-400 text-sm m-2 p-2">{{ authStore.errors.name[0]  }}</span>
                    </div>
                </div>
            </div>


            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Business Email address</label>
                <div class="mt-2">
                    <input id="email" v-model="form.email" name="email" type="email" autocomplete="email" class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    <div v-if="authStore.errors.email">
                        <span class="text-red-400 text-sm m-2 p-2">{{ authStore.errors.email[0]  }}</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                </div>
                <div class="mt-2">
                    <input id="password" v-model="form.password" name="password" type="password" autocomplete="current-password" class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    <div v-if="authStore.errors.password">
                        <span class="text-red-400 text-sm m-2 p-2">{{ authStore.errors.password[0]  }}</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password</label>
                </div>
                <div class="mt-2">
                    <input id="confirm_password" v-model="form.password_confirmation" name="password" type="password" autocomplete="current-password" class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    <div v-if="authStore.errors.confirm_password">
                        <span class="text-red-400 text-sm m-2 p-2">{{ authStore.errors.confirm_password[0]  }}</span>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="flex w-full justify-center rounded-md bg-[#1A7D7A] px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-[#0F9C98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Create Account</button>
            </div>
        </form>

        <p class="mt-10 text-center text-sm text-gray-500">
        Already registered?
        {{ ' ' }}
        <router-link :to="{name: 'Login'}" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Sign-in</router-link>
        </p>
    </div>

    
  </div>
</section>
<!-- Section: Design Block -->

</div>
</template>

<script>
export default {
    name: "ListingSetup",
    methods: {
        scrollToElement (ref_el) {
            const el = this.$refs[ref_el];
            console.log(ref_el);
            if (el) { el.scrollIntoView({ behavior: "smooth" }); }
        }
    }
}

</script>