<script setup>
import { onMounted, reactive } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import CreateModal from "@/Pages/Entity/Create.vue";
import EditModal from "@/Pages/Entity/Edit.vue";
import DeleteModal from "@/Pages/Entity/Delete.vue";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
import EntityService from '@/service/EntityService.js';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
    companies: Array,
});

// 👇 API hívás definíció
const fetchEntities = async (params) => {
    const response = await EntityService.getEntities(params);
    return response.data;
};

// 👇 Hook használata
const {
    data: entities,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchEntities);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    entity: null
});

onMounted(fetchData);
</script>

<template>
    <AuthLayout>

        <Head :title="props.title" />
        <div class="card">
            <CreateModal :show="data.createOpen" :title="props.title" @close="data.createOpen = false"
                @saved="fetchData" />
            <EditModal :show="data.editOpen" :entity="data.entity" :title="props.title" @close="data.editOpen = false"
                @saved="fetchData" />
            <DeleteModal :show="data.deleteOpen" :entity="data.entity" :title="props.title"
                @close="data.deleteOpen = false" @deleted="fetchData" />

            <Button v-if="has('create entity')" icon="pi pi-plus" label="Create" @click="data.createOpen = true"
                class="mr-2" />
            <Button @click="fetchData" :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" />

            <DataTable v-if="entities" :value="entities.data" :rows="entities.per_page" :totalRecords="entities.total"
                :first="(entities.current_page - 1) * entities.per_page" :loading="isLoading" lazy paginator
                dataKey="id" @page="onPageChange" tableStyle="min-width: 50rem">
                <template #header>
                    <div class="flex justify-between">
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearSearch" />
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
                <Column field="created_at" header="Created" />
                <Column field="updated_at" header="Updated" />
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-if="has('update entity')" icon="pi pi-pencil" outlined rounded class="mr-2"
                            @click="() => { data.editOpen = true; data.entity = slotProps.data; }" />
                        <Button v-if="has('delete entity')" icon="pi pi-trash" outlined rounded severity="danger"
                            @click="() => { data.deleteOpen = true; data.entity = slotProps.data; }" />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AuthLayout>
</template>
