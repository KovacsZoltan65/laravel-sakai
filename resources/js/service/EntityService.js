import BaseService from "./BaseService.js";

class EntityService extends BaseService
{
    constructor()
    {
        super();
        this.url = "/entities";
    }

    // Az entitások listájának lekérése (a fetch endpoint-ot használja)
    getEntities(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }

    // Új entitás létrehozása
    createEntity(payload) {
        return this.post(this.url, payload);
    }

    // Meglévő entitás frissítése
    updateEntity(id, payload) {
        return this.put(`${this.url}/${id}`, payload);
    }

    // Entitás törlése
    deleteEntity(id) {
        return this.delete(`${this.url}/${id}`);
    }
}

export default new EntityService();
