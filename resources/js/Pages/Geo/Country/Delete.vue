<script setup>
import { defineProps, defineEmits } from "vue";
import CountryService from "@/service/Geo/CountryService.js";

const props = defineProps({
    country: Object,
    title: String,
    show: Boolean
});

const emit = defineEmits(['close', 'deleted']);

// Törlés művelet
const deleteCountry = async () => {
    try {
        await CountryService.deleteCountry(props.country.id);
        emit('deleted', props.country.id);
        closeModal();
    } catch (e) {
        console.error("Törlés sikertelen", e);
    }
};

// Modal bezárása
const closeModal = () => {
    emit('close');
};

</script>

<template>
    <Dialog
        :visible="show" modal
        header="Cég törlése"
        @hide="closeModal"
        :style="{ width: '30vw' }"
    >
        <div class="text-center my-5 text-lg">
            Biztosan törölni szeretnéd a(z) <strong>{{ country.name }}</strong> országot?
        </div>

        <template #footer>
            <Button
                label="Mégse"
                icon="pi pi-times"
                @click="closeModal"
                severity="secondary"
                class="p-button-text"
            />
            <Button
                label="Törlés"
                icon="pi pi-trash"
                @click="deleteCountry"
                severity="danger"
            />
        </template>
    </Dialog>
</template>
