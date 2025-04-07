<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import CountryService from "@/service/Geo/CountryService.js";

import CreateModal from "@/Pages/Geo/Country/Create.vue";
import EditModal from "@/Pages/Geo/Country/Edit.vue";
import DeleteModal from "@/Pages/Geo/Country/Delete.vue";
import AssignRegionsModal from "./AssignRegionsModal.vue";

import { usePermissions } from '@/composables/usePermissions';
const { has } = usePermissions();

import pkg from "lodash";
const { _, debounce, pickBy } = pkg;

const isLoading = ref(false);

const props = defineProps({
    title: String,
    filters: Object,
    regions: Array,     // Összes régió
    cities: Array,      // Összes város
});

const countries = ref(null);

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        page: 1, // 👉 hozzáadva
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
        regionsOpen: false
    },
    country: null
});

const selectedCountry = ref(null);
const allRegions = ref(props.regions); // props.regions tartalmazza az összes régiót

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
        const response = await CountryService.getCountries(params);
        //console.log('response.data', response.data);
        countries.value = response.data;
    } catch (error) {
        console.error("Hiba az országok lekérdezésekor", error);
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

const openRegionModal = (country) => {
    data.country = country;
    data.regionsOpen = true;
};

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
                @saved="fetchItems" />

            <!-- EDIT MODAL -->
            <EditModal 
                :show="data.editOpen" 
                :country="data.country" 
                :title="props.title" 
                @close="data.editOpen = false"
                @saved="fetchItems" />

            <!-- DELETE MODAL -->
            <DeleteModal 
                :show="data.deleteOpen" 
                :country="data.country" 
                :title="props.title"
                @close="data.deleteOpen = false" 
                @deleted="fetchItems" />

            <!-- REGIONS MODAL -->
            <AssignRegionsModal
                :show="data.regionsOpen"
                title="Régiók hozzárendelése"
                :country="data.country"
                :regions="props.regions"
                @close="data.regionsOpen = false"
                @saved="fetchItems" />

            <!-- CREATE GOMB -->
            <Button :show="has('create country')" icon="pi pi-plus" label="Create" class="mr-2"
                @click="data.createOpen = true" />

            <!-- REFRESH GOMB -->
            <Button :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" @click="fetchItems" />

            <DataTable v-if="countries" :dataKey="'id'" lazy paginator :value="countries.data"
                :rows="countries.per_page" :totalRecords="countries.total"
                :first="(countries.current_page - 1) * countries.per_page" :loading="isLoading" @page="onPageChange"
                tableStyle="min-width: 50rem">

                <template #header>
                    <div class="flex justify-between">

                        <!-- SZŰRÉS TÖRLÉSE -->
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearFilter" />

                        <!-- FELIRAT -->
                        <div class="font-semibold text-xl mb-1">
                            countries_title
                        </div>

                        <!-- KERESÉS-->
                        <div class="flex justify-end">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="data.params.search" placeholder="Keyword Search" />
                            </IconField>
                        </div>
                    </div>
                </template>

                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column field="id" header="#"></Column>
                <Column field="name" header="Name"></Column>

                <Column field="regions_count" header="count_regions" />
                <Column field="cities_count" header="count_cities" />

                <Column :exportable="false" style="min-width: 12rem">

                    <template #body="slotProps">

                        <!-- EDIT MODAL -->
                        <Button 
                            v-show="has('update country')" 
                            icon="pi pi-pencil" 
                            outlined rounded 
                            class="mr-2" 
                            @click="(
                                (data.editOpen = true),
                                (data.country = slotProps.data)
                            )" />

                        <!-- DELETE MODAL -->
                        <Button
                            v-show="has('delete country')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            class="mr-2"
                            @click="(
                                (data.deleteOpen = true),
                                (data.country = slotProps.data)
                            )" />

                        <!-- REGIONS MODAL -->
                        <Button
                            v-show="has('update country')"
                            icon="pi pi-globe"
                            outlined rounded
                            severity="success"
                            @click="openRegionModal(slotProps.data)" />

                    </template>
                </Column>

            </DataTable>

        </div>
    </AuthLayout>
</template>
