import BaseService from "./BaseService.js";

class CompanyService extends BaseService
{
    constructor() {
        super();
        this.url = "/companies";
    }

    getCompanies(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }

    getCompany(payload)
    {
        return this.post(this.url, payload);
    }

    getCompanyByName(name)
    {
        return this.get(`${this.url}/name/${name}`);
    }

    createCompany(data)
    {
        return this.post(this.url, data);
    }

    updateCompany(id, payload) {
        return this.put(`${this.url}/${id}`, payload);
    }

    deleteCompany(id) {
        return this.delete(`${this.url}/${id}`);
    }
}

export default new CompanyService();
