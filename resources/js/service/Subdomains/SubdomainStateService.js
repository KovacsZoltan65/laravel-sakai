import BaseService from '@/service/BaseService.js';

class SubdomainStateService extends BaseService
{
    constructor() {
        super();
        this.url = "/subdomain_states";
    }

    getStates(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }

    getState(payload)
    {
        return this.post(this.url, payload);
    }

    getStateByName(name)
    {
        return this.get(`${this.url}/name/${name}`);
    }

    createState(data)
    {
        return this.post(this.url, data);
    }

    updateState(id, payload)
    {
        return this.put(`${this.url}/${id}`, payload);
    }

    deleteState(id)
    {
        return this.delete(`${this.url}/${id}`);
    }
}

export default new SubdomainStateService();
