<script setup>
const props = defineProps({
  form: { 
    type: Object, 
    default: {
        id: 0,
        product_id: 0,
        name: null,
        value: null,
        highlight: 0,
        important: 0,
    } 
  },
});

const emit = defineEmits(['submit']);

const submit = () => {
    // console.log("props.form", props.form);
    emit('save-data', { data: props.form,});
    // this.$emit('save-data', fromData);
}

const clear = () => {
    props.form.id = 0;
    props.form.name = null;
    props.form.value = null;
}

</script>
<template>
<div>
    <form @submit.prevent="submit()" method="POST">
        <input type="hidden" name="id" id="id" v-model="form.product_id" />
        <div class="mb-4">
            <label for="name" class="mb-3 block text-base font-medium text-[#07074D]">Title</label>
            <input type="text" v-model="form.name" name="name" id="name" required
            placeholder="Feature title" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
            />
        </div>
        <div class="mb-4">
            <label for="value" class="mb-3 block text-base font-medium text-[#07074D]">Feature value</label>
            <input type="text" v-model="form.value" name="value" id="value" required
            placeholder="Feature value" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-3 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" 
            />
        </div>
        <div class="mb-4 flex">
            <div class="w-full xs:w-1/2"></div>
            <div class="w-full xs:w-1/2"></div>
        </div>
        <div class="mb-4">
            <button class="text-white mr-4 bg-cyan-700 hover:bg-cyan-800 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                Add
            </button>
            <button @click.prevent="clear()" class="text-white mr-4 focus:ring-4 focus:outline-none focus:ring-cyan-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                Clear
            </button>
        </div>
    </form>
</div>

</template>