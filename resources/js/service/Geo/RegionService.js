import BaseService from "./BaseService.js";

class RegionService extends BaseService
{
    constructor() {
        super();
        this.url = "/regions";
    }

    getRegions(params = {}) {
        return this.get(`${this.url}/fetch`, { params });
    }

    // Új entitás létrehozása
    createRegion(payload) {
        return this.post(this.url, payload);
    }

    // Meglévő entitás frissítése
    updateRegion(id, payload) {
        return this.put(`${this.url}/${id}`, payload);
    }

    // Entitás törlése
    deleteRegion(id) {
        return this.delete(`${this.url}/${id}`);
    }
}

export default new RegionService();