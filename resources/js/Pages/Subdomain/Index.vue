<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

import SubdomainService from '@/service/SubdomainService.js';

import pkg from "lodash";
const { _, debounce, pickBy } = pkg;

const props = defineProps({
    title: String,
    filters: Object,
    states: Array
});

const subdomains = ref(null);

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
    subdomain: null
});

const isLoading = ref(false);

const fetchItems = async () => {

    isLoading.value = true;

    const params = pickBy({
        page: data.params.page ?? 1,
        search: data.params.search,
        field: data.params.field,
        order: data.params.order,
    });

    try {
        const response = await SubdomainService.getSubdomains(params);

        subdomains.value = response.data;

        console.log('subdomains.value', subdomains.value);
    } catch(error) {
        console.error("Hiba az subdomainek lekérdezésekor", error);
    } finally {
        isLoading.value = false;
    }

};

onMounted(() => {
    fetchItems();
});

</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />
        SUBDOMAINS<br/>
        <pre>
        {{ props.states }}
        </pre>
    </AuthLayout>

</template>