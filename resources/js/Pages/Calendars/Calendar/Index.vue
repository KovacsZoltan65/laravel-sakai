<script setup>
import { onMounted, reactive, ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermissions } from '@/composables/usePermissions';
import CalendarService from '@/service/Calendars/Calendar/CalendarService.js';
import { getActualYear } from "../../../helpers/functions.js";

import { useToast } from "primevue/usetoast";

import Holidays from "date-holidays";

import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';

import { hungarianLocale } from '@/locale/datepickerLocale';

const calendar = ref();

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object,
});

const createDialogVisible = ref(false)
const editDialogVisible = ref(false)

const newEvent = ref({ title: '', start: '', end: '' });
const editedEvent = ref({ id: null, title: '', start: '' });

const contextMenuVisible = ref(false);
const contextMenuX = ref(0);
const contextMenuY = ref(0);
const selectedEvent = ref(null);

const toast = useToast();
const showToast = (message) => {
    toast.add({
        severity: 'success',
        summary: 'Sikeres művelet',
        detail: message,
        life: 3000
    });
};

const activeFilters = ref({
    company: true,
    holidays: true,
    movedDays: true,
    birthdays: true
});

/**
 * Akkor hívódik meg, amikor egy dátumra kattintanak.
 *
 * @param {object} arg - Az arg objektum
 * @param {string} arg.dateStr - A dátum karakterláncként
 */
const handleDateClick = (arg) => {

    newEvent.value = {
        title: '',
        start: arg.dateStr,
        end: arg.dateStr
    };
    createDialogVisible.value = true;
};

/*
Nézet neve   | Mit jelent?               | Példa
dayGridMonth | Havi nézet (naptárrácsos) | Klasszikus teljes hónap
dayGridWeek  | Heti nézet (naptárrácsos) | Teljes hét rácsban
dayGridDay   | Napi nézet (naptárrácsos) | Egy nap rácsban
timeGridWeek | Heti időalapú nézet       | Órákra lebontott hét
timeGridDay  | Napi időalapú nézet       | Órákra lebontott nap
listYear     | Éves lista nézet          | Események listázva egész évre
listMonth    | Havi lista nézet          | Események listázva egy hónapra
listWeek     | Heti lista nézet          | Események listázva egy hétre
listDay      | Napi lista nézet          | Események listázva egy napra
*/
const calendarOptions = reactive({
    plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin],
    initialView: 'dayGridMonth',
    dateClick: handleDateClick,
    weekends: true,
    locale: 'hu',
    events: [
    {
            id: `birthday`,
            title: "Másnap",
            start: "2025-06-02",
            end: "2025-06-02",
            allDay: true,
            editable: true,
            color: '#28a745',
            extendedProps: {
                type: 'Születésnap',
                editable: true
            }
        }
    ],
    /**
     * A naptár fejlécében megjelen  elemek.
     *
     * @see https://fullcalendar.io/docs/headerToolbar
     */
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    /**
     * Amikor egy eventre kattintanak, akkor hívódik meg.
     *
     * @param {object} info - Az event információi
     * @param {string} info.event.id - Az event azonosítója
     * @param {string} info.event.title - Az event címe
     * @param {string} info.event.startStr - Az event kezd  dátumának karakterlánckénti megadása
     * @param {boolean} info.event.extendedProps.editable - Az event szerkeszthet ségének megadása
     */
    eventClick: (info) => {
        if (info.event.extendedProps.editable !== false) {
            editedEvent.value = {
                id: info.event.id,
                title: info.event.title,
                start: info.event.startStr
            }
            editDialogVisible.value = true;
        }
    },

    /**
     * Akkor aktiválódik, amikor egy esemény megjelenik a naptárban.
     *
     * @param {object} info - Információk a megrendezésre kerülő eseményről.
     * @param {object} info.event - Az esemény objektum részleteivel.
     * @param {string} info.event.extendedProps.type - Az esemény típusa, ha rendelkezésre áll.
     * @param {string} info.event.title - Az esemény címe.
     * @param {HTMLElement} info.el - Az esemény HTML eleme.
     */
    eventDidMount: (info) => {
        const tooltipText = info.event.extendedProps.type || info.event.title;
        info.el.setAttribute('title', tooltipText);

        info.el.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            openContextMenu(e, info.event);
        });
    },

    /**
     * Akkor aktiválódik, amikor egy eseményt áthelyeznek a naptárban.
     *
     * @param {object} info - Információk az áthelyezett eseményr  l.
     * @param {string} info.event.id - Az esemény azonosítója.
     * @param {string} info.event.startStr - Az esemény új kezd  dátumának karakterlánckénti megadása.
     */
    eventDrop: (info) => {
        const isEditable = info.event.extendedProps?.editable ?? true;
        if (!isEditable) {
            info.revert();
            return;
        }

        const movedEventId = info.event.id;
        const newStart = info.event.startStr;

        const index = calendarOptions.events.findIndex(e => e.id == movedEventId);
        if (index !== -1) {
            calendarOptions.events[index].start = newStart;
        }

        showToast('Esemény sikeresen áthelyezve!');
    },
});

