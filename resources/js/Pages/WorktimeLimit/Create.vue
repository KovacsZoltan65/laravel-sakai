<script setup>
import { ref, computed } from "vue";

import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import WorktimeLimitService from '@/service/WorktimeLimitService.js';

import { helpers } from '@vuelidate/validators';

const props = defineProps({
    show: Boolean,
    title: String,
});

const emit = defineEmits(["close", "saved"]);

const isSaving = ref(false);

// Form adatok
const form = ref({
    name: '',
    company_id: null,
    start_date: null,
    end_date: null,
    active: 1
});

const isDateAfterOrEqual = (startField) =>
    helpers.withMessage('A záró dátum nem lehet korábbi, mint a kezdő dátum.',
        helpers.withParams({ type: 'isDateAfterOrEqual' }, (value, vm) => {
            const startDate = vm[startField]
            return !value || !startDate || new Date(value) >= new Date(startDate)
        })
    );

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    company_id: { required, },
    start_date: { required, },
    end_date: { required, isDateAfterOrEqual: isDateAfterOrEqual('start_date') },
    active: { required, }
}));

const v$ = useVuelidate(rules, form);

const save = async () => {
    isSaving.value = true;

    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            await WorktimeLimitService.createLimit(form.value);

            emit('saved', form.value);
            closeModal();
        } catch(e) {
            console.error('Mentés sikertelen', e);
        } finally {
            isSaving.value = false;
        }
    };
};

const closeModal = () => {
    v$.value.$reset(); // 👈 hibák törlése
    emit('close');
};

</script>

<template>
    <Dialog 
        :visible="show" :style="{ width: '550px' }" modal
        header="Create Worktime Limit"
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

            <!-- COMPANY_ID -->

            <!-- START_DATE -->

            <!-- END_DATE -->

            <!-- ACTIVE -->


        </div>

    </Dialog>
</template>