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

const createDialogVisible = ref(false)
const newEvent = ref({
    title: '',
    start: '',
    end: ''
});

// Új változók a modál kezeléséhez
const editDialogVisible = ref(false)
const editedEvent = ref({
    id: null,
    title: '',
    start: ''
});

// Eseménykezelő függvény
const handleDateClick = (arg) => {
    console.log('date click! ', arg)
};

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
        { id: 16, title: 'meeting 1', start: '2025-04-10 11:30:00', end: '2025-04-10 12:00:00' },
        { id: 17, title: 'meeting 2', start: '2025-04-10 12:30:00', end: '2025-04-10 13:00:00' },
        { id: 18, title: 'meeting 3', start: '2025-04-10 13:30:00', end: '2025-04-10 14:00:00', },
    ],
    eventColor: '#378006', // green
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    eventClick: (info) => {
        editedEvent.value = {
            id: info.event.id,
            title: info.event.title,
            start: info.event.startStr // ISO formátumban
        }
        editDialogVisible.value = true
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

const saveEditedEvent = () => {
    const index = calendarOptions.events.findIndex(e => e.id == editedEvent.value.id)
    if (index !== -1) {
        calendarOptions.events[index].title = editedEvent.value.title
        calendarOptions.events[index].start = typeof editedEvent.value.start === 'string' ? editedEvent.value.start : editedEvent.value.start.toISOString().slice(0, 19)
    }
    editDialogVisible.value = false
}

const saveNewEvent = () => {
    const newId = calendarOptions.events.length + 1; // Gyors ID generálás
    calendarOptions.events.push({
        id: newId,
        title: newEvent.value.title,
        start: typeof newEvent.value.start === 'string' ? newEvent.value.start : newEvent.value.start.toISOString().slice(0, 19),
        end: typeof newEvent.value.end === 'string' ? newEvent.value.end : newEvent.value.end.toISOString().slice(0, 19)
    });
  
    // Új esemény után tisztítjuk az űrlapot
    newEvent.value = { title: '', start: '', end: '' };
    createDialogVisible.value = false;
}

const confirmDeleteEvent = () => {
    const index = calendarOptions.events.findIndex(e => e.id == editedEvent.value.id)
    if (index !== -1) {
        calendarOptions.events.splice(index, 1) // töröljük a tömbből
    }
    editDialogVisible.value = false
}

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
            label="Új esemény" 
            @click="createDialogVisible = true"
            class="mr-2"
        />


        <!-- REFRESH BUTTON -->
            <Button 
            @click="toggleWeekends"
            label="Toggle Weekends"
            />

        <FullCalendar :options="calendarOptions" />

    </div>

    <Dialog v-model:visible="editDialogVisible" header="Esemény szerkesztése" :modal="true" class="w-96">
        <div class="p-fluid">
            <div class="field">
                <label for="title">Cím</label>
                <InputText id="title" v-model="editedEvent.title" />
            </div>
            <div class="field">
                <label for="start">Kezdés dátuma</label>
                <Calendar id="start" v-model="editedEvent.start" showTime dateFormat="yy-mm-dd" hourFormat="24" />
            </div>
        </div>

        <template #footer>
            <Button label="Törlés" icon="pi pi-trash" class="p-button-danger" @click="confirmDeleteEvent" />
            <Button label="Mégse" icon="pi pi-times" @click="editDialogVisible = false" class="p-button-text" />
            <Button label="Mentés" icon="pi pi-check" @click="saveEditedEvent" />
        </template>
    </Dialog>

    <Dialog v-model:visible="createDialogVisible" header="Új esemény létrehozása" :modal="true" class="w-96">
        <div class="p-fluid">
            <div class="field">
                <label for="newTitle">Cím</label>
                <InputText id="newTitle" v-model="newEvent.title" />
            </div>
            <div class="field">
                <label for="newStart">Kezdés</label>
                <Calendar id="newStart" v-model="newEvent.start" showTime dateFormat="yy-mm-dd" hourFormat="24" />
            </div>
            <div class="field">
                <label for="newEnd">Befejezés</label>
                <Calendar id="newEnd" v-model="newEvent.end" showTime dateFormat="yy-mm-dd" hourFormat="24" />
            </div>
        </div>

        <template #footer>
            <Button label="Mégse" icon="pi pi-times" @click="createDialogVisible = false" class="p-button-text" />
            <Button label="Létrehozás" icon="pi pi-check" @click="saveNewEvent" />
        </template>
    </Dialog>


  </AuthLayout>
</template>

<style scoped>

</style>
