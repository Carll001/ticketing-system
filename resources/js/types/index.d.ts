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
  T extends Record<string, unknown> = Record<string, unknown>
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
  role: 'superadmin' | 'admin' | 'staff';
  [key: string]: unknown;

  departments?: Department[];
}

export interface Task {
  id: string;
  title: string;
  description: string;
  assigned_to: string;
  creator_id: string;
  type: string;
  due_date: string;
  created_at: string;
  updated_at: string;

  creator?: User;
  steps?: Step[];
  assigned?: Department | User; // Optional: could be a user or department
}

export interface Proof {
  id: string;
  description?: string;
  attachments?: Attachment[];
  user?: User;
}

export interface Attachment {
  id: string;
  original_name?: string;
  path?: string;
  mime?: string;
  size?: number;
  url: string; // computed in Vue from backend path
}

export interface Step {
  id: string;
  task_id: string;
  title: string;
  description?: string;
  assigned_to?: string;
  status: string;

  task: Task;
  assigned: User;
  fields?: Field[];

  // ✅ Add proofs here
  proofs?: Proof[];
}

export interface Department {
  id: string;
  name: string;
  users?: User[];
}

export interface Field { 
  id: string;
  step_id: string;
  type: 'Checkbox' | 'Input' | 'Description';
  label: string;
  responses?: Response[];
  created_at?: string;
  updated_at?: string;
}

export interface Response {
  id: string;
  response: string;
  user_id: string;
  created_at: string;
  updated_at: string;

  user: User;
}

export interface Preset {
    id: string
    name: string
    description: string
    has_cost: boolean

    fields: Field[]
}

export interface Transaction {
  id: string
  content: string
  transaction_number: string

  user?: User
  task?: Task
  step?: Step
  department?: Department

  created_at: string
  updated_at: string
}

export type BreadcrumbItemType = BreadcrumbItem;
