<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import RegionService from "@/service/Geo/RegionService.js";

import CreateModal from "@/Pages/Geo/Region/Create.vue";
import EditModal from "@/Pages/Geo/Region/Edit.vue";
import DeleteModal from "@/Pages/Geo/Region/Delete.vue";
import AssignCitiesModal from "./AssignCitiesModal.vue";

import { usePermissions } from '@/composables/usePermissions';
const { has } = usePermissions();

import pkg from "lodash";
const { _, debounce, pickBy } = pkg;

const isLoading = ref(false);

const props = defineProps({
    title: String,
    filters: Object,
    cities: Array,
    countries: Array,
});

const regions = ref(null);

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        page: 1, // 👉 hozzáadva
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
        citiesOpen: false,
    },
    region: null
});

const selectedRegion = ref(null);
const allCountries = ref(props.countries);

const onPageChange = (event) => {
    data.params.page = event.page + 1;
    fetchItems();
};

const fetchItems = async () => {

    isLoading.value = true;

    const params = pickBy({
        page: data.params.page ?? 1,
        search: data.params.search,
        field: data.params.field,
        order: data.params.order,
    });

    try {
        const response = await RegionService.getRegions(params);

        regions.value = response.data;
    } catch(error) {
        console.error("Hiba az területek lekérdezésekor", error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchItems();
});

watch(
    () => [data.params.search, data.params.field, data.params.order], // 🧠 kizárjuk a page-et
    debounce(() => {
        data.params.page = 1; // új keresés = első oldal
        fetchItems();
    }, 300)
);

const clearFilter = () => {
    console.log('Clear Filters');
};

const openRegionModal = (region) => {
    data.region = region;
    data.editOpen = true;
}

</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />

        <div class="card">
        <!-- CREATE MODAL -->
        <CreateModal 
            :show="data.createOpen"
            :title="props.title"
            @close="data.createOpen = false"
            @saved="fetchItems"
        />

        <!-- EDIT MODAL -->
        <EditModal 
            :show="data.editOpen" 
            :title="props.title" 
            @close="data.editOpen = false" 
            @saved="fetchItems"
        />

        <!-- DELETE MODAL -->
        <DeleteModal 
            :show="data.deleteOpen" 
            :title="props.title" 
            @close="data.deleteOpen = false" 
            @saved="fetchItems"
        />

        <!-- CITIES MODAL -->
        <AssignCitiesModal
            :show="data.citiesOpen" 
            :title="props.title" 
            @close="data.citiesOpen = false" 
            @saved="fetchItems"
        />

        <!-- CREATE GOMB -->
        <Button 
            :show="has('create region')" 
            icon="pi pi-plus" 
            label="Create" 
            class="mr-2"
            @click="data.createOpen = true" />

        <!-- REFRESH GOMB -->
        <Button 
            :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" 
            @click="fetchItems"
        />


        </div>

    </AuthLayout>
</template>