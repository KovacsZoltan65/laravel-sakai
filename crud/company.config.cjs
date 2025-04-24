module.exports = {
    entity: "Company",
    entityPlural: "Companies",
    namespace: "Companies",
    route: "companies",
    apiRoute: "api/companies",
    permissions: ["read company", "create company", "update company", "delete company"],
    fields: [
        { name: "created_at", type: "datetime", label: "Created Time" },
        { name: "updated_at", type: "datetime", label: "Updated Time" },
        { name: "deleted_at", type: "datetime", label: "Deleted Time" }
    ]
};
