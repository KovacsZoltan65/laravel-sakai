<script setup>
import { onMounted, reactive, watch } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import CreateModal from "@/Pages/Company/Create.vue";
import EditModal from "@/Pages/Company/Edit.vue";
import DeleteModal from "@/Pages/Company/Delete.vue";

import CompanyService from '@/service/CompanyService.js';

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object
});

// 👇 API hívás definíció
const fetchCompanies = async (params) => {
    const response = await CompanyService.getCompanies(params);
    return response.data;
};

// 👇 Hook használata
const {
    data: companies,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchCompanies);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    company: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('companies.index', newParams), { preserveScroll: true, preserveState: true });
}, { deep: true });

onMounted(fetchData);
</script>

<template>
    <AuthLayout>

        <Head :title="props.title" />

        <div class="card">
            <CreateModal :show="data.createOpen" :title="props.title" @close="data.createOpen = false"
                @saved="fetchData" />
            <EditModal :show="data.editOpen" :company="data.company" :title="props.title" @close="data.editOpen = false"
                @saved="fetchData" />
            <DeleteModal :show="data.deleteOpen" :company="data.company" :title="props.title"
                @close="data.deleteOpen = false" @deleted="fetchData" />

            <Button v-if="has('create company')" icon="pi pi-plus" label="Create" @click="data.createOpen = true"
                class="mr-2" />

            <Button @click="fetchData" :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" />

            <DataTable v-if="companies" :value="companies.data" :rows="companies.per_page"
                :totalRecords="companies.total" :first="(companies.current_page - 1) * companies.per_page"
                :loading="isLoading" lazy paginator dataKey="id" @page="onPageChange" tableStyle="min-width: 50rem">
                <template #header>
                    <div class="flex justify-between">
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearSearch" />

                        <div class="font-semibold text-xl mb-1">
                            companies_title
                        </div>

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

                <Column field="name" header="Name" />
                <Column field="email" header="Email" />
                <Column field="address" header="Address" />
                <Column field="phone" header="Phone" />
                <Column field="entities_count" header="Entities" />
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-if="has('update company')" icon="pi pi-pencil" outlined rounded class="mr-2" @click="() => {
                            data.editOpen = true;
                            data.company = slotProps.data;
                        }" />
                        <Button v-if="has('delete company')" icon="pi pi-trash" outlined rounded severity="danger"
                            @click="() => {
                                data.deleteOpen = true;
                                data.company = slotProps.data;
                            }" />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AuthLayout>
</template>
