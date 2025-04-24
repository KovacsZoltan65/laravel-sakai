module.exports = {
    entity: "Calendar",
    entityPlural: "Calendars",
    namespace: "Calendars/Calendar",
    apiRoute: "api/calendars",
    permissions: ["read calendar", "create calendar", "update calendar", "delete calendar"],
    fields: [
        { name: "name", type: "string", label: "Name", searchable: true },
        { name: "starts_at", type: "datetime", label: "Start Time" },
        { name: "ends_at", type: "datetime", label: "End Time" },
        { name: "color", type: "string", label: "Color" }
    ]
};
