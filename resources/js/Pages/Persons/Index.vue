<script setup>
import { Head } from "@inertiajs/vue3";
import AuthLayout from "../../Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    title: String,
    content: String,
    perPage: Number,
});

</script>

<template>
    <AuthLayout>
        <Head :title="props.title" />

        

    </AuthLayout>
</template>