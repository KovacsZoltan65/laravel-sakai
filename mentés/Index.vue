<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";

import EntityService from '../../service/EntityService.js';

import Create from "@/Pages/Entity/Create.vue";

import pkg from "lodash";
const { _, debounce, pickBy } = pkg;

const entities = ref();
const dt = ref();

const props = defineProps({
    title: String,
    filters: Object,
    //entities: Object,
    perPage: Number,
});

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
        pagination: {
            current_page: 0,
            per_page: 0,
            total: 0,
            last_page: 0,
            from: 0,
            to: 0,
            first_page_url: '',
            last_page_url: '',
            links: [],
            next_page_url: '',
            path: '',
            per_page: 0,
            prev_page_url: '',
            total: 0
        }
    },
    entity: null
});

//const onPageChange = (event) => {
//    router.get(
//        route("entities.index"),
//        { page: event.page + 1 },
//        { preserveState: true },
//    );
//};

const onPageChange = (event) => {
    router.get(
        route('api.entities'),
        { page: event.page + 1 },
        { preserveState: true },
    );
}

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);

        router.get(route("entities.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        });
    }, 150),
);

const fetchItems = async () => {
    await EntityService.getEntities()
    .then((response) => {
        //console.log('response', response.data);
/*
        //console.log('response.data.entities', response.data.entities);
        console.log('response.data.entities.current_page', response.data.entities.current_page);
        console.log('response.data.entities.first_page_url', response.data.entities.first_page_url);
        console.log('response.data.entities.from', response.data.entities.from);
        console.log('response.data.entities.last_page', response.data.entities.last_page);
        console.log('response.data.entities.links', response.data.entities.links);
        console.log('response.data.entities.next_page_url', response.data.entities.next_page_url);
        console.log('response.data.entities.path', response.data.entities.path);
        console.log('response.data.entities.per_page', response.data.entities.per_page);
        console.log('response.data.entities.prev_page_url', response.data.entities.prev_page_url);
        console.log('response.data.entities.to', response.data.entities.to);
        console.log('response.data.entities.total', response.data.entities.total);

        console.log('=================================');
*/
        //console.log('data.params.pagination.current_page', data.params.pagination.first_page_url);
        data.params.pagination.current_page = response.data.entities.current_page;
        data.params.pagination.first_page_url = response.data.entities.first_page_url;
        data.params.pagination.from = response.data.entities.from;
        data.params.pagination.last_page = response.data.entities.last_page;
        data.params.pagination.links = response.data.entities.links;
        data.params.pagination.next_page_url = response.data.entities.next_page_url;
        data.params.pagination.path = response.data.entities.path;
        data.params.pagination.per_page = response.data.entities.per_page;
        data.params.pagination.prev_page_url = response.data.entities.prev_page_url;
        data.params.pagination.to = response.data.entities.to;
        data.params.pagination.total = response.data.entities.total;
        
        //console.log('data.params.pagination.total', data.params.pagination.total);

        //data.params.pagination.curent_page = response.data.current_page;

        //console.log('params.pagination', data.params.pagination);

        entities.value = response.data.entities.data;
        //console.log('entities.value.entities', entities.value.entities);
        console.log('response.data.entities', response.data.entities);
    })
    .catch((error) => {
        console.log('error', error);
    })
    .finally(() => {
        console.log('finally');
    });
};

onMounted(() => {
    fetchItems();
});

</script>

<template>
    <AuthLayout>

        <Head :title="props.title" />

        <div class="card">

            <DataTable
                ref="dt" lazy paginator dataKey="id"
                tableStyle="min-width: 50rem"
                :value="entities"
                :rows="data.params.pagination.per_page"
                :totalRecords="data.params.pagination.total"
                :first="(data.params.pagination.current_page - 1) * data.params.pagination.per_page"
                @page="onPageChange"
            >
                <Column field="name" header="Name" />
                <Column field="email" header="Email" />

            </DataTable>
        
        </div>

    </AuthLayout>
</template>