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
});

const fetchItems = async () => {
    loading.value = true;

    await CompanyService.getCompanies()
        .then((response) => {
            console.log('response', response);
            data.companies = response.data;
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
/*
const deleteData = () => {
    deleteDialog.value = false;
    //data.deleteOpen = false;

    form.delete(route("company.destroy", data.company?.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};
*/
/*
const onPageChange = (event) => {
    router.get(
        route('company.index'),
        { page: event.page + 1 },
        { preserveState: true }
    );
};
*/
/*
watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        router.get(route('company.index'), params, {
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

            <div>
                <pre>{{ JSON.stringify(data.companies, null, 2) }}</pre>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped lang="scss">

</style>
