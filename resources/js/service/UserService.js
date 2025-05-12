import BaseService from "./BaseService.js";

class UserService extends BaseService
{
    constructor()
    {
        super();
        this.url = "/users";
    }

    getUsers(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }
}

export default new UserService();