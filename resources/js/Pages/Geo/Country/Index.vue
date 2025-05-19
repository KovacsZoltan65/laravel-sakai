<script setup>
import { onMounted, reactive } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import CountryService from "@/service/Geo/CountryService.js";

import CreateModal from "@/Pages/Geo/Country/Create.vue";
import EditModal from "@/Pages/Geo/Country/Edit.vue";
import DeleteModal from "@/Pages/Geo/Country/Delete.vue";
import AssignRegionsModal from "./AssignRegionsModal.vue";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
    regions: Array,
    cities: Array,
});

const fetchCountries = async (params) => {
    const response = await CountryService.getCountries(params);
    return response.data;
};

const {
    data: countries,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchCountries);

const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    regionsOpen: false,
    country: null
});

const openRegionModal = (country) => {
    data.country = country;
    data.regionsOpen = true;
};

onMounted(fetchData);
</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />
        <div class="card">
            <CreateModal :show="data.createOpen" :title="props.title" @close="data.createOpen = false" @saved="fetchData" />
            <EditModal :show="data.editOpen" :country="data.country" :title="props.title" @close="data.editOpen = false" @saved="fetchData" />
            <DeleteModal :show="data.deleteOpen" :country="data.country" :title="props.title" @close="data.deleteOpen = false" @deleted="fetchData" />
            <AssignRegionsModal :show="data.regionsOpen" title="Régiók hozzárendelése" :country="data.country" :regions="props.regions" @close="data.regionsOpen = false" @saved="fetchData" />

            <Button v-if="has('create country')" icon="pi pi-plus" label="Create" class="mr-2" @click="data.createOpen = true" />
            <Button :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" @click="fetchData" />

            <DataTable
                v-if="countries"
                :value="countries.data"
                :rows="countries.per_page"
                :totalRecords="countries.total"
                :first="(countries.current_page - 1) * countries.per_page"
                :loading="isLoading"
                lazy paginator dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >
                <template #header>
                    <div class="flex justify-between">
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearSearch" />
                        <div class="font-semibold text-xl mb-1">countries_title</div>
                        <div class="flex justify-end">
                            <IconField>
                                <InputIcon><i class="pi pi-search" /></InputIcon>
                                <InputText v-model="params.search" placeholder="Keyword Search" />
                            </IconField>
                        </div>
                    </div>
                </template>

                <template #empty>No data found.</template>
                <template #loading>Loading data. Please wait.</template>

                <Column field="id" header="#" />
                <Column field="name" header="Name" />
                <Column field="regions_count" header="count_regions" />
                <Column field="cities_count" header="count_cities" />
                <Column :exportable="false" 
                    style="width: 150px; min-width: 150px; max-width: 150px;">
                    <template #body="slotProps">
                        <Button v-if="has('update country')" icon="pi pi-pencil" outlined rounded class="mr-2"
                            @click="() => { data.editOpen = true; data.country = slotProps.data; }" />
                        <Button v-if="has('delete country')" icon="pi pi-trash" outlined rounded severity="danger" class="mr-2"
                            @click="() => { data.deleteOpen = true; data.country = slotProps.data; }" />
                        <Button v-if="has('update country')" icon="pi pi-globe" outlined rounded severity="success"
                            @click="() => openRegionModal(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AuthLayout>
</template>
