<script setup>
  import { onMounted } from 'vue';
  import { useAuthStore } from './stores/auth';
  import { initFlowbite } from 'flowbite';
  

  onMounted(async () => {
    initFlowbite();
    const authStore = useAuthStore();
    // await authStore.getUser('App Vue');
    // console.log("APP VUE");
  });

</script>

<template>
  <main>
    <RouterView />
  </main>
</template>
