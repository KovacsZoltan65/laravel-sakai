<script setup>
import { ref, computed } from "vue";

import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import ShiftService from "@/service/ShiftService";

const props = defineProps({
    show: Boolean,
    title: String,
});

const emit = defineEmits(["close", "saved"]);

// Form adatok
const form = ref({
    name: '',
    code: '',
    start_time: '',
    end_time: '',
    active: 1
    // ide jön minden egyéb mező
});

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    code: { required, minLength: minLength(3), maxLength: maxLength(8) },
    start_time: { required },
    end_time: { required }
}));

const v$ = useVuelidate(rules, form);

const save = async () => {
    v$.value.$touch();
    if(!v$.value.$invalid) {
        try {
            await ShiftService.createShift(form.value);

            emit('saved', form.value);
            closeModal();
        } catch( e ) {
            console.error('Mentés sikertelen', e);
        }
    }
};

const closeModal = () => {
    v$.value.$reset(); // 👈 hibák törlése
    resetForm(); // 👈 form törlése
    emit('close');
};

const getBools = () => {
    return [
        { label: "NO", value: 0, },
        { label: "YES", value: 1, },
    ];
};

const resetForm = () => {
    form.value = {
        name: '',
        code: '',
        start_time: '',
        end_time: '',
        active: 1
    };
}

</script>

<template>
    <Dialog 
        :visible="show"
        :style="{ width: '550px' }" modal
        :closable="false"
        header="Create Shift"
        @hide="closeModal"
        position="top"
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
                    enter_shift_name
                </Message>
                <small class="text-red-500" v-if="v$.name.$error">
                    {{ v$.name.$errors[0].$message }}
                </small>
            </div>

            <!-- CODE -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="code" class="block font-bold mb-3">
                        Code
                    </label>
                    <InputText
                        id="name"
                        v-model="form.code"
                        fluid
                    />
                </FloatLabel>
                <Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_shift_code
                </Message>
                <small class="text-red-500" v-if="v$.code.$error">
                    {{ v$.code.$errors[0].$message }}
                </small>
            </div>

            <!-- START_TIME -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="start_time" class="block font-bold mb-3">
                        start_time
                    </label>
                    <DatePicker
                        id="start_time" timeOnly hourFormat="24" fluid
                        v-model="form.start_time"
                        dateFormat="yy-mm-dd"
                    />
                </FloatLabel>
                <Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_shift_start_time
                </Message>
                <small class="text-red-500" v-if="v$.start_time.$error">
                    {{ v$.start_time.$errors[0].$message }}
                </small>
            </div>

            <!-- END_TIME -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="end_time" class="block font-bold mb-3">
                        end_time
                    </label>
                    <DatePicker
                        id="start_time" timeOnly hourFormat="24" fluid
                        v-model="form.end_time"
                        dateFormat="yy-mm-dd"
                    />
                </FloatLabel>
                <Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_shift_end_time
                </Message>
                <small class="text-red-500" v-if="v$.end_time.$error">
                    {{ v$.end_time.$errors[0].$message }}
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

        </div>

        <div class="flex justify-end gap-2 mt-4">
            <Button 
                label="Cancel" 
                severity="secondary" 
                @click="closeModal"
            />
            <Button 
                label="Save" 
                icon="pi pi-check" 
                @click="save"
            />
        </div>

    </Dialog>
</template>