<script setup>
import { onMounted, reactive, ref, watch, computed } from "vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
//import { router } from "@inertiajs/vue3";
import CreateModal from "@/Pages/Entity/Create.vue";
import EditModal from "@/Pages/Entity/Edit.vue";

import { usePermissions } from '@/composables/usePermissions';
const { has } = usePermissions();

import pkg from "lodash";
const { _, debounce, pickBy } = pkg;

const isLoading = ref(false);

const props = defineProps({
  title: String,
  filters: Object,
  companies: Array,
});

const entities = ref(null);

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
    entity: null
});

const onPageChange = (event) => {
    data.params.page = event.page + 1;
    fetchItems();
};

/**
 * Lekéri az entitásokat a szerverről,
 * majd beállítja a `entities`-t a kapott adatokkal.
 *
 * @param {Object} [params={}] - a lekérdezés paraméterei
 * @property {number} [params.page=1] - az oldal száma
 * @property {string} [params.search] - a keresendő szöveg
 * @property {string} [params.field] - a rendezendő mez
 * @property {string} [params.order] - a rendezés iránya (asc/desc)
 *
 * @returns {Promise<void>}
 */
const fetchItems = async () => {

    isLoading.value = true;

    const params = pickBy({
        page: data.params.page ?? 1,
        search: data.params.search,
        field: data.params.field,
        order: data.params.order,
    });

    try {
        //const response = await axios.get(route('api.entities.fetch'), { params });
        const response = await EntityService.getEntities(params);

        entities.value = response.data;
    } catch (error) {
        console.error("Hiba az entitások lekérdezésekor", error);
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

</script>

<template>
    <AuthLayout>

        <Head :title="props.title" />

        <div class="card">

            <CreateModal
                :show="data.createOpen"
                :title="props.title"
                @close="data.createOpen = false"
                @saved="fetchItems"
            />

            <EditModal 
                :show="data.editOpen"
                :entity="data.entity"
                :title="props.title"
                @close="data.editOpen = false"
                @saved="fetchItems"
            />

            <Button
                v-show="has('create entity')"
                icon="pi pi-plus"
                label="Create"
                @click="data.createOpen = true"
                class="mr-2"
            />

            <Button
                @click="fetchItems"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
            />

            <DataTable
                v-if="entities"
                :dataKey="'id'"
                lazy paginator
                :value="entities.data"
                :rows="entities.per_page"
                :totalRecords="entities.total"
                :first="(entities.current_page - 1) * entities.per_page"
                :loading="isLoading"
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

                <Column field="id" header="#"></Column>
                <Column field="name" header="Name"></Column>

                <Column field="created_at" header="Created"></Column>
                <Column field="updated_at" header="Updated"></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">

                        <Button
                            v-show="has('update entity')"
                            icon="pi pi-pencil"
                            outlined
                            rounded
                            class="mr-2"
                            @click="((data.editOpen = true),(data.entity = slotProps.data))"
                        />
                        <Button
                            v-show="has('delete entity')"
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
