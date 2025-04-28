export function formatCurrency(value)
{
    if (!value) return;

    return value.toLocaleString("en-US", {
        style: "currency",
        currency: "USD",
    });
}

export function formatDate(date)
{
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(date).toLocaleString(undefined, options);
}

export function createId()
{
    return Array.from({ length: 5 }, () =>
        String.fromCharCode(Math.floor(Math.random() * 62) + 48)
    ).join("");
};

export function getActualYear()
{
    return new Date().getFullYear();
}
