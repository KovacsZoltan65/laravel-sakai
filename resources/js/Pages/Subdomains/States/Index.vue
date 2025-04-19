<script setup>
import { onMounted, reactive } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";

import StateService from '@/service/Subdomains/SubdomainStateService.js';

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
    subdomains: Array
});

const fetchStates = async (params) => {
    const response = await StateService.getStates(params);
    return response.data;
};

const {
    data: states,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchStates);

const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    state: null
});

onMounted(fetchData);
</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />

        <div class="card">
            <!-- CREATE BUTTON -->
            <Button
                v-if="has('create subdomain_state')"
                icon="pi pi-plus"
                label="Create"
                class="mr-2"
                @click="data.createOpen = true"
            />

            <!-- REFRESH GOMB -->
            <Button
                @click="fetchData"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
            />

            <DataTable
                v-if="states"
                :value="states.data"
                :rows="states.per_page"
                :totalRecords="states.total"
                :first="(states.current_page - 1) * states.per_page"
                :loading="isLoading"
                lazy paginator dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >
                <template #header>
                    <div class="flex justify-between">
                        <Button
                            type="button"
                            icon="pi pi-filter-slash"
                            label="Clear"
                            outlined
                            @click="clearSearch"
                        />
                        <div class="font-semibold text-xl mb-1">
                            subdomains_title
                        </div>
                        <div class="flex justify-end">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText
                                    v-model="params.search"
                                    placeholder="Keyword Search"
                                />
                            </IconField>
                        </div>
                    </div>
                </template>

                <template #empty>No data found.</template>
                <template #loading>Loading data. Please wait.</template>

                <Column field="id" header="ID" />
                <Column field="name" header="Name" />
                <Column :exportable="false" style="min-width: 12rem" header="Actions">
                    <template #body="slotProps">
                        <Button
                            v-if="has('update subdomain_state')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="() => {
                                data.editOpen = true;
                                data.state = slotProps.data;
                            }"
                        />
                        <Button
                            v-if="has('delete subdomain_state')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            @click="() => {
                                data.deleteOpen = true;
                                data.state = slotProps.data;
                            }"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AuthLayout>
</template>
