<script setup>
import { ref, computed, watch } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import RegionService from "@/service/Geo/RegionService";

const props = defineProps({
    show: Boolean,
    title: String,
    region: Object
});

const emit = defineEmits(["close", "saved"]);

const isSaving = ref(false);

const form = ref({
    name: '',
    code: '',
    active: 1
});

const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    code: { required, minLength: minLength(3), maxLength: maxLength(10) }
}));

const v$ = useVuelidate(rules, form);

const save = async () => {};

const getBools = () => {
    return [
        { label: "NO", value: 0, },
        { label: "YES", value: 1, },
    ];
};

const closeModal = () => {
    v$.value.$reset(); // 👈 hibák törlése
    emit('close');
};

</script>

<template>
    <div>EDIT</div>
</template>