import BaseService from "../BaseService.js";

class CityService extends BaseService
{
    constructor() {
        super();
        this.url = "/cities";
    }

    getCities(params = [])
    {
        return this.get(`${this.url}/fetch`, { params });
    }

    createCity(payload)
    {
        return this.post(this.url, payload);
    }

    updateCity(id, payload) {
        return this.put(`${this.url}/${id}`, payload);
    }

    // Entitás törlése
    deleteCity(id) {
        return this.delete(`${this.url}/${id}`);
    }
}

export default new CityService();