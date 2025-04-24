module.exports = {
    entity: "Calendar",
    entityPlural: "Calendars",
    namespace: "Calendars/Calendar",
    apiRoute: "api/calendars",
    permissions: ["read calendar", "create calendar", "update calendar", "delete calendar"],
    fields: [
        { name: "date", type: "date", label: "Name", searchable: true },
        { name: "created_at", type: "datetime", label: "Created Time" },
        { name: "updated_at", type: "datetime", label: "Updated Time" },
        { name: "deleted_at", type: "datetime", label: "Deleted Time" }
    ]
};
