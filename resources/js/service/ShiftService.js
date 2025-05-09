import BaseService from "./BaseService.js";

class ShiftService extends BaseService {
    constructor() {
        super();
        this.url = "/shifts";
    }

    getShifts(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }

    // Új entitás létrehozása
    createShift(payload) {
        return this.post(this.url, payload);
    }

    // Meglévő entitás frissítése
    updateShift(id, payload) {
        return this.put(`${this.url}/${id}`, payload);
    }

    // Entitás törlése
    deleteShift(id) {
        return this.delete(`${this.url}/${id}`);
    }
}

export default new ShiftService();