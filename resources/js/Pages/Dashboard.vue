<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import UsersCard from "@/Components/DashboardCards/UsersCard.vue";
import RolesCard from "@/Components/DashboardCards/RolesCard.vue";
import PermissionsCard from "@/Components/DashboardCards/PermissionsCard.vue";
import CompaniesCard from "@/Components/DashboardCards/CompaniesCard.vue";

const props = defineProps({
    title: String,
    users: Number,
    roles: Number,
    permissions: Number,
    companies: Number,
});
</script>

<template>
    
    <Head :title="props.title" />

    <app-layout>
        <div class="grid grid-cols-12 gap-8">

            <!-- USERS -->
            <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                <UsersCard :users="props.users" />
            </div>

            <!-- ROLE -->
            <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                <RolesCard :roles="props.roles" />
            </div>

            <!-- PERMISSIONS -->
            <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                <PermissionsCard :permissions="props.permissions" />
            </div>

            <!-- COMPANIES -->
            <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                <CompaniesCard :companies="props.companies" />
            </div>

        </div>
    </app-layout>
</template>

<style scoped lang="scss"></style>
