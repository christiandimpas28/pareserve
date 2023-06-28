<script setup>
import { onMounted, ref } from 'vue';
// import { useHelperStore } from '../stores/index';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import Navigation from '../components/home/Navigation.vue';
import SearchCategory from '../components/home/sections/searchCategoty.vue';

    // import Hero from '../components/home/sections/HeroFrom.vue'

    // const datepickerEl = document.getElementById('datepickerId');
    // new Datepicker(datepickerEl, {
    //     // options
    // }); 
    
    // const helperStore = useHelperStore();
    // const u = useAuthStore();
    // const m = u.testMechant();
    // console.log("Home", helperStore.homeIndexView);

const router = useRouter();
const authStore = useAuthStore();

const showMain = ref(true);
const gridRows = ref([0,3,6,9,12,15]);
const topDestinations = ref([]);

const displayCollection = (idx) => {
    const new_coll = [];
    new_coll.length=0;

    for(let i=idx; i<(idx+3); i++){
        new_coll.push(topDestinations.value[i])
    }
    return new_coll;
}


onMounted(() => {
    authStore.getUser('Home');
    // console.log("HOME: ", router.currentRoute.value)
    if (router.currentRoute.value.name === 'Home') showMain.value=true;

    topDestinations.value = [
        { img: 'http://localhost:8000/uploads/destinations/enchanted.jpg', name: 'Hinatuan Enchanted River', query: 'enchanted' },
        { img: 'http://localhost:8000/uploads/destinations/bukidnon.jpg', name: 'Bukidnon', query: 'Bukidnon' },
        { img: 'http://localhost:8000/uploads/destinations/cotabato.jpg', name: 'Cotabato', query: 'Cotabato' },
        { img: 'http://localhost:8000/uploads/destinations/parola.jpg', name: 'Parola', query: 'Parola' },
        { img: 'http://localhost:8000/uploads/destinations/peagle.jpg', name: 'Philippine Eagle', query: 'Eagle' },
        { img: 'http://localhost:8000/uploads/destinations/samal.jpg', name: 'Samal Island', query: 'samal' },
        { img: 'http://localhost:8000/uploads/destinations/aliwagwag.jpg', name: 'Aliwagwag Falls', query: 'Aliwagwag' },
        { img: 'http://localhost:8000/uploads/destinations/siargao.jpg', name: 'Siargao', query: 'Siargao' },
        { img: 'http://localhost:8000/uploads/destinations/tawitawi.jpg', name: 'Tawi-Tawi', query: 'Tawi' },
        { img: 'http://localhost:8000/uploads/destinations/eden.jpg', name: 'Eden', query: 'Eden' },
        { img: 'http://localhost:8000/uploads/destinations/cdo.jpeg', name: 'Cagayan de Oro', query: 'Cagayan de Oro' },
        { img: 'http://localhost:8000/uploads/destinations/gensan.jpg', name: 'Gensan', query: 'Gensan' },
        { img: 'http://localhost:8000/uploads/destinations/camiguin.jpg', name: 'Camiguin', query: 'Camiguin' },
        { img: 'http://localhost:8000/uploads/destinations/kapatagan.jpg', name: 'Kapatagan', query: 'Kapatagan' },
        { img: 'http://localhost:8000/uploads/destinations/Sarangani.jpg', name: 'Dahilayan', query: 'Dahilayan' },
        { img: 'http://localhost:8000/uploads/destinations/davaon.jpg', name: 'Davao', query: 'Davao' },
        { img: 'http://localhost:8000/uploads/destinations/cdon.jpg', name: 'Cdo', query: 'Cdo' },
    ];

    showMain.value= router.currentRoute.value.name==='Home';
});

const search = (param) => {
    console.log("Search query: ", param);

    const val = (param.query !== undefined) ? encodeURIComponent(param.query.toLowerCase()):'';  
    const start = ''; //encodeURIComponent(param.start)  
    const end = ''; // encodeURIComponent(param.end)  
    router.push({ name: 'ListingsSearch', query: { query: val, start: start, end: end } })
}

    // router.push({ name: 'Listings' });

router.beforeEach((to, from) => { 
    console.log("from", from);
    console.log("to", to);
    showMain.value= to.name==='Home';
});


</script>

<template>
    <Navigation></Navigation>
    <router-view></router-view>
    <div class="pb-12 pt-10 md:pt-16" v-show="showMain">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex-1">
                <div class="mb-4 mt-8 items-center justify-center">
                    <h1 class="font-semibold text-3xl">Stay at our top unique properties</h1>
                    <p>From hotels and villas to resorts and vacation houses, we've got it all</p>
                    <div>
                        <SearchCategory></SearchCategory>
                    </div>
                </div>


                <div class="mb-4 mt-8 items-center justify-center">
                    <div class="mb-4">
                        <h1 class="font-semibold text-3xl">Trending destinations</h1>
                        <p>Most popular choices for travellers from the Philippines</p>
                    </div>
                    <div class="mb-4">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="grid gap-4" v-for="item in gridRows">
                                <div v-for="img in displayCollection(item)" @click="search(img)" class="cursor-pointer relative">
                                    <img class="h-auto max-w-full rounded-lg" :src="img?.img" :alt="img?.name" />
                                    <h3 class="absolute text-lg text-gray-100 top-5 left-0 drop-shadow-md bg-gray-800 p-2">{{ img?.name }}</h3>
                                </div>
                                
                            </div>
                            
                        </div>

                    </div>
                </div>

                
                

            </div>
        </div>
    </div>

</template>