<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
console.log("My Account User:", authStore.user);
</script>

<template>
    <h1>My Account</h1>s
</template>