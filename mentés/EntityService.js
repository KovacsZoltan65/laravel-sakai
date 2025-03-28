import BaseService from './BaseService.js';

class EntityService extends BaseService 
{
    constructor() {
        super();
    }

    url = "/entities";

    getEntities()
    {
        return this.get(this.url);
    }
}

export default new EntityService();