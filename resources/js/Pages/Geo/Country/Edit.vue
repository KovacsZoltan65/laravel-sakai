<script setup>
import { ref, computed, watch } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import CountryService from "@/service/Geo/CountryService.js";

const props = defineProps({
    show: Boolean,
    title: String,
    country: Object, // A szerkesztendő ország adatai
});

const emit = defineEmits(["close", "saved"]);

// Form adatok
const form = ref({
    name: '',
    code: '',
    active: 1
});

const isUpdating = ref(false);

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    code: { required, minLength: minLength(3), maxLength: maxLength(10) }
}));

const v$ = useVuelidate(rules, form);

// Amikor a props.entity változik, töltsük be a form értékeit
watch(
    () => props.country,
    (newCountry) => {
        if (newCountry) {
            form.value = {
                name: newCountry.name || '',
                code: newCountry.code || '',
                active: newCountry.active || 1,
            };
            v$.value.$reset(); // Reseteljük a validációt, hogy ne legyenek előző hibák
        }
    },
    { immediate: true }
);

// Frissítés (update) művelet
const updateCountry = async () => {

    isUpdating.value = true;

    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            // A szerkesztett entitás azonosítóját props.entity.id használjuk
            await CountryService.updateCountry(props.country.id, form.value);
            emit('saved', form.value);
            closeModal();
        } catch (e) {
            console.error('Frissítés sikertelen', e);
        } finally {
            isUpdating.value = false;
        }
    }
};

const closeModal = () => {
    v$.value.$reset(); // 👈 hibák törlése
    emit('close');
};

const getBools = () => {
    return [
        { label: "NO", value: 0, },
        { label: "YES", value: 1, },
    ];
};

</script>

<template>
    <Dialog
        :visible="show" modal
        :style="{ width: '550px' }"
        header="Edit Country"
        @hide="closeModal"
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
                enter_country_name
            </Message>
            <small class="text-red-500" v-if="v$.name.$error">
                {{ v$.name.$errors[0].$message }}
            </small>
        </div>

        <!-- CODE -->
        <div class="flex flex-col grow basis-0 gap-2">
            <FloatLabel variant="on">
                <label for="code" class="block font-bold mb-3">
                    code
                </label>
                <InputText
                    id="code"
                    v-model="form.code"
                    fluid
                />
            </FloatLabel>
            <Message
                size="small"
                severity="secondary"
                variant="simple"
            >
                enter_country_code
            </Message>
            <small class="text-red-500" v-if="v$.code.$error">
                {{ v$.code.$errors[0].$message }}
            </small>
        </div>

        <!-- ACTIVE -->
        <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel>
                    <label for="active" class="block font-bold mb-3">
                        active
                    </label>
                    <Select
                        id="active"
                        v-model="form.active"
                        :options="getBools()"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Active" fluid
                    />
                </FloatLabel>
                <Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_subdomain_active
                </Message>
            </div>

        <!-- Gombok -->
        <div class="flex justify-end gap-2 mt-4">
            <Button
                label="Cancel"
                severity="secondary"
                @click="closeModal"
            />
            <Button
                label="Update"
                :icon="isUpdating ? 'pi pi-spin pi-spinner' : 'pi pi-check'"
                @click="updateCountry"
            />
        </div>
    </div>

    </Dialog>
</template>
