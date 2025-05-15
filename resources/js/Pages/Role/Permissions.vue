<script setup>
import { watch } from "vue";


const props = defineProps({
    show: Boolean,
    title: String,
    role: Object,
});

const emit = defineEmits(["close"]);

watch(
    () => props.role, 
    (newRole) => {
        if(newRole) {
            //
        }
    }, 
    { immediate: true }
);

const closeModal = () => {
    emit("close");
}

</script>

<template>

    <Dialog
        :visible="show" modal
        :header="'Permission ' + props.role?.name"
        :style="{ width: '50rem' }"
    >
        <div class="grid grid-cols-2 sm:grid-cols-3">
            <div
                v-for="(permission, index) in props.role?.permissions"
                :key="index"
                class="flex justify-between w-full px-4 py-2"
            >
                {{ ++index + ". " + permission.name }}
            </div>
        </div>
    </Dialog>

</template>