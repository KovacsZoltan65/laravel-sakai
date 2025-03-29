<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
});

const create = () => {
    form.post(route("entity.store"), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};

watchEffect(() => {
    if (props.show) {
        form.errors = {};
    }
});
</script>

<template>
    <Dialog
        v-model:visible="props.show"
        position="center"
        modal
        :header="'Add ' + props.title"
        :style="{ width: '30rem' }"
        :closable="false"
    >
        <form @submit.prevent="create">
            <div class="flex flex-col gap-4">

                <!-- Name -->
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

                <!-- Email -->
                <div class="flex flex-col gap-2">
                    <label for="email">Email</label>
                    <InputText
                        id="email"
                        v-model="form.email"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="Email"
                    />
                    <small v-if="form.errors.email" class="text-red-500">{{
                        form.errors.email
                    }}</small>
                </div>

                <!-- Start Date -->
                <div class="flex flex-col gap-2">
                    <label for="start_date">Start Date</label>
                    <InputText
                        id="start_date"
                        v-model="form.start_date"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="Start Date"
                    />
                    <small v-if="form.errors.start_date" class="text-red-500">{{
                        form.errors.start_date
                    }}</small>
                </div>

                <!-- End Date -->
                <div class="flex flex-col gap-2">
                    <label for="end_date">End Date</label>
                    <InputText
                        id="end_date"
                        v-model="form.end_date"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="End Date"
                    />
                    <small v-if="form.errors.end_date" class="text-red-500">{{
                        form.errors.end_date
                    }}</small>
                </div>

                <!-- Last Export -->
                <div class="flex flex-col gap-2">
                    <label for="last_export">Last Export</label>
                    <InputText
                        id="last_export"
                        v-model="form.last_export"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="Last Export"
                    />
                    <small v-if="form.errors.last_export" class="text-red-500">{{
                        form.errors.last_export
                    }}</small>
                </div>

                <!-- Company -->
                <div class="flex flex-col gap-2">
                    <label for="company_id">Company</label>
                    <InputText
                        id="company_id"
                        v-model="form.company_id"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="Company"
                    />
                    <small v-if="form.errors.company_id" class="text-red-500">{{
                        form.errors.company_id
                    }}</small>
                </div>

                <!-- User -->
                <div class="flex flex-col gap-2">
                    <label for="user_id">User</label>
                    <InputText
                        id="user_id"
                        v-model="form.user_id"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="Last Export"
                    />
                    <small v-if="form.errors.user_id" class="text-red-500">{{
                        form.errors.user_id
                    }}</small>
                </div>

                <!-- Active -->
                <div class="flex flex-col gap-2">
                    <label for="active">Active</label>
                    <InputText
                        id="active"
                        v-model="form.active"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="Active"
                    />
                    <small v-if="form.errors.active" class="text-red-500">{{
                        form.errors.active
                    }}</small>
                </div>

                <div class="flex justify-end gap-2">
                    
                    <Button
                        type="button"
                        label="Cancel"
                        severity="secondary"
                        @click="emit('close')"
                    ></Button>

                    <Button type="submit" label="Save"></Button>

                </div>
            </div>
        </form>
    </Dialog>
</template>
