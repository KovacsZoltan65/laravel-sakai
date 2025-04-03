<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    country: Number,
    title: String,
    show: Boolean,
    allRegions: Array, // az összes régió
    assignedRegions: Array // az adott country-hoz tartozó régiók
});

const emit = defineEmits(["close", "saved"]);

const regions = ref({
    source: [], // elérhető régiók
    target: []  // hozzárendelt régiók
})

watch(
    () => props.show,
    (visible) => {
        if (visible && props.country) {
            const assignedIds = props.country.regions.map(r => r.id);
            regions.value.source = props.allRegions.filter(r => !assignedIds.includes(r.id));
            regions.value.target = [...props.country.regions];
        }
    },
    { immediate: true }
);

const save = () => {
    emit('saved', {
        countryId: props.country.id,
        regions: regions.value.target
    });
    closeModal();
};

const closeModal = () => {
    emit('close');
};
</script>

<template>
    <Dialog :visible="show" modal :style="{ width: '750px' }" :header="title" @hide="closeModal">

        <PickList v-model="regions" dataKey="id" :sourceHeader="'Elérhető régiók'" :targetHeader="'Hozzárendelt régiók'"
            :metaKeySelection="false">
            <template #item="{ item }">
                <div>{{ item.name }}</div>
            </template>
        </PickList>

        <div class="flex justify-end gap-2 mt-4">
            <Button label="Mégse" severity="secondary" @click="closeModal" />
            <Button label="Mentés" icon="pi pi-check" @click="save" />
        </div>

    </Dialog>
</template>
