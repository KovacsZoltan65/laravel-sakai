<script setup>
import { ref } from "vue";
import AppMenuItem from "./AppMenuItem.vue";

const model = ref([
    {
        label: "home",
        items: [
            {
                label: "dashboard",
                icon: "pi pi-fw pi-home",
                to: "/dashboard",
            },
        ],
    },
    {
        label: "administration",
        items: [
            { label: "users", icon: "pi pi-fw pi-users", to: "/users", can: "read users" },
            { label: "roles", icon: "pi pi-fw pi-users", to: "/roles", can: "read roles" },
            { label: "permissions", icon: "pi pi-fw pi-users", to: "/permissions", can: "read permissions" },
        ],
    },
    {
        label: "system",
        items: [
            {
                label: "geo",
                items: [
                    { label: "countries", icon: "pi pi-fw pi-map-marker", to: "/countries" },
                    { label: "regions", icon: "pi pi-fw pi-map-marker", to: "/regions" },
                    { label: "cities", icon: "pi pi-fw pi-map-marker", to: "/cities" },
                ],
            },
            {
                label: "subdomain_states",
                icon: "",
                to: "/subdomain_states",
            },
        ],
    },
    {
        label: "specimens",
        items: [
            { label: "companies", icon: "pi pi-fw pi-briefcase", to: "/companies" },
            { label: "subdomains", icon: "", to: "/subdomains" },
        ],
    },
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item.label + i">
            <app-menu-item
                v-if="!item.separator"
                :item="item"
                :index="i"
            ></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
