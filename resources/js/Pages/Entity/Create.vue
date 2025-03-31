<script setup>
import { ref, computed } from "vue";

import useVuelidate from "@vuelidate/core";
import { helpers, maxLength, minLength, required } from "@vuelidate/validators";
import EntityService from "@/service/EntityService";

const props = defineProps({
    show: Boolean,
    title: String,
});

const emit = defineEmits(["close", "saved"]);

// Form adatok
const form = ref({
    name: '',
    email: '',
    // ide jön minden egyéb mező
});

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    email: { required, email: helpers.isEmail },
}));

const v$ = useVuelidate(rules, form);

// Mentés
const save = async () => {
    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            // Itt mehet az axios.post...
            //await axios.post('/api/entities', form.value);
            await EntityService.create(form.value); // �� itt kell a service-ben a form.value-t adni

            emit('saved', form.value);

            closeModal();
        } catch (e) {
            console.error('Mentés sikertelen', e);
        }
    }
};

const closeModal = () => {
    v$.value.$reset(); // 👈 hibák törlése
    emit('close');
};

</script>

<template>
    <Dialog
        :visible="show"
        :style="{ width: '550px' }" modal
        header="Create Entity"
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
                    enter_company_name
                </Message>
                <small class="text-red-500" v-if="v$.name.$error">
                    {{ v$.name.$errors[0].$message }}
                </small>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <Button label="Cancel" severity="secondary" @click="closeModal" />
            <Button label="Save" icon="pi pi-check" @click="save" />
        </div>

    </Dialog>

</template>
