<script setup>
import { watchEffect } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    title: String,
    show: Boolean,
    company: Object,
});

const emit = defineEmits(["close"]);

const form = useForm({
    id: 0,
    name: "",
    email: "",
    address: "",
    phone: "",
});

const deleteCompany = async () => {
    console.log('deleteCompany');
};

watchEffect(() => {
    if( props.show ) {
        form.errors = {};
        form.name = props.company.name;
        form.email = props.company.email;
        form.phone = props.company.phone;
        form.address = props.company.address;
    }
});

</script>

<template>
    <Dialog
        v-model:visible="props.show"
        :style="{ width: '450px' }"
        header="Confirm"
        :modal="true"
    >
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl" />
            <span v-if="props.company"
            >Are you sure you want to delete
                <b>{{ props.company.name }}</b
                >?</span
            >
        </div>
        <template #footer>
            <Button
                label="No"
                icon="pi pi-times"
                text
                @click="emit('close')"
            />
            <Button
                label="Yes"
                icon="pi pi-check"
                @click="deleteCompany"
            />
        </template>
    </Dialog>
</template>
