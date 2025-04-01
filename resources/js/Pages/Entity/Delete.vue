<script setup>
import { defineProps, defineEmits } from "vue";
import EntityService from "@/service/EntityService";

const props = defineProps({
    entity: Object,
    title: String,
    show: Boolean
});

const emit = defineEmits(['close', 'deleted']);

// Törlés művelet
const deleteEntity = async () => {
    try {
        await EntityService.deleteEntity(props.entity.id);
        emit('deleted', props.entity.id);
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
            Biztosan törölni szeretnéd a(z) <strong>{{ entity.name }}</strong> dolgozót?
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
                @click="deleteEntity"
                severity="danger"
            />
        </template>
    </Dialog>
</template>
