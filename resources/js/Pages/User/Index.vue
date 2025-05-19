<script setup>
import { onMounted, reactive, watch } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";

import CreateModal from "@/Pages/User/Create.vue";
import EditModal from "@/Pages/User/Edit.vue";
import DeleteModal from "@/Pages/User/Delete.vue";

import UserService from "@/service/UserService.js";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
    roles: Object,
});

const fetchUsers = async (params) => {
    const response = await UserService.getUsers(params);
    return response.data;
};

// 👇 Hook használata
const {
    data: users,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchUsers);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    user: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('users.index', newParams), { preserveScroll: true, preserveState: true });
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
                :roles="props.roles"
                @close="data.createOpen = false"
                @saved="fetchData"
            />

            <!-- EDIT MODAL -->
            <EditModal
                :show="data.editOpen"
                :user="data.user"
                :title="props.title"
                :roles="props.roles"
                @close="data.editOpen = false"
                @saved="fetchData"
            />

            <!-- DELETE MODAL -->
            <DeleteModal
                :show="data.deleteOpen"
                :user="data.user"
                :title="props.title"
                @close="data.deleteOpen = false"
                @deleted="fetchData" />

            <Button 
                v-if="has('create user')" 
                icon="pi pi-plus" 
                label="Create" 
                @click="data.createOpen = true"
                class="mr-2"
            />

            <Button 
                @click="fetchData" 
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
            />

            <DataTable 
                v-if="users" 
                :value="users.data" 
                :rows="users.per_page"
                :totalRecords="users.total" 
                :first="(users.current_page - 1) * users.per_page"
                :loading="isLoading" 
                lazy paginator 
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

                <Column header="Role">
                    <template #body="slotProps">
                       {{ slotProps.data.roles[0].name }}
                    </template>                 
                </Column>

                <Column :exportable="false" 
                    style="width: 150px; min-width: 150px; max-width: 150px;">
                    <template #body="slotProps">
                        <Button 
                            v-if="has('update company')" 
                            icon="pi pi-pencil" 
                            outlined rounded 
                            class="mr-2" @click="() => {
                                data.editOpen = true;
                                data.user = slotProps.data;
                            }" />
                        <Button 
                            v-if="has('delete company')" 
                            icon="pi pi-trash" 
                            outlined rounded 
                            severity="danger"
                            @click="() => {
                                data.deleteOpen = true;
                                data.user = slotProps.data;
                            }" />
                    </template>
                </Column>


            </DataTable>

        </div>

    </AuthLayout>
</template>