import BaseService from "../BaseService.js";

class CountryService extends BaseService
{
    constructor() {
        super();
        this.url = "/countries";
    }

    getCountries(params = [])
    {
        return this.get(`${this.url}/fetch`, { params });
    }

    createCountry(payload)
    {
        return this.post(this.url, payload);
    }

    updateCountry(id, payload) {
        return this.put(`${this.url}/${id}`, payload);
    }

    updateAssignedRegions(id, payload) {
        //
    }

    // Entitás törlése
    deleteCountry(id) {
        return this.delete(`${this.url}/${id}`);
    }
}

export default new CountryService();
