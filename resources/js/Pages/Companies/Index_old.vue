<script setup>

/*
 * https://github.com/primefaces/primevue/issues/6648
 */
import { onMounted, reactive, ref, watch, computed } from "vue";
import { DataTable } from 'primevue';

import { router } from "@inertiajs/vue3";
import CompanyService from "@/service/CompanyService";

//const id = ref(router.params.id);
const apiUrl = `/api/getCompanies`;

const globalFilterFields = ref(["item_no", "item_type", "date"]);
const filters = ref({
    global: { value: '' },
    name: { value: null, matchMode: 'contains' },
    email: { value: null, matchMode: 'contains' },
    address: { value: null, matchMode: 'contains' },
    phone: { value: null, matchMode: 'contains' },
});

const exampleData = ref([]);
const loading = ref(false);

const onDateChange = (filterModel, filterCallback) => {
    console.log(filterModel.value);
    filterModel.value = filterModel.value.map((date) => {
        if (date instanceof Date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Pad month to 'MM' format
            const day = String(date.getDate()).padStart(2, '0'); // Pad day to 'DD' format
            return `${year}-${month}-${day}`; // Format as 'yyyy-mm-dd'
        }
        return date; // Return original value if it's not a Date
    });
    console.log(filterModel.value);
    filterCallback();
};

const onDateChange2 = (filterModel, filterCallback) => {
    console.log(filterModel.value);
    filterModel.value = filterModel.value.map((date) => {
        if (date instanceof Date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Pad month to 'MM' format
            const day = String(date.getDate()).padStart(2, '0'); // Pad day to 'DD' format
            return `${year}-${month}-${day}`; // Format as 'yyyy-mm-dd'
        }
        return date; // Return original value if it's not a Date
    });
    console.log(filterModel.value);
    filterCallback();
};

onMounted(async () => {
    CompanyService.getCompanies()
        .then((response) => {
            console.log('response.data',response.data);
            exampleData.value = response.data;
            console.log('exampleData.value',exampleData.value);
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            //
        });
    /*
    loading.value = true;
    await axios
        .get(apiUrl)
        .then((response) => {
            console.log(response.data);
            exampleData.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            loading.value = false;
        });
    */
});

</script>

<template>
    <DataTable
        v-model:filters="filters" 
        :value="exampleData.data" 
        paginator showGridlines 
        :rows="10" 
        dataKey="id"
        filterDisplay="row" 
        :loading="loading" 
        :globalFilterFields="globalFilterFields"
        :rowsPerPageOptions="[10, 25, 50, 100]"
        paginatorTemplate="CurrentPageReport RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products"
    >
        <template #header>
            <div class="flex justify-end">
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                </IconField>
            </div>
        </template>
        <template #empty> No items found. </template>
        <template #loading> Loading items data. Please wait. </template>

        <Column field="name" header="name" style="min-width: 12rem">
            <template #body="slotProps">
                <span>{{ slotProps.data.name }}</span>
            </template>
            <template #filter="{ filterModel, filterCallback }">
                <InputText 
                    v-model="filters['name'].value" 
                    @update:modelValue="onDateChange(filterModel, filterCallback)"
                />
            </template>
        </Column>
        <Column field="email" header="email" style="min-width: 12rem"></Column>
        <Column field="address" header="address" style="min-width: 12rem"></Column>
        <Column field="phone" header="phone" style="min-width: 12rem">
            <template #body="slotProps">
                <span>{{ slotProps.data.phone }}</span>
            </template>
            <template #filter="{ filterModel, filterCallback }">
                <InputText 
                    v-model="filters['phone'].value" 
                    @update:modelValue="onDateChange(filterModel, filterCallback)"
                />
            </template>
        </Column>

    </DataTable>
</template>