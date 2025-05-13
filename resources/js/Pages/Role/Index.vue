<script setup>
import { onMounted, reactive, watch } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";

import CreateModal from "@/Pages/User/Create.vue";
import EditModal from "@/Pages/User/Edit.vue";
import DeleteModal from "@/Pages/User/Delete.vue";

import RoleService from '@/service/RoleService.js';

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
    permissions: Object,
});

const fetchRoles = async (params) => {
    const response = await RoleService.getRoles(params);
    return response.data;
};

// 👇 Hook használata
const {
    data: roles,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchRoles);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    role: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('roles.index', newParams), { preserveScroll: true, preserveState: true });
}, { deep: true });

onMounted(fetchData);

</script>

<template>

    <AuthLayout>

        <Head :title="props.title" />

        <div class="card">

            <!-- CREATE MODAL -->
            <CreateModal 
                :show="data.createOpen"
                :title="props.title"
                :permissions="props.permissions"
                @close="data.createOpen = false"
                @saved="fetchData" />

            <!-- EDIT MODAL -->
            <EditModal
                :show="data.editOpen"
                :role="data.role"
                :title="props.title"
                :permissions="props.permissions"
                @close="data.editOpen = false"
                @saved="fetchData" />

            <!-- DELETE MODAL -->
            <DeleteModal
                :show="data.deleteOpen"
                :role="data.role"
                :title="props.title"
                @close="data.deleteOpen = false"
                @deleted="fetchData" />

            <!-- NEW BUTTON -->
            <Button
                v-if="has('create role')"
                icon="pi pi-plus"
                label="Create"
                class="mr-2"
                @click="data.createOpen = true" />

            <!-- REFRESH BUTTON -->
            <Button 
                @click="fetchData" 
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" />

            <DataTable>

                <template #header>
                    <div class="flex justify-between">
                        <Button
                            type="button"
                            icon="pi pi-filter-slash"
                            label="Clear" outlined
                            @click="clearSearch"
                        />

                        <div class="font-semibold text-xl mb-1">
                            roles_title
                        </div>

                        <div class="flex justify-end">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText 
                                    v-model="params.search" 
                                    placeholder="Keyword Search" />
                            </IconField>
                        </div>
                    </div>
                </template>

                <template #empty>No data found.</template>

                <template #loading>Loading data. Please wait.</template>

                <Column field="name" header="Name" />

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button 
                            v-if="has('update role')" 
                            icon="pi pi-pencil" 
                            outlined rounded 
                            class="mr-2" @click="() => {
                                data.editOpen = true;
                                data.user = slotProps.data;
                            }" />
                        <Button 
                            v-if="has('delete role')" 
                            icon="pi pi-trash" 
                            outlined rounded 
                            severity="danger"
                            @click="() => {
                                data.deleteOpen = true;
                                data.role = slotProps.data;
                            }" />
                    </template>
                </Column>


            </DataTable>

        </div>

    </AuthLayout>

</template>