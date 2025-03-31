<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import CreateModal from "@/Pages/Company/Create.vue";
import EditModal from "@/Pages/Company/Edit.vue";
import DeleteModal from "@/Pages/Company/Delete.vue";

import { usePermissions } from '@/composables/usePermissions';
const { has } = usePermissions();

import pkg from "lodash";
const { _, debounce, pickBy } = pkg;

const isLoading = ref(false);

const props = defineProps({
    title: String,
    filters: Object,
    companies: Array,
});

const companies = ref(null);

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
    company: null
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
        const response = await axios.get(route('api.companies.fetch'), { params });
        //const response = await axios.get('/api/companies/fetch', { params });

        companies.value = response.data;
    } catch (error) {
        console.error("Hiba az entitások lekérdezésekor", error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchItems();
});

watch(
    () => [data.params.search, data.params.field, data.params.order], // 🧠 kizárjuk a page-et
    debounce(() => {
        data.params.page = 1; // új keresés = első oldal
        fetchItems();
    }, 300)
);

const clearFilter = () => {
    console.log('clearFilter');
}

</script>

<template>
    <AuthLayout>

        <Head :title="props.title" />

        <div class="card">

            <!-- CREATE MODAL -->
            <CreateModal
                :show="data.createOpen"
                :title="props.title"
                @close="data.createOpen = false"
                @saved="fetchItems"
            />

            <!-- EDIT MODAL -->
            <EditModal
                :show="data.editOpen"
                :company="data.company"
                :title="props.title"
                @close="data.editOpen = false"
                @saved="fetchItems"
            />

            <!-- TÖRLÉS MODAL -->
            <DeleteModal
                :show="data.deleteOpen"
                :company="data.company"
                :title="props.title"
                @close="data.deleteOpen = false"
                @deleted="fetchItems"
            />

            <Button
                :show="has('create company')"
                icon="pi pi-plus"
                label="Create"
                @click="data.createOpen = true"
                class="mr-2"
            />

            <Button
                @click="fetchItems"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
            />

            <DataTable v-if="companies" :dataKey="'id'" lazy paginator :value="companies.data"
                :rows="companies.per_page" :totalRecords="companies.total"
                :first="(companies.current_page - 1) * companies.per_page" :loading="isLoading" @page="onPageChange"
                tableStyle="min-width: 50rem">
                <template #header>
                    <div class="flex justify-between">

                        <!-- SZŰRÉS TÖRLÉSE -->
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearFilter()" />

                        <!-- FELIRAT -->
                        <div class="font-semibold text-xl mb-1">
                            companies_title
                        </div>

                        <!-- KERESÉS-->
                        <div class="flex justify-end">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="data.params.search" placeholder="Keyword Search" />
                            </IconField>
                        </div>
                    </div>
                </template>

                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column field="name" header="Name" />
                <Column field="email" header="Email" />
                <Column field="address" header="Address" />
                <Column field="phone" header="Phone" />

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">

                        <Button v-show="has('update company')" icon="pi pi-pencil" outlined rounded class="mr-2" @click="(
                            (data.editOpen = true),
                            (data.company = slotProps.data)
                        )" />
                        <Button v-show="has('delete company')" icon="pi pi-trash" outlined rounded severity="danger"
                            @click="(
                                (data.deleteOpen = true),
                                (data.company = slotProps.data)
                            )" />

                    </template>
                </Column>
            </DataTable>
        </div>

    </AuthLayout>
</template>
