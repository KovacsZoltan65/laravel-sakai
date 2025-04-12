<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

import StateService from '@/service/Subdomains/SubdomainStateService.js';

import { usePermissions } from '@/composables/usePermissions';
const { has } = usePermissions();

import pkg from "lodash";
const { _, debounce, pickBy } = pkg;

const isLoading = ref(false);

const props = defineProps({
    title: String,
    filters: Object,
    subdomains: Array
});

const states = ref(null);

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        page: 1, // 👉 hozzáadva
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
    },
    state: null
});

const onPageChange = (event) => {
    data.params.page = event.page + 1;
    fetchItems();
};

const fetchItems = async () => {

    isLoading.value = true;

    const params = pickBy({
        page: data.params.page ?? 1,
        search: data.params.search,
        field: data.params.field,
        order: data.params.order,
    });

    try {
        const response = await StateService.getStates(params);

        states.value = response.data;

        console.log('states.value', states.value);
    } catch(error) {
        console.error("Hiba az subdomai státuszok lekérdezésekor", error);
    } finally {
        isLoading.value = false;
    }

};

watch(
    () => [data.params.search, data.params.field, data.params.order], // 🧠 kizárjuk a page-et
    debounce(() => {
        data.params.page = 1; // új keresés = első oldal
        fetchItems();
    }, 300)
);

onMounted(() => {
    fetchItems();
});

const clearFilter = () => {
    console.log('Clear Filters');
};

</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />

        <div class="card">

            <!-- CREATE MODAL -->

            <!-- EDIT MODAL -->

            <!-- DELETE MODAL -->

            <!-- CREATE BUTTON -->
            <Button
                v-show="has('create subdomain_state')"
                icon="pi pi-plus"
                label="Create"
                @click="data.createOpen = true"
                class="mr-2" />

            <!-- REFRESH GOMB -->
            <Button
                @click="fetchItems"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
            />

            <DataTable
                v-if="states"
                :dataKey="'id'" lazy paginator
                :value="states.data"
                :rows="states.per_page"
                :totalRecords="states.total"
                :first="(states.current_page - 1) * states.per_page"
                :loading="isLoading"
                @page="onPageChange"
                tableStyle="min-width: 50rem">

                <template #header>
                    <div class="flex justify-between">

                        <!-- SZŰRÉS TÖRLÉSE -->
                        <Button
                            type="button"
                            icon="pi pi-filter-slash"
                            label="Clear"
                            outlined
                            @click="clearFilter()"
                        />

                        <!-- FELIRAT -->
                        <div class="font-semibold text-xl mb-1">
                            subdomains_title
                        </div>

                        <!-- KERESÉS-->
                        <div class="flex justify-end">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText
                                    v-model="data.params.search"
                                    placeholder="Keyword Search"
                                />
                            </IconField>
                        </div>
                    </div>
                </template>

                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column field="id" header="ID" />
                <Column field="name" header="Name" />

                <Column :exportable="false" style="min-width: 12rem" header="Actions">

                    <template #body="slotProps">

                        <Button
                            v-show="has('update subdomain_state')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="(
                                (data.editOpen = true),
                                (data.state = slotProps.data)
                            )" />

                        <Button
                            v-show="has('delete subdomain_state')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            @click="(
                                (data.deleteOpen = true),
                                (data.state = slotProps.data)
                            )" />

                    </template>
                </Column>

            </DataTable>
        </div>

    </AuthLayout>
</template>
