import BaseService from "./BaseService.js";

class RoleService extends BaseService
{
    constructor()
    {
        super();
        this.url = "/roles";
    }

    getRoles(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }
}

export default new RoleService();