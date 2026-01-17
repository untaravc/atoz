import { defineStore } from 'pinia';

export const useConfigLoaderStore = defineStore('configLoader', {
    state: () => ({
        overlay: {
            isFullPage: false,
            loader: 'dots',
            color: '#0f172a',
            backgroundColor: '#ffffff',
            opacity: 0.7,
        },
    }),
});
