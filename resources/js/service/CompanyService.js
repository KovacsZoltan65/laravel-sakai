import BaseService from "./BaseService.js";

class CompanyService extends BaseService {
    constructor() {
        super();
    }

    url = "/companies";

    getCompanies() {
        console.log('asdasd');
        let a = this.get(this.url);
        console.log('a',a);
        return a;
    }

    getCompany(id) {
        return this.get(`${this.url}/${id}`);
    }

    getCompanyByName(name) {
        return this.get(`${this.url}/name/${name}`);
    }

    createCompany(data) {
        return this.post(this.url, data);
    }

    updateCompany(id, data) {
        return this.put(`${this.url}/${id}`, data);
    }

    deleteCompanies(ids) {
        const query = ids.map(id => `ids[]=${id}`).join('&');
        return this.delete(`${this.url}?${query}`);
    }

    deleteCompany(id) {
        return this.delete(this.url + `/${id}`);
    }
}

export default new CompanyService();