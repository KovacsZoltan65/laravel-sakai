<script setup>
//import { defineProps, defineEmits } from "vue"; // ❌ NEM SZABAD
import CompanyService from "@/service/CompanyService";

const props = defineProps({
    company: Object,
    title: String,
    show: Boolean
});

const emit = defineEmits(['close', 'deleted']);

// Törlés művelet
const deleteCompany = async () => {
    try {
        await CompanyService.deleteCompany(props.company.id);
        emit('deleted', props.company.id);
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
            Biztosan törölni szeretnéd a(z) <strong>{{ company.name }}</strong> céget?
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
                @click="deleteCompany"
                severity="danger"
            />
        </template>
    </Dialog>
</template>
