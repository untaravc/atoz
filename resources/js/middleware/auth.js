export const AUTH_TOKEN_KEY = 'auth_token';

export const getAuthToken = () => {
    try {
        return localStorage.getItem(AUTH_TOKEN_KEY);
    } catch (e) {
        return null;
    }
};

export const clearAuthToken = () => {
    try {
        localStorage.removeItem(AUTH_TOKEN_KEY);
    } catch (e) {
        // ignore
    }
};

export const authGuard = (to) => {
    const publicNames = new Set(['login', 'unauthorized']);
    if (publicNames.has(to.name)) {
        return true;
    }

    const token = getAuthToken();
    if (!token) {
        return {
            name: 'unauthorized',
            query: { redirect: to.fullPath },
        };
    }

    return true;
};

