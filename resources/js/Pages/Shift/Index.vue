<script setup>
import { onMounted, reactive, watch } from "vue";
import { nextTick } from 'vue';
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

    // 💤 Szimulált késleltetés (pl. 1.5 másodperc)
    //await new Promise(resolve => setTimeout(resolve, 1500));

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

//onMounted(fetchData);

onMounted(async () => {
  await nextTick(); // teljes renderelés megvárása
  await fetchData(); // itt már minden DOM elem is kész
});


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

            <!-- CREATE BUTTON -->
            <Button
                v-if="has('create shift')"
                icon="pi pi-plus"
                label="Create"
                class="mr-2"
                @click="data.createOpen = true"
            />
            <!-- REFRESH BUTTON -->
            <Button
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
                @click="fetchData"
            />

            <DataTable
            :value="shifts?.data ?? []"
:rows="shifts?.per_page ?? 10"
:totalRecords="shifts?.total ?? 0"
:first="((shifts?.current_page ?? 1) - 1) * (shifts?.per_page ?? 10)"
                :loading="isLoading" lazy paginator
                dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >

                <template #header>
                    <div class="flex justify-between">
                        <!-- CLEAR FILTER -->
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

                <!--<template #loading>
                    <div class="flex justify-center items-center h-24 text-lg text-gray-500">
                        <i class="pi pi-spin pi-spinner mr-2"></i> Loading shifts...
                    </div>
                </template>-->

                <Column field="id" header="#" />
                <Column field="name" header="Name" />
                <Column field="code" header="Code" />
                <Column field="start_time" header="Start" />
                <Column field="end_time" header="End" />

                <Column>
                    <template #body="slotProps">
                        <Button
                            v-if="has('update shift')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="() => {
                                data.editOpen = true;
                                data.shift = slotProps.data;
                            }"
                        />

                        <Button
                            v-if="has('delete shift')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            @click="() => {
                                data.deleteOpen = true;
                                data.shift = slotProps.data;
                            }"
                        />
                    </template>
                </Column>

            </DataTable>

        </div>
    </AuthLayout>
</template>