const openContextMenu = (event, calendarEvent) => {
    if (calendarEvent.extendedProps.editable === false) {
        return; // Ha nem szerkeszthető, nincs menü
    }
    contextMenuX.value = event.clientX;
    contextMenuY.value = event.clientY;
    selectedEvent.value = calendarEvent;
    contextMenuVisible.value = true;
};

const closeContextMenu = () => {
    contextMenuVisible.value = false;
};

const loadHolidays = () => {
    const hd = new Holidays('hu');
    const year = new Date().getFullYear();
    const holidays = hd.getHolidays(year);

    holidays.forEach((holiday, index) => {
        calendarOptions.events.push({
            id: `holiday-${index}`,
            title: holiday.name,
            start: holiday.date,
            end: holiday.end ?? holiday.date,
            allDay: true,
            editable: false,
            color: '#28a745',
            extendedProps: {
                type: 'Állami ünnepnap',
                editable: false
            }
        });
    });
}

const loadMovedDays = () => {
    const year = getActualYear();
    const movedDays = CalendarService.getMovedDays(year);

    movedDays.forEach((day, index) => {
        const isWorkday = day.type === 'workday';
        calendarOptions.events.push({
            id: `moved-${index}`,
            title: day.title,
            start: day.start,
            end: day.end ?? day.start,
            allDay: day.allDay ?? true,
            editable: false,
            color: isWorkday ? '#dc3545' : '#fd7e14',
            extendedProps: {
                type: isWorkday ? 'Áthelyezett munkanap' : 'Áthelyezett pihenőnap',
                editable: false
            }
        });
    });
};

const loadEntityDays = () => {
    const year = getActualYear();
    const entityDays = CalendarService.getEntityDays(year);

    entityDays.forEach((day, index) => {
        calendarOptions.events.push({
            id: `entity-${index}`,
            title: day.title,
            start: day.start,
            end: day.end ?? day.start,
            allDay: true,
            editable: true,
            color: '#17a2b8',
            extendedProps: {
                type: 'Születésnap',
                editable: true
            }
        });
    });
};

const fetchCalendar = async (params) => {
    await CalendarService.getCalendar()
        .then((response) => {
            calendar.value = response.data;
            // Itt majd később map-eljük be a céges eseményeket
        })
        .catch((error) => {
            console.error("getCalendar API Error:", error);
        });
};

