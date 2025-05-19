<script setup>
import { watchEffect, reactive } from "vue";
import { useForm } from "@inertiajs/vue3";

import SubdomainStateService from "@/service/Subdomains/SubdomainStateService";

const props = defineProps({
    show: Boolean,
    title: String,
});

const emit = defineEmits(["close"]);

const data = reactive({
    multipleSelect: false,
});

const form = useForm({
    name: "",
    active: 1,
});

const create = async () => {
    try {
        // Csak a form adatokat adjuk át a service-nek (sima objektumot)
        const response = await SubdomainStateService.create(form.data);

        emit("close");
        form.reset();

        console.log("Sikeres mentés", response);
    } catch(err) {
        console.error("Hiba történt:", err);
    }
}

watchEffect(() => {
    if( props.show ) {
        form.errors = {};
    }
});
/*
const selectAll = (event) => {
    if( event.target.checked === false ) {
        //
    } else {
        //
    }
};
*/
/*
const select = () => {
    if() {
        //
    } else {
        //
    }
};
*/
</script>

<template>
    <Dialog
        v-model:visible="props.show"
        position="center" modal
        :header="'Add ' + props.title"
        :style="{ width: '50rem' }"
        :closable="false"
    >
        <form @submit.prevent="create">
            <div class="flex flex-col gap-4">
                
                <div class="flex flex-col gap-2">
                    <label for="name">Name</label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="Name"
                    />
                    <small v-if="form.errors.name" class="text-red-500">{{
                        form.errors.name
                    }}</small>
                </div>

                <div class="flex justify-end gap-2">
                    <Button
                        type="button"
                        label="Cancel"
                        severity="secondary"
                        @click="emit('close')"
                    />
                    <Button type="submit" label="Save" />
                </div>

            </div>
        </form>
    </Dialog>
</template>