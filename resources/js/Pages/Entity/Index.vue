<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import Create from "@/Pages/Entity/Create.vue";

import pkg from "lodash";
const { _, debounce, pickBy } = pkg;

const props = defineProps({
    title: String,
    filters: Object,
    entities: Object,
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
    },
    entity: null
});

const onPageChange = (event) => {
    router.get(
        route("entities.index"),
        { page: event.page + 1 },
        { preserveState: true },
    );
};

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

</script>

<template>
    <AuthLayout>

        <Head :title="props.title" />

        <div class="card">

            <Create
                :show="data.createOpen"
                :title="props.title"
                @close="data.createOpen = false"
            />

            <Button
                v-show="true"
                icon="pi pi-plus"
                label="Create"
                @click="data.createOpen = true"
            />

            <DataTable
                lazy paginator
                :value="entities.data"
                :rows="entities.per_page"
                :totalRecords="entities.total"
                :first="(entities.current_page - 1) * entities.per_page"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >
                <template #header>
                    <div class="flex justify-end">
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText
                                v-model="data.params.search"
                                placeholder="Keyword Search"
                            />
                        </IconField>
                    </div>
                </template>
                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column header="No">
                    <template #body="slotProps">
                        {{ slotProps.index + 1 }}
                    </template>
                </Column>

                <Column field="name" header="Name"></Column>
                
                <Column field="created_at" header="Created"></Column>
                <Column field="updated_at" header="Updated"></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        
                        <Button
                            v-show="true"
                            icon="pi pi-pencil"
                            outlined
                            rounded
                            class="mr-2"
                            @click="
                                ((data.editOpen = true),
                                (data.entity = slotProps.data))
                            "
                        />
                        <Button
                            v-show="true"
                            icon="pi pi-trash"
                            outlined
                            rounded
                            severity="danger"
                            @click="
                                deleteDialog = true;
                                data.entity = slotProps.data;
                            "
                        />
                    
                    </template>
                </Column>
            </DataTable>
        </div>

    </AuthLayout>
</template>