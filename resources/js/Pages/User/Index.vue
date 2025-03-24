<script setup>

//import AppLayout from "@/sakai/layout/AppLayout.vue";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";

import { Head } from "@inertiajs/vue3";
import Create from "@/Pages/User/Create.vue";
import Edit from "@/Pages/User/Edit.vue";
import { usePage, useForm } from "@inertiajs/vue3";

import { onMounted, reactive, ref, watch, computed } from "vue";
import pkg from "lodash";
import { router } from "@inertiajs/vue3";
const { _, debounce, pickBy } = pkg;
import { loadToast } from "@/composables/loadToast";

const props = defineProps({
    title: String,
    filters: Object,
    users: Object,
    roles: Object,
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
        // perPage: props.perPage,
        createOpen: false,
        editOpen: false,
    },
    user: null,
});

const deleteData = () => {
    deleteDialog.value = false;

    form.delete(route("user.destroy", data.user?.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};

const roles = props.roles?.map((role) => ({
    name: role.name,
    code: role.name,
}));

const onPageChange = (event) => {
    router.get(
        route("user.index"),
        { page: event.page + 1 },
        { preserveState: true },
    );
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        // A `pickBy` (a Lodash könyvtár része) kiszűri az `data.params`
        // objektumból azokat a kulcs-érték párokat, amelyek értékei `null`,
        // `undefined`, vagy más falsy értékek. Ez garantálja, hogy csak a
        // releváns paraméterek kerülnek tovább felhasználásra.
        let params = pickBy(data.params);
        router.get(route("user.index"), params, {
            // Nem ad új elemet a böngésző történetéhez, csak lecseréli az aktuális URL-t.
            replace: true,
            // Megőrzi a Vue komponens állapotát.
            preserveState: true,
            // Megőrzi a nézet görgetési pozícióját.
            preserveScroll: true,
        });
    }, 150),
);
</script>

<template>
    <AuthLayout>

        <Head :title="props.title" />

        <div class="card">

            <!-- CREATE MODAL -->
            <Create
                :show="data.createOpen"
                @close="data.createOpen = false"
                :roles="roles"
                :title="props.title"
            />

            <!-- EDIT MODAL -->
            <Edit
                :show="data.editOpen"
                @close="data.editOpen = false"
                :roles="roles"
                :user="data.user"
                :title="props.title"
            />

            <!-- CREATE BUTTON -->
            <Button
                v-show="can(['create user'])"
                label="Create"
                @click="data.createOpen = true"
                icon="pi pi-plus"
            />

            <!-- DATA TABLE -->
            <DataTable
                lazy
                :value="users.data"
                paginator
                :rows="users.per_page"
                :totalRecords="users.total"
                :first="(users.current_page - 1) * users.per_page"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >
                <template #header>
                    <div class="flex justify-end">

                        <!-- SEARCH -->
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

                <!-- NINCS ELEM -->
                <template #empty> No data found. </template>

                <!-- TÖLTÉS -->
                <template #loading> Loading data. Please wait. </template>

                <!-- ID -->
                <Column header="No">
                    <template #body="slotProps">
                        {{ slotProps.index + 1 }}
                    </template>
                </Column>

                <!-- NAME -->
                <Column field="name" header="Name"></Column>

                <!-- EMAIL -->
                <Column field="email" header="Email"></Column>

                <!-- ROLE -->
                <Column header="Role">
                    <template #body="slotProps">
                        {{ slotProps.data.roles[0].name }}
                    </template>
                </Column>

                <!-- CREATED -->
                <Column field="created_at" header="Created"></Column>

                <!-- UPDATED -->
                <Column field="updated_at" header="Updated"></Column>

                <!-- ACTIONS -->
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button
                            v-show="can(['update user'])"
                            icon="pi pi-pencil"
                            outlined
                            rounded
                            class="mr-2"
                            @click="
                                ((data.editOpen = true),
                                (data.user = slotProps.data))
                            "
                        />
                        <Button
                            v-show="can(['delete user'])"
                            icon="pi pi-trash"
                            outlined
                            rounded
                            severity="danger"
                            @click="
                                deleteDialog = true;
                                data.user = slotProps.data;
                            "
                        />
                    </template>
                </Column>
            </DataTable>

            <!-- DELETE MODAL -->
            <Dialog
                v-model:visible="deleteDialog"
                :style="{ width: '450px' }"
                header="Confirm"
                :modal="true"
            >
                <div class="flex items-center gap-4">
                    <i class="pi pi-exclamation-triangle !text-3xl" />
                    <span v-if="data.user"
                        >Are you sure you want to delete
                        <b>{{ data.user.name }}</b
                        >?</span
                    >
                </div>
                <template #footer>
                    <Button
                        label="No"
                        icon="pi pi-times"
                        text
                        @click="deleteDialog = false"
                    />
                    <Button
                        label="Yes"
                        icon="pi pi-check"
                        @click="deleteData"
                    />
                </template>
            </Dialog>
        </div>
    </AuthLayout>
</template>

<style scoped lang="scss"></style>
