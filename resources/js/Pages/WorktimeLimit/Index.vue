<script setup>
import { onMounted, reactive } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
import WorktimeLimitService from '@/service/WorktimeLimitService.js';

import CreateModal from "@/Pages/WorktimeLimit/Create.vue";
import EditModal from "@/Pages/WorktimeLimit/Edit.vue";
import DeleteModal from "@/Pages/WorktimeLimit/Delete.vue";

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
});

// 👇 API hívás definíció
const fetchLimits = async (params) => {
    //
};

// 👇 Hook használata
const {
    data: limits,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchLimits);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    limit: null
});

onMounted(fetchData);
</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />

        <div class="card">

            <CreateModal 
                :show="data.createOpen"
                :title="props.title"
                @close="data.createOpen = false"
                @saved="fetchData"
            />

            <EditModal 
                :show="data.editOpen"
                :title="props.title"
                :limit="data.limit"
                @close="data.editOpen = false"
                @saved="fetchData"
            />

            <DeleteModal
                :show="data.deleteOpen"
                :title="props.title"
                :limit="data.limit"
                @close="data.deleteOpen = false"
                @deleted="fetchData"
            />

            <Button 
                v-if="has('create worktime_limit')" 
                icon="pi pi-plus" 
                label="Create" @click="data.createOpen = true"
                class="mr-2"
            />
            
            <Button 
                @click="fetchData" 
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
            />

            <DataTable
                v-if="limits"
                :value="limits.value"
                :rows="limits.per_page"
                :totalRecords="limits.total"
                :first="(limits.current_page - 1) * limits.per_page"
                :loading="isLoading" lazy paginator
                dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >
                
                <template #header>
                    <div class="flex justify-between">
                        <Button 
                            type="button" 
                            icon="pi pi-filter-slash" 
                            label="Clear" outlined 
                            @click="clearSearch"
                        />
                        <div class="font-semibold text-xl mb-1">entities_title</div>
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

            </DataTable>

        </div>

    </AuthLayout>
</template>