<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
    company: Object,
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    email: "",
    address: "",
    phone: "",
});

const update = () => {
    form.put(route("company.update", props.company?.id), {
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
        form.name = props.company?.name;
        form.email = props.company?.email;
        form.address = props.company?.address;
        form.phone = props.company?.phone;
    }
});
</script>

<template>
    <Dialog
        v-model:visible="props.show"
        position="top"
        modal
        :header="'Update ' + props.title"
        :style="{ width: '30rem' }"
        :closable="false"
    >
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">

                <!-- NAME -->
                <div class="flex flex-col gap-2">
                    <label for="name">Name</label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="Name"
                    />
                    <small v-if="form.errors.name" class="text-red-500">
                        {{ form.errors.name }}
                    </small>
                </div>

                <!-- EMAIL -->
                <div class="flex flex-col gap-2">
                    <label for="email">Email</label>
                    <InputText
                        id="email"
                        v-model="form.email"
                        class="flex-auto"
                        autocomplete="off"
                        placeholder="Email"
                    />
                    <small v-if="form.errors.email" class="text-red-500">
                        {{ form.errors.email }}
                    </small>
                </div>

                <!-- ADDRESS -->
                <div class="flex flex-col gap-2">
                    <label for="address">Address</label>
                    <InputText
                        id="address"
                        v-model="form.address"
                        type="text"
                        placeholder="address"
                    />
                    <small v-if="form.errors.address" class="text-red-500">
                        {{ form.errors.address }}
                    </small>
                </div>

                <!-- PHONE -->
                <div class="flex flex-col gap-2">
                    <label for="phone">Phone</label>
                    <InputText
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        placeholder="phone"
                    />
                    <small
                        v-if="form.errors.phone"
                        class="text-red-500">
                        {{ form.errors.phone }}
                    </small>
                </div>
                
                <div class="flex justify-end gap-2">
                    <Button
                        type="button"
                        label="Cancel"
                        severity="secondary"
                        @click="emit('close')"
                    ></Button>
                    
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
