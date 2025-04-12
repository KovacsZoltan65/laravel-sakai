import BaseService from "../BaseService.js";

class SubdomainService extends BaseService
{
    constructor() {
        super();
        this.url = "/subdomains";
    }

    getSubdomains(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }
}

export default new SubdomainService();
