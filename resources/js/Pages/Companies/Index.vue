<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import pkg from "lodash";
const { _, debounce, pickBy } = pkg;
import { router } from "@inertiajs/vue3";
import { loadToast } from "@/composables/loadToast";

import AppLayout from "@/sakai/layout/AppLayout.vue";
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
/*
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
*/
</script>

<template>
    <AppLayout>
        <div class="card">

            <DataTable lazy paginator :value="data.companies" ref="dt" dataKey="id" :rows="data.pagination.per_page"
                :totalRecords="data.pagination.total"
                :first="(data.pagination.current_page - 1) * data.pagination.per_page" @page="onPageChange"
                tableStyle="min-width: 50rem">
                <Column field="name" header="name" />
                <Column field="email" header="email" />
                <Column field="address" header="address" />
                <Column field="phone" header="phone" />
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
    </AppLayout>
</template>
