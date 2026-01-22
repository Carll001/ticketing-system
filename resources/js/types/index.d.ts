import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: string;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...

    departments?: Department[]
}

export interface Task {
    id: string
    title: string
    description: string
    assigned_to: string
    creator_id: string
    type: string
    due_date: string
    created_at: string
    updated_at: string

    creator?: User
    steps?: Step[]
    assigned?: Department
}

export interface Step {
    id: string
    task_id: string
    title: string
    description: string
    assigned_to?: string
    status: string

    task: Task
    assigned: User
    fields?: Field[]
}
export interface Department {
    users: any;
    id: string
    name: string
    users?: User[]
}

export interface Field { 
    id: string;
    step_id: string;
    type: 'Checkbox' | 'Input' | 'Description';
    label: string;
    responses?: Response[]
    created_at?: string;
    updated_at?: string;
}

export interface Response {
    id: string
    response: string
    user_id: string
    created_at: string
    updated_at: string

    user: User
}

export interface Preset {
    id: string
    
}

export type BreadcrumbItemType = BreadcrumbItem;
