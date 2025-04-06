<script setup>
import { onMounted, ref, watch } from 'vue';

import CountryService from "@/service/Geo/CountryService.js";

const props = defineProps({
    show: Boolean,
    title: String,
    country: Object,
    regions: Array,
});

const emit = defineEmits(["close", "saved"]);

//const source = ref(null); // nem hozzárendelt régiók
//const target = ref(null); // hozzárendelt régiók

const sourceItems = ref(null);
const targetItems = ref(null);
const picklistProducts = ref(null);

//const picklistProducts = ref({
//    source: [],
//    target: []
//});

onMounted(() => {

    //ProductService.getProductsSmall().then((data) => {
    //    picklistProducts.value = [data, []];
    //});

});

watch(
    [() => props.show, () => props.country],
    ([visible, country]) => {

        if( visible && country?.regions) {

            const assigned = country.regions;
            const assignedIds = assigned.map(r => r.id);

            picklistProducts.value = [
                props.regions.filter(region => !assignedIds.includes(region.id)),
                assigned
            ];

            /*
            EZ IS MŰKÖDIK!!!!
            picklistProducts.value = [
                props.regions.filter((region) => !props.country.regions.some((r) => r.id === region.id)),
                props.country.regions
            ];
            */
        }
    },
    { immediate: true }
);

const save = async () => {

    isUpdating = true;

    try {
        await CountryService.updateAssignRegions(props.country.id, targetItems.value);
    } catch(e) {
        console.error('Frissítés sikertelen', e);
    } finally {
        isUpdating = false;
    }





    console.log('picklistProducts', picklistProducts.value[1][0]);

    emit('saved', {
        countryId: props.country.id,
        regions: targetItems.value
    });
    closeModal();
};

const closeModal = () => {
    emit('close');
};
</script>

<template>
    <Dialog
        :visible="show" modal
        :style="{ width: '750px' }"
        :header="title"
        :closable="false"
        @hide="closeModal"
    >

    <PickList
        v-model="picklistProducts"
        dataKey="id"
    >
        <template #sourceheader> Elérhető </template>
        <template #targetheader> Kiválasztott </template>
        <template #option="{ option }">
            {{ option.name }}
        </template>
    </PickList>
        <!--<PickList
            v-model="sourceItems"
            v-model:selection="targetItems"
            dataKey="id"
            breakpoint="1400px"
            listStyle="height:342px"
        >
            <template #option="{ option }">
                {{ option.name }}
            </template>

            <template #sourceheader> Available </template>
            <template #targetheader> Selected </template>

        </PickList>-->

        <div class="flex justify-end gap-2 mt-4">
            <Button label="Mégse" severity="secondary" @click="closeModal"/>
            <Button label="Mentés" icon="pi pi-check" @click="save"/>
        </div>

    </Dialog>
</template>
