import BaseService from "./BaseService.js";

class PersonService extends BaseService
{
    constructor()
    {
        super();
    }

    url = "/persons";
    
    getPersons()
    {
        return this.get(this.url);
    }
}

export default new PersonService();