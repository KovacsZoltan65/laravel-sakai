<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import CityService from "@/service/Geo/CityService.js";

import { usePermissions } from '@/composables/usePermissions';
const { has } = usePermissions();

import pkg from "lodash";
const { _, debounce, pickBy } = pkg;

const isLoading = ref(false);

const props = defineProps({
    title: String,
    filters: Object,
    regions: Array,
    countries: Array,
});

const cities = ref(null);

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
    city: null
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
        const response = await CityService.getCities(params);

        cities.value = response.data;
    } catch(error) {
        console.error("Hiba az városok lekérdezésekor", error);
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
    console.log('Clear Filters');
};

</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />

        <div class="card"></div>
    </AuthLayout>
</template>