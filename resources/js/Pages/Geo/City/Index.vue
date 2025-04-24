<script setup>
import { onMounted, reactive } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
import CityService from "@/service/Geo/CityService.js";

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
    regions: Array,
    countries: Array,
});

// 👇 API hívás definíció
const fetchCities = async (params) => {
    const response = await CityService.getCities(params);
    return response.data;
};

// 👇 Hook használata
const {
    data: cities,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchCities);

// 👇 Modálvezérlés – egyelőre nincs modal, csak előkészítés
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    city: null
});

onMounted(fetchData);
</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />

        <div class="card">
            <Button @click="fetchData" :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" />

            <DataTable
                v-if="cities"
                :value="cities.data"
                :rows="cities.per_page"
                :totalRecords="cities.total"
                :first="(cities.current_page - 1) * cities.per_page"
                :loading="isLoading"
                lazy paginator dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >
                <template #header>
                    <div class="flex justify-between">
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearSearch" />
                        <div class="font-semibold text-xl mb-1">cities_title</div>
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
                <Column field="region_name" header="Region" />
                <Column field="country_name" header="Country" />
                <Column field="created_at" header="Created" />
                <Column field="updated_at" header="Updated" />
            </DataTable>
        </div>
    </AuthLayout>
</template>
