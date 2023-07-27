<script setup>
import { toRef, ref, watch } from 'vue';

const props = defineProps({
    guest: { type: Array, default: [] },
    partners: { type: Array, default: [] },
});

</script>
<template>
<div class="p-4">
    <div class="mb-8">
        <div class="grid grid-cols-2 gap-10">
            <div class="relative overflow-x-auto">
                <div class="mb-8">                
                    <h1 class="flex justify-start py-2 font-semibold text-lg">Guest Users</h1>
                    <table class="w-full text-sm text-left text-gray-700 border-gray-600 rounded-lg" v-if="guest.length>0">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Guest Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Date Verified
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Signup Date
                                </th>
                            </tr>
                        </thead>
                        <tbody v-if="guest.length>0">
                            <tr class="bg-white border-b" v-for="item in guest">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap white:text-dark">
                                    {{ item.name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ item.email }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.email_verified_at }}
                                </td>
                                <td class="px-6 py-4 truncate">
                                    {{ item.created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Col 2-->
            <div class="relative overflow-x-auto">
                <h1 class="flex justify-start py-2 font-semibold text-lg">Merchant Users</h1>
                <table class="w-full text-sm text-left text-gray-700 border-gray-600 rounded-lg ">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Merchant Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Date Verified
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Signup Date
                            </th>
                        </tr>
                    </thead>
                    <tbody v-if="partners.length>0">
                        <tr class="bg-white border-b" v-for="item in partners">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap white:text-dark">
                                {{ item.name }}
                            </th>
                            <td class="px-6 py-4">
                                {{ item.email }}
                            </td>
                            <td class="px-6 py-4 truncate">
                                {{ item.email_verified_at }}
                            </td>
                            <td class="px-6 py-4 truncate">
                                {{ item.created_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</template>