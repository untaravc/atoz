const getStoredToken = () => {
    try {
        return localStorage.getItem('auth_token');
    } catch (e) {
        return null;
    }
};

const withAuth = (headers, token) => {
    const resolvedToken = token === undefined ? getStoredToken() : token;
    if (!resolvedToken) {
        return headers;
    }

    return {
        ...headers,
        Authorization: `Bearer ${resolvedToken}`,
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
        if (response.status === 401 && !url.endsWith('/api/login')) {
            try {
                localStorage.removeItem('auth_token');
            } catch (e) {
                // ignore
            }
            if (typeof window !== 'undefined') {
                window.location.assign('/bo/401');
            }
        }
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
