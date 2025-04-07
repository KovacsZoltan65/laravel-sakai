<script setup>
import { ref, computed } from "vue";

import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import RegionService from "@/service/Geo/RegionService";

const props = defineProps({
    show: Boolean,
    title: String,
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

const save = async () => {
    
    isSaving.value = true;

    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            await RegionService.createRegion(form.value);

            emit('saved', form.value);
            closeModal();
        } catch(error) {
            console.error('Mentés sikertelen', error);
        } finally {
            isSaving.value = false;
        }
    }
};

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
    <Dialog
        :visible="show"
        :style="{ width: '550px' }" modal
        :closable="false"
        header="Create Country"
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
                    enter_region_name
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
                    enter_region_code
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
                    enter_region_active
                </Message>
            </div>

        </div>

        <div class="flex justify-end gap-2 mt-4">
            <Button
                label="Cancel"
                severity="secondary"
                @click="closeModal"
            />
            <Button
                label="Save"
                :icon="isSaving ? 'pi pi-spin pi-spinner' : 'pi pi-check'"
                @click="save"
            />
        </div>

    </Dialog>
</template>
