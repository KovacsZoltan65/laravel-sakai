<script setup>
import { ref, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
  show: Boolean,
  title: String
});

const emit = defineEmits(['close', 'saved']);
const toast = useToast();

const form = useForm({
  name: '',
  starts_at: '',
  ends_at: '',
  color: ''
});

const handleSubmit = () => {
  form.post(route('api/calendars.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Sikeres mentés', life: 3000 });
      emit('close');
      emit('saved');
    }
  });
};
</script>

<template>
  <Dialog v-model:visible="props.show" modal header="Új Calendar" :style="{ width: '40rem' }" @hide="emit('close')">
    <form @submit.prevent="handleSubmit" class="p-fluid">
      <div class="field">
        <label for="name">Name</label>
        <InputText id="name" v-model="form.name" />
      </div>
      <div class="field">
        <label for="starts_at">Starts At</label>
        <Calendar id="starts_at" v-model="form.starts_at" showTime dateFormat="yy-mm-dd" />
      </div>
      <div class="field">
        <label for="ends_at">Ends At</label>
        <Calendar id="ends_at" v-model="form.ends_at" showTime dateFormat="yy-mm-dd" />
      </div>
      <div class="field">
        <label for="color">Color</label>
        <InputText id="color" v-model="form.color" />
      </div>
      <Button type="submit" label="Save" class="mt-3" />
    </form>
  </Dialog>
</template>