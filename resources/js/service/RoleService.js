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

    getRole(payload)
    {
        return this.post(this.url, payload);
    }

    getRoleByName(name)
    {
        return this.get(`${this.url}/name/${name}`);
    }

    createRole(data)
    {
        return this.post(this.url, data);
    }

    updateRole(id, payload) {
        return this.put(`${this.url}/${id}`, payload);
    }

    deleteRole(id) {
        return this.delete(`${this.url}/${id}`);
    }
}

export default new RoleService();