<script setup>
import { onMounted, reactive, watch } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";

import CreateModal from "@/Pages/User/Create.vue";
import EditModal from "@/Pages/User/Edit.vue";
import DeleteModal from "@/Pages/User/Delete.vue";

import UserService from "@/service/UserService.js";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object
});

const fetchUsers = async (params) => {
    const response = await UserService.getUsers(params);
    return response.data;
};

// 👇 Hook használata
const {
    data: users,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchUsers);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    user: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('users.index', newParams), { preserveScroll: true, preserveState: true });
}, { deep: true });

onMounted(fetchData);

</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />
        
        <div class="card">
            <CreateModal 
                :show="data.createOpen"
                :title="props.title"
                @close="data.createOpen = false"
                @saved="fetchData"
            />
            <EditModal
                :show="data.editOpen"
                :user="data.user"
                :title="props.title"
                @close="data.editOpen = false"
                @saved="fetchData"
            />
            <DeleteModal
                :show="data.deleteOpen"
                :user="data.user"
                :title="props.title"
                @close="data.deleteOpen = false"
                @deleted="fetchData" />

            <Button v-if="has('create user')" icon="pi pi-plus" label="Create" @click="data.createOpen = true"
                class="mr-2" />

            <Button @click="fetchData" :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" />
        </div>

    </AuthLayout>
</template>