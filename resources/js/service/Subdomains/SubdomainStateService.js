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
}

export default new SubdomainStateService();
