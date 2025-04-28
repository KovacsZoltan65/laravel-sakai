<script setup>
import { onMounted, reactive, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
import CalendarService from '@/service/Calendars/Calendar/CalendarService.js';

import Holidays from "date-holidays";

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
    events: [],
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
    },
    eventDidMount: function(info) {
        const tooltipText = info.event.extendedProps.type || info.event.title;
        info.el.setAttribute('title', tooltipText);
    }
});

const loadHolidays = () => {
    const hd = new Holidays('hu');  // Magyarország
    const year = new Date().getFullYear();
    const holidays = hd.getHolidays(year);

    holidays.forEach((holiday, index) => {
        calendarOptions.events.push({
            id: `holiday-${index}`,
            title: holiday.name,
            start: holiday.date,
            end: holiday.end ?? holiday.date, // ha nincs end, akkor starttal megegyező
            editable: false, // <-- NEM lehessen áthelyezni/szerkeszteni
            allDay: true, // ünnepnapok jellemzően egész naposak
            color: '#28a745', // Zöld - állami ünnepnap
            extendedProps: {
                type: 'Állami ünnepnap'
            }
        });
    });
}

const loadMovedDays = () => {
    const year = new Date().getFullYear();
    const movedDays = CalendarService.getMovedDays(year);

    movedDays.forEach((day, index) => {
        const isWorkday = day.type === 'workday';
        calendarOptions.events.push({
            id: `moved-${index}`,
            title: day.title,
            start: day.start,
            end: day.end ?? day.start,
            allDay: day.allDay ?? true,
            editable: false, // <-- Nem módosítható
            color: isWorkday ? '#dc3545' : '#fd7e14', // Piros a munkanap, narancs a pihenőnap
            extendedProps: {
                type: isWorkday ? 'Áthelyezett munkanap' : 'Áthelyezett pihenőnap'
            }
        });
    });
};

const fetchCalendar = async (params) => {

    await CalendarService.getCalendar()
        .then((response) => {
            calendar.value = response.data;
        })
        .catch((error) => {
            console.error("getCalendar API Error:", error);
        });
};

onMounted(() => {
    fetchCalendar();
    loadHolidays(); // <-- itt töltjük be az ünnepnapokat is
    loadMovedDays(); // áthelyezett napok
});

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
