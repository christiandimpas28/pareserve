<script setup>
    import SideBar from '../components/admin/sections/sidebar.vue'
    console.log("Admin");
</script>

<template>
    <div class="relative fles min-h-screen">
        <SideBar />
        <div class="p-4 sm:ml-64">
            <div class="bg-white shadow p-4">Header</div>
            Admin Dashboard
            <router-view></router-view>
        </div>
    </div>
</template>