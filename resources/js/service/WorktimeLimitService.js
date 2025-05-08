import BaseService from "./BaseService.js";

class WorktimeLimitService extends BaseService {
    constructor() {
        super();
        this.url = "/worktime_limits";
    }

    getLimits(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }

    createLimit(payload)
    {
        return this.post(this.url, payload);
    }

    updateLimit(id, payload)
    {
        return this.put(`${this.url}/${id}`, payload);
    }

    deleteLimit(id)
    {
        return this.delete(`${this.url}/${id}`);
    }
}

export default new WorktimeLimitService;