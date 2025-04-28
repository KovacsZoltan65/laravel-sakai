import BaseService from '../../BaseService.js';

const movedDaysByYear = {
  2025: [
    {
      title: "Áthelyezett pihenőnap (május 2.)",
      start: "2025-05-02",
      allDay: true,
      type: "workday",
    },
    {
      title: "Áthelyezett munkanap (május 17.)",
      start: "2025-05-17",
      allDay: true,
      type: "dayoff",
    }
  ],
};

const entityDays = {
    2025: [
        {
            id: `birthday`,
            title: "Szülinap",
            start: "2025-06-01",
            end: "2025-06-01",
            allDay: true,
            editable: true,
            color: '#28a745',
            extendedProps: {
                type: 'Születésnap',
                editable: true
            }
        }
    ]
};

class CalendarService extends BaseService
{
    constructor()
    {
        super();
        this.url = "/calendar";
    }

    getCalendar(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }

    getMovedDays(year)
    {
        return movedDaysByYear[year] || [];
    }

    getEntityDays(year)
    {
        return entityDays[year] || [];
    }
}

export default new CalendarService();
