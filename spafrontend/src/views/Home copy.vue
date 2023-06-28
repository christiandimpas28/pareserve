<script setup>
    import { useHelperStore } from '../stores/index';
    import { useAuthStore } from '../stores/auth';
    import { useRouter } from 'vue-router';
    import Navigation from '../components/home/Navigation.vue';
    import Hero from '../components/home/sections/HeroFrom.vue'
    import Datepicker from 'flowbite-datepicker/Datepicker';

    // const datepickerEl = document.getElementById('datepickerId');
    // new Datepicker(datepickerEl, {
    //     // options
    // }); 
    
    const helperStore = useHelperStore();
    const u = useAuthStore();
    const m = u.testMechant();
    console.log("Home", helperStore.homeIndexView);

    const router = useRouter()
    router.push({ name: 'Listings' });
</script>

<template>
    <Navigation />
    <Hero />
    <button @click="helperStore.testHide">Toggle View</button>
    <router-view></router-view>
    <section v-if="helperStore.homeIndexView">
        Home Landing Page
    </section>
    
</template>