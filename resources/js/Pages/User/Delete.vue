<script setup>
import UserService from "@/service/UserService.js";

const props = defineProps({
    company: Object,
    title: String,
    show: Boolean
});

const emit = defineEmits(['close', 'deleted']);

const deleteUser = async () => {
    try {
        await UserService.deleteUser(props.user.id);
        emit('deleted', props.user.id);
        closeModal();
    } catch(e) {
        console.error("Törlés sikertelen", e);
    }
};

const closeModal = () => {
    emit('close');
};

</script>

<template>
    <div>INDEX</div>
</template>