<script setup>
import { onMounted, reactive, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import CreateModal from "@/Pages/Shift/Create.vue";
import EditModal from "@/Pages/Shift/Edit.vue";
import DeleteModal from "@/Pages/Shift/Delete.vue";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
import ShiftService from '@/service/ShiftService.js';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: {
        type: Object,
        default: () => ({}) // 💡 garantáltan nem lesz undefined
    },
});

// 👇 API hívás definíció
const fetchShifts = async (params) => {
    const response = await ShiftService.getShifts(params);
    return response.data;
};

// 👇 Hook használata
const {
    data: shifts,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchShifts);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    shift: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('shifts.index', newParams), { preserveScroll: true, preserveState: true });
}, { deep: true });

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
                @close="data.editOpen = false" 
                @saved="fetchData"
            />
            <DeleteModal 
                :show="data.deleteOpen" 
                :title="props.title" 
                @close="data.deleteOpen = false" 
                @saved="fetchData"
            />

            <Button 
                v-if="has('create shift')" 
                icon="pi pi-plus" 
                label="Create" 
                class="mr-2" 
                @click="data.createOpen = true"
            />
            <Button 
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" 
                @click="fetchData"
            />

            <DataTable
                v-if="shifts" 
                :value="shifts.data" 
                :rows="shifts.per_page" 
                :totalRecords="shifts.total"
                :first="(shifts.current_page - 1) * shifts.per_page" 
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

                        <div class="font-semibold text-xl mb-1">shifts_title</div>
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