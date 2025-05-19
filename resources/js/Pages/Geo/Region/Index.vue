<script setup>
import { onMounted, reactive } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import RegionService from "@/service/Geo/RegionService.js";

import CreateModal from "@/Pages/Geo/Region/Create.vue";
import EditModal from "@/Pages/Geo/Region/Edit.vue";
import DeleteModal from "@/Pages/Geo/Region/Delete.vue";
import AssignCitiesModal from "@/Pages/Geo/Region/AssignCitiesModal.vue";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
    cities: Array,
    countries: Array,
});

const fetchRegions = async (params) => {
    const response = await RegionService.getRegions(params);
    return response.data;
};

const {
    data: regions,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchRegions);

const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    citiesOpen: false,
    region: null
});

const openRegionModal = (region) => {
    data.region = region;
    data.editOpen = true;
};

onMounted(fetchData);
</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />
        <div class="card">
            <CreateModal :show="data.createOpen" :title="props.title" @close="data.createOpen = false" @saved="fetchData" />
            <EditModal :show="data.editOpen" :title="props.title" @close="data.editOpen = false" @saved="fetchData" />
            <DeleteModal :show="data.deleteOpen" :title="props.title" @close="data.deleteOpen = false" @saved="fetchData" />
            <AssignCitiesModal :show="data.citiesOpen" :title="props.title" @close="data.citiesOpen = false" @saved="fetchData" />

            <Button v-if="has('create region')" icon="pi pi-plus" label="Create" class="mr-2" @click="data.createOpen = true" />
            <Button :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" @click="fetchData" />

            <DataTable
                v-if="regions"
                :value="regions.data"
                :rows="regions.per_page"
                :totalRecords="regions.total"
                :first="(regions.current_page - 1) * regions.per_page"
                :loading="isLoading"
                lazy paginator dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >
                <template #header>
                    <div class="flex justify-between">
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearSearch" />
                        <div class="font-semibold text-xl mb-1">regions_title</div>
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
                <Column field="country_name" header="Country" />
                <Column field="cities_count" header="Cities" />
                <Column :exportable="false" 
                    style="width: 150px; min-width: 150px; max-width: 150px;">
                    <template #body="slotProps">
                        <Button v-if="has('update region')" icon="pi pi-pencil" outlined rounded class="mr-2"
                            @click="() => { data.editOpen = true; data.region = slotProps.data; }" />
                        <Button v-if="has('delete region')" icon="pi pi-trash" outlined rounded severity="danger" class="mr-2"
                            @click="() => { data.deleteOpen = true; data.region = slotProps.data; }" />
                        <Button v-if="has('update region')" icon="pi pi-map-marker" outlined rounded severity="success"
                            @click="() => { data.citiesOpen = true; data.region = slotProps.data; }" />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AuthLayout>
</template>
