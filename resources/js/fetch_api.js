const withAuth = (headers, token) => {
    if (!token) {
        return headers;
    }

    return {
        ...headers,
        Authorization: `Bearer ${token}`,
    };
};

const request = async (method, url, data, token) => {
    const options = {
        method,
        headers: withAuth(
            {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            token
        ),
    };

    if (data !== undefined) {
        options.body = JSON.stringify(data);
    }

    const response = await fetch(url, options);
    const text = await response.text();
    const payload = text ? JSON.parse(text) : null;

    if (!response.ok) {
        const error = new Error('API request failed');
        error.status = response.status;
        error.payload = payload;
        throw error;
    }

    return payload;
};

export const apiGet = (url, token) => request('GET', url, undefined, token);
export const apiPost = (url, data, token) => request('POST', url, data, token);
export const apiPatch = (url, data, token) => request('PATCH', url, data, token);
export const apiPut = (url, data, token) => request('PUT', url, data, token);
export const apiDelete = (url, token) => request('DELETE', url, undefined, token);
