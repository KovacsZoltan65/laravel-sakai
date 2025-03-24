<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, reactive, ref, watch, computed } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import pkg from "lodash";
const { _, debounce, pickBy } = pkg;
import { router } from "@inertiajs/vue3";
import { loadToast } from "@/composables/loadToast";

//import AppLayout from "@/sakai/layout/AppLayout.vue";
import AuthLayout from "../../Layouts/AuthenticatedLayout.vue";

import Create from "@/Pages/Companies/Create.vue";
import Edit from "@/Pages/Companies/Edit.vue";
import Delete from "@/Pages/Companies/Delete.vue";

import CompanyService from "@/service/CompanyService.js";

const loading = ref(true);

const props = defineProps({
    title: String,
    filters: Object,
    perPage: Number,
});

loadToast();

const deleteDialog = ref(false);
const form = useForm({});

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
    },
    companies: null,
    company: null,
    pagination: {
        current_page: 1,
        last_page: 0,
        per_page: 10,
        rows: 0
    }
});

const deleteData = () => {
    deleteDialog.value = false;

    form.delete(route("", data.companies?.id), {
        preverseScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};

const fetchItems = async () => {
    loading.value = true;

    await CompanyService.getCompanies()
        .then((response) => {
            //console.log('response', response);
            //console.log('response.data.data', response.data.data);
            //console.log('response.data.pagination', response.data.pagination);

            data.companies = response.data.data;
            //console.log('data.companies',data.companies);

            data.pagination = response.data.pagination;
            //console.log('data.pagination',data.pagination);

            //data.pagination.current_page = data.companies.pagination.current_page;
            //data.pagination.last_page = data.companies.pagination.last_page;
            //data.pagination.per_page = data.companies.pagination.per_page;
            //data.pagination.total = data.companies.pagination.total;
            //console.log('data.pagination', data.pagination);

        })
        .catch((error) => {
            console.error("getCompanies API Error:", error);
        })
        .finally(() => {
            loading.value = false;
        });
};

onMounted(() => {
    fetchItems();
});

const onPageChange = (event) => {
    router.get(
        route('api.companies'),
        { page: event.page + 1 },
        { preserveState: true }
    );
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        console.log('params', params);
        router.get(route('api.companies'), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        })
    }, 150),
);

const onUpload = (event) => {
    console.log('onUpload', event);
};

const exportCSV = () => {
    dt.value.exportCSV();
};

</script>

<template>
    <AuthLayout>

        <Head :title="props.title" />

        <!-- CREATE MODAL -->
        <Create
            :show="data.createOpen"
            @close="data.createOpen = false"
            title="Company"
        />
        
        <!-- EDIT MODAL -->
        <Edit
            :show="data.editOpen"
            @close="data.editOpen = false"
            :company="data.company"
            title="Company"
        />

        <!-- DELETE MODAL -->
        <Delete
            :show="data.deleteDialog"
            @close="data.deleteDialog = false"
            title="Company"
        />

        <div class="card">

            <Toolbar class="mb-6">

                <template #start>
                    <!-- CREATE BUTTON -->
                    <Button
                        v-show="true"
                        label="Create"
                        @click="data.createOpen = true"
                        icon="pi pi-plus"
                    />
                </template>

                <template #end>
                    <FileUpload
                        mode="basic"
                        accept="image/*"
                        :maxFileSize="1000000"
                        label="Import"
                        customUpload auto
                        chooseLabel="Import"
                        class="mr-2"
                        :chooseButtonProps="{ severity: 'secondary' }"
                        @upload="onUpload"
                    />
                    <Button
                        label="Export"
                        icon="pi pi-upload"
                        severity="secondary"
                        @click="exportCSV($event)"
                    />
                </template>

            </Toolbar>

            <DataTable
                lazy paginator
                :value="data.companies"
                ref="dt"
                dataKey="id"
                :rows="data.pagination.per_page"
                :totalRecords="data.pagination.total"
                :first="(data.pagination.current_page - 1) * data.pagination.per_page"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >

                <Column field="name" header="name" />
                <Column field="email" header="email" />
                <Column field="address" header="address" />
                <Column field="phone" header="phone" />
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button
                            v-show="true"
                            icon="pi pi-pencil"
                            outlined
                            rounded
                            class="mr-2"
                            @click="((data.editOpen = true), (data.company = slotProps.data))"
                        />
                        <Button
                            v-show="true"
                            icon="pi pi-trash"
                            outlined
                            rounded
                            severity="danger"
                            @click="deleteDialog = true;
                                data.company = slotProps.data;"
                        />
                    </template>
                </Column>
            </DataTable>

            <div>
                <!--<pre>pagination.total: {{ data.companies.pagination.total }} </pre>-->
                <!--<pre>pagination.per_page: {{ data.companies.pagination.per_page }} </pre>-->
                <!--<pre>pagination.current_page: {{ data.companies.pagination.current_page }} </pre>-->

                <!--<pre>pagination: {{ data.companies.pagination }}</pre>-->
                <!--<pre>data: {{ JSON.stringify(data.companies.data, null, 2) }}</pre>-->
                <!--<pre>data.companies: {{ JSON.stringify(data.companies, null, 2) }}</pre>-->
                <!--<pre>data: {{ JSON.stringify(data, null, 2) }}</pre>-->
            </div>

        </div>
    </AuthLayout>
</template>
