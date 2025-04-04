<script setup>
import { onMounted, ref, watch } from 'vue'

const props = defineProps({
    show: Boolean,
    title: String,
    country: Object,
    regions: Array,
});

const emit = defineEmits(["close", "saved"]);

//const source = ref(null); // nem hozzárendelt régiók
//const target = ref(null); // hozzárendelt régiók

const sourceItems = ref([]);
const targetItems = ref([]);

/*
const pickListModel = ref({
  source: [],
  target: []
});
*/

watch(
    [() => props.show, () => props.country],
    ([visible, country]) => {
        if (visible && country?.regions && props.regions) {
            sourceItems.value = props.regions.filter(r =>
                !country.regions.some(cr => cr.id === r.id)
            );
            targetItems.value = country.regions;

            sourceItems.value = [
                {
                    id: '1000',
                    code: 'f230fh0g3',
                    name: 'Bamboo Watch',
                    description: 'Product Description',
                    image: 'bamboo-watch.jpg',
                    price: 65,
                    category: 'Accessories',
                    quantity: 24,
                    inventoryStatus: 'INSTOCK',
                    rating: 5
                }
            ];
            targetItems.value = [
                {
                    id: 1001,
                    code: 'nvklal433',
                    name: 'Black Watch',
                    description: 'Product Description',
                    image: 'black-watch.jpg',
                    price: 72,
                    category: 'Accessories',
                    quantity: 61,
                    inventoryStatus: 'INSTOCK',
                    rating: 4
                }
            ];

            console.log('sourceItems.value', sourceItems.value);
            console.log('targetItems.value', targetItems.value);
        }
    },
    { immediate: true }
);

const save = () => {
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
        @hide="closeModal"
    >

        <PickList
            v-model:source="sourceItems"
            v-model:target="targetItems"
            dataKey="id"
            breakpoint="1400px"
            responsiveLayout="scroll"
            filterBy="name"
            filterMatchMode="contains"
            filterPlaceholder="Keres..."
            :sourceStyle="{ height: '300px' }"
            :targetStyle="{ height: '300px' }"
            sourceHeader="Nem hozzárendelt régiók"
            targetHeader="Hozzárendelt régiók"
            :reorderable="false"
            :showSourceControls="false"
            :showTargetControls="false"
            :showItemNavigator="false"
            :showHeader="false"
            :dragdrop="false"
            :clone="false"
            :metaKeySelection="false"
            :responsive="false"
            :loading="true"
            :loadingIcon="true"
            :filterInPlaceholder="true"
            :filterOutPlaceholder="true"
            :filterMatchModeOptions="[
                { value: 'contains', label: 'Keres' },
                { value: 'startsWith', label: 'Keres' },
                { value: 'endsWith', label: 'Keres' },
            ]"

        >
            <template #option="{ option }">
                {{ option.name }}
            </template>
        </PickList>

        <div class="flex justify-end gap-2 mt-4">
            <Button label="Mégse" severity="secondary" @click="closeModal"/>
            <Button label="Mentés" icon="pi pi-check" @click="save"/>
        </div>

    </Dialog>
</template>
