<script setup>
import { onMounted, reactive, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
import CalendarService from '@/service/Calendars/Calendar/CalendarService.js';

import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';

const calendar = ref();

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
});

// Eseménykezelő függvény
const handleDateClick = (arg) => {
    console.log('date click! ', arg)
}

const calendarOptions = reactive({
    plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin],
    //initialView: 'dayGridMonth',
    initialView: 'listMonth',
    dateClick: handleDateClick,
    weekends: true,
    locale: 'hu',
    events: [
        // ünnepnapok
        { id:  0, title: 'Szilveszter', date: '2025-01-01' }, // Szilveszter
        { id:  1, title: 'Nemzeti ünnepnap', date: '2025-03-15' }, // Nemzeti ünnepnap
        { id:  2, title: 'Húsvét csütörtök', date: '2025-04-18' }, // Húsvét csütörtök
        { id:  3, title: 'Húsvét szombat', date: '2025-04-20' }, // Húsvét szombat
        { id:  4, title: 'Húsvét vasárnap', date: '2025-04-21' }, // Húsvét vasárnap
        { id:  5, title: 'Május 1.', date: '2025-05-01' }, // Május 1.
        { id:  6, title: 'Pünkösd vasárnap', date:'2025-06-08' }, // Pünkösd vasárnap
        { id:  7, title: 'Pünkösd hete', date: '2025-06-09' }, // Pünkösd hétf
        { id:  8, title: 'Szent István király ünnepe', date: '2025-08-20' }, // Szent István király ünnepe
        { id:  9, title: '1956-os forradalom emleknapja', date:'2025-10-23' }, // Október 23. - 1956-os forradalom emléknapja
        { id: 10, title: 'Mindenszentek napja', date:'2025-11-01' }, // Mindenszentek napja
        { id: 11, title: 'Karácsony els napja', date:'2025-12-25' }, // Karácsony els napja
        { id: 12, title: 'Karácsony második napja', date:'2025-12-26' }, // Karácsony második napja

        { id: 13, title: 'holiday', start: '2025-04-07', end: '2025-04-11' },
        // áthelyezett pihenőnapok
        {id: 14, title: '2025-05-02 -> 2025-05-17', date: '2025-05-02'},

        // áthelyezett munkanapok
        {id: 15, title: '2025-05-02 -> 2025-05-17', date: '2025-05-17'},

        { 
            id: 16, 
            title: 'meeting 1', 
            start: '2025-04-10 11:30:00',
            end: '2025-04-10 12:00:00'
        },

        { 
            id: 17, 
            title: 'meeting 2', 
            start: '2025-04-10 12:30:00',
            end: '2025-04-10 13:00:00'
        },

        { 
            id: 18, 
            title: 'meeting 3', 
            start: '2025-04-10 13:30:00',
            end: '2025-04-10 14:00:00',
        },

    ],
    eventColor: '#378006', // green
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    }
});

const fetchCalendar = async (params) => {

    await CalendarService.getCalendar()
        .then((response) => {
            calendar.value = response.data;
        })
        .catch((error) => {
            console.error("getCalendar API Error:", error);
        });
};

onMounted(fetchCalendar);

const toggleWeekends = () => {
    calendarOptions.weekends = !calendarOptions.weekends;
};

</script>

<template>
  <AuthLayout>
    <Head :title="props.title" />

        <div class="card">
            <!-- CREATE MODAL -->

            <!-- EDIT MODAL -->

            <!-- DELETE MODAL -->

            <!-- CREATE BUTTON -->
            <Button 
                v-if="has('create calendar')" 
                icon="pi pi-plus" 
                label="Create" 
                @click="data.createOpen = true"
                class="mr-2"
            />

            <!-- REFRESH BUTTON -->
             <Button 
                @click="toggleWeekends"
                label="Toggle Weekends"
             />

            <FullCalendar :options="calendarOptions" />

        </div>
  </AuthLayout>
</template>

<style scoped>

</style>
