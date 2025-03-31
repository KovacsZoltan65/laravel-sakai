<script setup>
import { ref, computed, watch } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import CompanyService from "@/service/CompanyService";

const props = defineProps({
    show: Boolean,
    title: String,
    company: Object, // A szerkesztendő entitás adatai
});

const emit = defineEmits(['close', 'saved']);

// Űrlap adatok
const form = ref({
    name: '',
    email: '',
});

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    email: { required, email },
}));

const v$ = useVuelidate(rules, form);

// Amikor a props.company változik, töltsük be a form értékeit
watch(
    () => props.company,
    (newCompany) => {
        if (newCompany) {
            form.value = {
                name: newCompany.name || '',
                email: newCompany.email || '',
            };
            v$.value.$reset(); // Reseteljük a validációt, hogy ne legyenek előző hibák
        }
    },
    { immediate: true }
);

// Frissítés (update) művelet
const updateCompany = async () => {
    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            // A szerkesztett entitás azonosítóját props.company.id használjuk
            await CompanyService.updateCompany(props.company.id, form.value);
            emit('saved', form.value);
            closeModal();
        } catch (e) {
            console.error('Frissítés sikertelen', e);
        }
    }
};

// Modál bezárása: reseteljük a validációs állapotot, majd emitáljuk a close eseményt
const closeModal = () => {
    v$.value.$reset();
    emit('close');
};

</script>

<template>
    <Dialog 
        :visible="show" modal 
        header="Edit Company" 
        @hide="closeModal" 
        :style="{ width: '550px' }"
    >
        <div class="flex flex-col gap-6" style="margin-top: 17px;">

            <!-- NAME -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="name" class="block font-bold mb-3">
                        Name
                    </label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        fluid
                    />
                </FloatLabel>
                <Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_company_name
                </Message>
                <small class="text-red-500" v-if="v$.name.$error">
                    {{ v$.name.$errors[0].$message }}
                </small>
            </div>

            <!-- EMAIL -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="email" class="block font-bold mb-3">
                        Email
                    </label>
                    <InputText
                        id="email"
                        v-model="form.email"
                        fluid
                    />
                </FloatLabel>
                <Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_company_email
                </Message>
                <small class="text-red-500" v-if="v$.email.$error">
                    {{ v$.email.$errors[0].$message }}
                </small>
            </div>

            <!-- Gombok -->
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" severity="secondary" @click="closeModal" />
                <Button label="Update" icon="pi pi-check" @click="updateCompany" />
            </div>
        </div>
    </Dialog>
</template>
