<script setup>
import { useForm } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
  show: Boolean,
  title: String,
  calendar: Object
});

const emit = defineEmits(['close', 'deleted']);
const toast = useToast();

const form = useForm({});

const handleDelete = () => {
  form.delete(route('api/calendars.destroy', props.calendar.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Sikeres törlés', life: 3000 });
      emit('close');
      emit('deleted');
    }
  });
};
</script>

<template>
  <Dialog v-model:visible="props.show" modal :header="props.title" :style="{ width: '30rem' }" @hide="emit('close')">
    <div class="confirmation-content">
      <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
      <span>Biztosan törölni szeretnéd ezt a calendar elemet?</span>
    </div>

    <template #footer>
      <Button label="Mégse" icon="pi pi-times" text @click="emit('close')" />
      <Button label="Törlés" icon="pi pi-trash" text severity="danger" @click="handleDelete" />
    </template>
  </Dialog>
</template>