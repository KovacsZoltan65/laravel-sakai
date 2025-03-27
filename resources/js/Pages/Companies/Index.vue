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
        { preserveState: true },
        { preserveScroll: true }
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

        </div>
    </AuthLayout>
</template>