onMounted(() => {
    fetchCalendar();
    loadHolidays();
    loadMovedDays();
    loadEntityDays();
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
    const newId = calendarOptions.events.length + 1;
    calendarOptions.events.push({
        id: newId,
        title: newEvent.value.title,
        start: typeof newEvent.value.start === 'string' ? newEvent.value.start : newEvent.value.start.toISOString().slice(0, 19),
        end: typeof newEvent.value.end === 'string' ? newEvent.value.end : newEvent.value.end.toISOString().slice(0, 19),
        allDay: false,
        color: '#007bff', // Céges esemény színe
        extendedProps: {
            type: 'Céges esemény',
            editable: true
        }
    });
    newEvent.value = { title: '', start: '', end: '' };
    createDialogVisible.value = false;
}

const confirmDeleteEvent = () => {
    const index = calendarOptions.events.findIndex(e => e.id == editedEvent.value.id)
    if (index !== -1) {
        calendarOptions.events.splice(index, 1)
    }
    editDialogVisible.value = false
}

const editSelectedEvent = () => {
    if (selectedEvent.value) {
        editedEvent.value = {
            id: selectedEvent.value.id,
            title: selectedEvent.value.title,
            start: selectedEvent.value.startStr ?? selectedEvent.value.start
        };
        editDialogVisible.value = true;
        closeContextMenu();
    }
};

const deleteSelectedEvent = () => {
    if (selectedEvent.value) {
        const index = calendarOptions.events.findIndex(e => e.id == selectedEvent.value.id);
        if (index !== -1) {
            calendarOptions.events.splice(index, 1);
        }
        closeContextMenu();
    }
};

const filteredEvents = computed(() => {
    return calendarOptions.events.filter(event => {
        const type = event.extendedProps?.type || '';

        if (type.includes('Céges esemény') && activeFilters.value.company) return true;
        if (type.includes('Állami ünnepnap') && activeFilters.value.holidays) return true;
        if (type.includes('Áthelyezett') && activeFilters.value.movedDays) return true;
        if (type.includes('Születésnap') && activeFilters.value.birthdays) return true;

        return false;
    });
});

</script>

<template>
  <AuthLayout>
    <Head :title="props.title" />
    <Toast />

    <div class="card relative">
        <!-- CREATE BUTTON -->
        <Button
            v-if="has('create calendar')"
            icon="pi pi-plus"
            label="Új esemény"
            @click="createDialogVisible = true"
            class="mr-2"
        />

        <!-- TOGGLE WEEKENDS BUTTON -->
        <Button
            @click="toggleWeekends"
            label="Toggle Weekends"
        />

        <div class="flex gap-4 mb-4">
            <label><input type="checkbox" v-model="activeFilters.company" /> Céges események</label>
            <label><input type="checkbox" v-model="activeFilters.holidays" /> Ünnepnapok</label>
            <label><input type="checkbox" v-model="activeFilters.movedDays" /> Áthelyezett napok</label>
            <label><input type="checkbox" v-model="activeFilters.birthdays" /> Születésnapok</label>
        </div>

        <FullCalendar :options="{...calendarOptions, events: filteredEvents}" locale="hu" />

        <!-- CONTEXT MENU -->
        <div
            v-if="contextMenuVisible"
            :style="{ top: contextMenuY + 'px', left: contextMenuX + 'px' }"
            class="absolute bg-white border rounded shadow-md p-2 z-50"
            @click="closeContextMenu"
        >
            <ul class="space-y-2">
                <li class="cursor-pointer hover:text-primary" @click="editSelectedEvent">✏️ Szerkesztés</li>
                <li class="cursor-pointer hover:text-red-500" @click="deleteSelectedEvent">🗑️ Törlés</li>
            </ul>
        </div>
    </div>

    <!-- EDIT EVENT DIALOG -->
    <Dialog v-model:visible="editDialogVisible" header="Esemény szerkesztése" :modal="true" class="w-96">
        <div class="p-fluid">
            <div class="field">
                <label for="title">Cím</label>
                <InputText id="title" v-model="editedEvent.title" />
            </div>
            <div class="field">
                <label for="start">Kezdés dátuma</label>
                <DatePicker
                    id="start"
                    v-model="editedEvent.start"
                    dateFormat="yy-mm-dd"
                    hourFormat="24"
                    :locale="hungarianLocale"
                />
            </div>
        </div>

        <template #footer>
            <Button label="Törlés" icon="pi pi-trash" class="p-button-danger" @click="confirmDeleteEvent" />
            <Button label="Mégse" icon="pi pi-times" @click="editDialogVisible = false" class="p-button-text" />
            <Button label="Mentés" icon="pi pi-check" @click="saveEditedEvent" />
        </template>
    </Dialog>

    <!-- CREATE EVENT DIALOG -->
    <Dialog
        v-model:visible="createDialogVisible"
        header="Új esemény létrehozása"
        :modal="true"
        class="w-96"
    >
        <div class="p-fluid">
            <div class="field">
                <label for="newTitle">Cím</label>
                <InputText id="newTitle" v-model="newEvent.title" />
            </div>
            <div class="field">
                <label for="newStart">Kezdés</label>
                <DatePicker
                    id="newStart"
                    v-model="newEvent.start"
                    dateFormat="yy-mm-dd"
                    hourFormat="24"
                    :locale="hungarianLocale"
                />
            </div>
            <div class="field">
                <label for="newEnd">Befejezés</label>
                <DatePicker
                    id="newEnd"
                    v-model="newEvent.end"
                    dateFormat="yy-mm-dd"
                    hourFormat="24"
                    :locale="hungarianLocale"
                />
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
/* egyszerűbb context menü stílus */
ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
</style>
