<script setup>
import { onMounted, reactive, ref } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";

import SubdomainService from '@/service/Subdomains/SubdomainService.js';

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
    states: Array
});

const fetchSubdomains = async (params) => {
    const response = await SubdomainService.getSubdomains(params);
    return response.data;
};

const {
    data: subdomains,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchSubdomains);

const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    subdomain: null
});

const buttonFrozen = ref(false);

onMounted(fetchData);
</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />

        <div class="card">
            <Button
                @click="fetchData"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
            />

            <DataTable
                v-if="subdomains"
                :value="subdomains.data"
                :rows="subdomains.per_page"
                :totalRecords="subdomains.total"
                :first="(subdomains.current_page - 1) * subdomains.per_page"
                :loading="isLoading"
                lazy paginator dataKey="id" stripedRows scrollable
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
                                <InputIcon><i class="pi pi-search" /></InputIcon>
                                <InputText v-model="params.search" placeholder="Keyword Search" />
                            </IconField>
                        </div>
                    </div>
                </template>

                <template #empty>No data found.</template>
                <template #loading>Loading data. Please wait.</template>

                <Column
                    :exportable="false"
                    style="min-width: 8rem"
                    header="Actions"
                    frozen
                >
                    <template #body="slotProps">
                        <Button
                            v-if="has('update subdomain')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="() => {
                                data.editOpen = true;
                                data.subdomain = slotProps.data;
                            }"
                        />
                        <Button
                            v-if="has('delete subdomain')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            @click="() => {
                                data.deleteOpen = true;
                                data.subdomain = slotProps.data;
                            }"
                        />
                    </template>
                </Column>

                <Column field="id" header="ID" class="font-bold" />
                <Column field="name" header="Name"/>
                <Column field="url" header="URL" />
                <Column field="db_host" header="DB host" />
                <Column field="db_name" header="DB name" />
                <Column field="db_port" header="Port" />
                <Column field="db_user" header="User" />
                <Column field="db_password" header="Password" />
                <Column field="state_id" header="State" />
                <Column field="subdomain_state.name" header="State" />
                <Column field="notification" header="notification" />
                <Column field="sso" header="sso" />
                <Column field="is_mirror" header="is_mirror" />
                <Column field="created_at" header="Created At" />
                <Column field="updated_at" header="Updated At" />
                <Column field="deleted_at" header="Deleted At" />
            </DataTable>
        </div>
    </AuthLayout>
</template>
