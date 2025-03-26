<script setup>
import { Head } from "@inertiajs/vue3";
import AuthLayout from "../../Layouts/AuthenticatedLayout.vue";
import { onMounted, reactive, ref } from "vue";
import { loadToast } from "@/composables/loadToast";
import PersonService from "@/service/PersonService.js";

const loading = ref(true);

const props = defineProps({
    title: String,
    content: String,
    perPage: Number,
});

loadToast();

const data = reactive({
    params: {
        search: props.filters.search ?? "",
        field: props.filters.field,
        order: props.filters.order,
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
    },
    persons: null,
    person: null,
    pagination: {
        current_page: 1,
        last_page: 0,
        per_page: 10,
        rows: 0
    }
});

const fetchItems = async () => {
    loading.value = true;

    await PersonService.getPersons()
    .then((response) => {
        console.log('response', response);
        data.persons = response.data.data;
        data.pagination = response.data.pagination;
    })
    .catch((error) => {
        console.error("getPersons API Error:", error);
    })
    .finally(() => {
        loading.value = false;
    });
};

onMounted(() => {
    fetchItems();
});

</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />

        

    </AuthLayout>
</template>