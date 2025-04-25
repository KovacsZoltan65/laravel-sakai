import BaseService from '../../BaseService.js';

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
}

export default new CalendarService();
/*
export default {
  async getCalendars(params) {
    return await axios.get(route('api/calendars.index'), { params });
  },
  async storeCalendar(data) {
    return await axios.post(route('api/calendars.store'), data);
  },
  async updateCalendar(id, data) {
    return await axios.put(route('api/calendars.update', id), data);
  },
  async deleteCalendar(id) {
    return await axios.delete(route('api/calendars.destroy', id));
  }
};
*/