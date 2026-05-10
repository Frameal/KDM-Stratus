import { PageProps as InertiaPageProps } from '@inertiajs/core';

export interface User {
    id: number;
    first_name: string;
    last_name: string;
    username: string;
    email: string;
    contact_number: string;
    balance: number | string;
    role: 'customer' | 'manager' | 'hq';
    branch_id: number | null;
    email_verified_at: string;
    created_at: string;
    updated_at: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $page: import('@inertiajs/core').Page<PageProps>;
        // Tells Vue templates that route() is a valid function
        route: (name?: string, params?: any, absolute?: boolean) => string; 
    }
}

// Tells <script setup> that route() is a valid global function
declare global {
    function route(name?: string, params?: any, absolute?: boolean): string;
}