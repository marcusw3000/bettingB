const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

export async function api(path, options = {}) {
    const response = await fetch(path, {
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrf(),
            ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            ...options.headers,
        },
        ...options,
    });
    const body = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(body.message || 'Não foi possível concluir esta ação.');
    return body;
}

export const credits = (value) => new Intl.NumberFormat('pt-BR', {
    maximumFractionDigits: 2,
}).format(Number(value || 0));

export const dateTime = (value) => value ? new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short', timeStyle: 'short',
}).format(new Date(value)) : '—';
