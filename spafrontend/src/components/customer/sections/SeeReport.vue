<script setup>
import { toRef,ref  } from 'vue';

const props = defineProps({
  form: { 
    type: Object, 
    default: {
        book_id: 0,
        user_id: 0,
        product_id: 0,
        photos: [],
        remarks: null,
        related_to: null,
        other: null,
        settled: 0,
    } 
  },
  header: { type: Object, default: { title: 'What happened?', subTitle: '' } },
});

const settled = (val) => {
    if (val==0) {
        return 'Not yet settled';
    } else { 
        return 'Case Settled';
    }
}

const getFileExtension = (filename) => {
    // get file extension
    return filename.substring(filename.lastIndexOf('.') + 1, filename.length);
}

const isFileImage = (filename) => {
    const imagesExt = Array('gif', 'jpg', 'jpeg', 'tif', 'png');
    const fileExt = getFileExtension(filename);

    const exist = imagesExt.find( (ext) => {
        return ext.toLowerCase() === fileExt.toLowerCase();
    });

    if (exist == undefined) return false;
    return true;
}

const reformatUploads = (photos) => {
    const arr_photos = photos.split(',');
    if (arr_photos.length==0) return [];
    let fu = [];
    arr_photos.forEach( (item, index)=> {
        let item_name = item.replace(/^\s+|\s+$/gm,'');
        fu.push({
            isIamge: isFileImage(item_name),
            filename: item_name,
            url: 'http://localhost:8000/uploads/complains/' + item_name
        })
    });

    return fu;
}

</script>
<template>
    <div class="md-4 mx-auto w-full max-w-[820px] min-w-[600px] bg-white">
        <div class="mb-4 pt-3">
            <p class="mb-4 text-base font-medium text-gray-800">What's the problem related to: <strong>{{ form.related_to }}</strong></p>
            <!-- <p class="mb-4 text-base font-medium text-gray-800">Status: <strong>{{ settled(form.settled) }}</strong></p> -->
        </div>
        <div class="mb-4">
            <div class="p-6 rounded-lg bg-gray-200 mb-6">
                <p>{{ form.remarks }}</p>
            </div>
            <div class="mb-4 grid grid-cols-3 gap-2 w-full">
                <template v-if="form.photos!=null" v-for="img in reformatUploads(form.photos)">
                    <div class="flex" v-if="!img.isIamge">
                        <span class="px-2 py-1 bg-gray-300 rounded-full">{{ img.filename }}</span>
                    </div>
                </template>
            </div>
            <div class="mb-4 grid grid-cols-3 gap-2 w-full">                    
                <template v-if="form.photos!=null" v-for="img in reformatUploads(form.photos)">
                    <div class="flex max-h-22" v-if="img.isIamge">
                        <img :src="img.url" :alt="img.name" class="rounded-md object-cover" />
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>