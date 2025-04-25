<script setup>
import { onMounted, reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
import CalendarService from '@/service/Calendars/Calendar/CalendarService.js';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
});

// 👇 API hívás definíció
const fetchCalendar = async (params) => {
    const response = await CalendarService.getCalendar(params);
    return response.data;
};

// 👇 Hook használata
const {
    data: calendars,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchCalendar);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    day: null
});

onMounted(fetchData);

/*
const { has } = usePermissions();

const props = defineProps({
  title: String,
  filters: Object
});

const fetchCalendar = async (params) => {
  const response = await CalendarService.getCalendar(params);
  return response.data;
};

const {
  data: calendars,
  params,
  isLoading,
  fetchData,
  onPageChange,
  clearSearch
} = useDataTableFetcher(props.filters, fetchCalendar);

const data = reactive({
  createOpen: false,
  editOpen: false,
  deleteOpen: false,
  calendar: null
});

onMounted(fetchData);
*/
</script>

<template>
  <AuthLayout>
    <Head :title="props.title" />
    <!--<div class="card">
      <DataTable
        v-if="calendars"
        :value="calendars.data"
        :rows="calendars.per_page"
        :totalRecords="calendars.total"
        :first="(calendars.current_page - 1) * calendars.per_page"
        :loading="isLoading"
        lazy paginator dataKey="id"
        @page="onPageChange"
      >
        <template #header>
          <div class="flex justify-between">
            <Button icon="pi pi-filter-slash" label="Clear" outlined @click="clearSearch" />
            <div class="font-semibold text-xl">{{ props.title }}</div>
            <div class="flex justify-end">
              <InputText v-model="params.search" placeholder="Search..." />
            </div>
          </div>
        </template>

        <Column field="id" header="ID" />
        <Column field="name" header="Name" />
        <Column field="starts_at" header="Starts At" />
        <Column field="ends_at" header="Ends At" />
        <Column field="color" header="Color" />

        <Column :exportable="false" header="Actions">
          <template #body="slotProps">
            <Button v-if="has('update calendar')" icon="pi pi-pencil" outlined rounded class="mr-2"
              @click="() => { data.editOpen = true; data.calendar = slotProps.data }" />
            <Button v-if="has('delete calendar')" icon="pi pi-trash" outlined rounded severity="danger"
              @click="() => { data.deleteOpen = true; data.calendar = slotProps.data }" />
          </template>
        </Column>
      </DataTable>
    </div>-->
  </AuthLayout>
</template>
