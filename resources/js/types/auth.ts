export type Unit = {
    id: number;
    code: string;
    name: string;
    type: string;
};

export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    nip?: string | null;
    phone?: string | null;
    is_active: boolean;
    last_login_at: string | null;
    unit_id: number | null;
    unit?: Unit | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
    roles: string[];
    permissions: string[];
};
