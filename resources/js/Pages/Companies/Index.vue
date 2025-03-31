<script setup>
import { reactive, watch } from 'vue';
import { Head, router } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";

import pkg from "lodash";

const { _, debounce, pickBy } = pkg;

const props = defineProps({
    title: String,
    filters: Object,
    companies: Object,
    perPage: Number,
});

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        createOpen: false,
        editOpen: false,
    },
    company: null
})

const onPageChange = (event) => {
    router.get(
        route('companies.index'),
        { page: event.page + 1 },
        { preserveState: true },
    ).then((response) => {
        console.log('response', response);
    }).catch((error) => {
        console.log('error', error);
    }).finally(() => {
        console.log('finally');
    });
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        router.get(route("permission.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        });
    }, 150),
);

</script>

<template>
    <AuthLayout>
        <Head :title="props.title"/>

        <div class="card">

            <Button
                v-show="can(['create permission'])"
                label="Create"
                @click="data.createOpen = true"
                icon="pi pi-plus"
            />

            <DataTable
                lazy paginator
                :value="companies.data"
                :rows="companies.per_page"
                :totalRecords="companies.total"
                :first="(companies.current_page - 1) * companies.per_page"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >
                <Column field="name" header="Name" />
                <Column field="email" header="Email" />
                <Column field="address" header="Address" />
                <Column field="phone" header="Phone" />

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button
                            v-show="can(['update permission'])"
                            icon="pi pi-pencil"
                            outlined
                            rounded
                            class="mr-2"
                            @click="
                                ((data.editOpen = true),
                                (data.permission = slotProps.data))
                            "
                        />
                        <Button
                            v-show="can(['delete permission'])"
                            icon="pi pi-trash"
                            outlined
                            rounded
                            severity="danger"
                            @click="
                                deleteDialog = true;
                                data.permission = slotProps.data;
                            "
                        />
                    </template>
                </Column>

            </DataTable>
        </div>
    </AuthLayout>
</template>
