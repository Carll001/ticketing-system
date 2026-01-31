<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { ChevronsUpDown } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import user from '@/routes/user';
import { BreadcrumbItem, Department } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { cn } from '@/lib/utils';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';

const page = usePage();
const auth = computed(() => page.props.auth);
const openCreate = ref(false);
const openCombo = ref(false);
const props = defineProps<{
    departments: Department[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users Management',
        href: user.index().url,
    },
    {
        title: 'Create User',
        href: user.create().url,
    },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    // THIS IS MULTIPLE SECLECTION. IM USING UUID SO I USED STRING NOT ID 
    department_id: [] as string[],
    permissions: [] as string[],
})

const permissions = [
    {
        name: 'can create user',
    },
    {
        name: 'can edit user',
    },
    {
        name: 'can delete user',
    },
    {
        name: 'can create department',
    },
    {
        name: 'can edit department',
    },
    {
        name: 'can delete department',
    },
    {
        name: 'can create task',
    },
    {
        name: 'can edit task',
    },
    {
        name: 'can delete task',
    },
    // {
    //     name: 'can manage preset',
    // },
];
const toggleDepartment = (id: string) => {
    const index = form.department_id.indexOf(id);
    if (index > -1) {
        form.department_id.splice(index, 1); // Remove if exists
    } else {
        form.department_id.push(id); // Add if not exists
    }
}

const selectedLabel = computed(() => {
    if (form.department_id.length === 0) return "Select departments...";
    if (form.department_id.length === 1) {
        return props.departments?.find(d => d.id === form.department_id[0])?.name;
    }
    return `${form.department_id.length} departments selected`;
});
const createUser = () => {
    form.post(user.store().url, {
        onSuccess: () => form.reset()
    });
};
</script>

<template>

    <Head title="Create User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <form @submit.prevent="createUser">
                <section class="flex justify-between items-center mb-4">
                    <div>
                        <h3>Create New User</h3>
                    </div>
                    <div class="space-x-2">
                        <Button size="sm" type="button" variant="destructive">Discard</Button>
                        <Button size="sm" type="submit" variant="default" :disabled="form.processing">
                            {{ form.processing ? 'Creating...' : 'Create User' }}
                        </Button>
                    </div>
                </section>

                <section class="grid grid-cols-[3fr_2fr] gap-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>User Details</CardTitle>
                            <CardDescription>Details of user</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-6">
                                <section class="grid grid-cols-[2fr_1fr] gap-4">
                                    <div class="space-y-2">
                                        <Label for="name">Name</Label>
                                        <Input id="name" v-model="form.name" />
                                    </div>
                                    <div class="space-y-2 flex flex-col">
                                        <Label for="department">Departments</Label>
                                        <Popover v-model:open="openCombo">
                                            <PopoverTrigger as-child>
                                                <Button variant="outline" role="combobox" :aria-expanded="openCombo"
                                                    class="justify-between font-normal">
                                                    <span class="truncate">{{ selectedLabel }}</span>
                                                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="w-[200px] p-0" align="end">
                                                <Command>
                                                    <CommandInput placeholder="Search department..." />
                                                    <CommandEmpty>No department found.</CommandEmpty>
                                                    <CommandList>
                                                        <CommandGroup>
                                                            <CommandItem v-for="dept in departments" :key="dept.id"
                                                                :value="dept.name" @select="toggleDepartment(dept.id)">
                                                                <Check :class="cn(
                                                                    'mr-2 h-4 w-4',
                                                                    form.department_id.includes(dept.id) ? 'opacity-100' : 'opacity-0'
                                                                )" />
                                                                {{ dept.name }}
                                                            </CommandItem>
                                                        </CommandGroup>
                                                    </CommandList>
                                                </Command>
                                            </PopoverContent>
                                        </Popover>
                                        <InputError :message="form.errors.department_id" />
                                    </div>
                                </section>
                                <section class="gap-4 grid grid-cols-[2fr_1fr] w-full">
                                    <div class="space-y-2">
                                        <Label for="email">Email</Label>
                                        <Input id="email" type="email" v-model="form.email" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="email">Role</Label>
                                        <Select v-model="form.role">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Select a role for user" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="admin">
                                                    Admin
                                                </SelectItem>
                                                <SelectItem value="staff">
                                                    Staff
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </section>
                                <section class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="password">Password</Label>
                                        <Input id="password" type="password" v-model="form.password"
                                            placeholder="••••••••" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="password_confirmation">Confirm Password</Label>
                                        <Input id="password_confirmation" type="password"
                                            v-model="form.password_confirmation" placeholder="••••••••" />
                                    </div>
                                </section>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Permissions</CardTitle>
                            <CardDescription>User permissions</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="columns-2 space-y-4">
                                <div v-for="permission in permissions" :key="permission.name"
                                    class="flex items-center gap-2">
                                    <input type="checkbox" :id="permission.name" :value="permission.name"
                                        v-model="form.permissions" class="h-4 w-4 rounded border-gray-300" />

                                    <Label :for="permission.name" class="text-sm font-normal cursor-pointer capitalize">
                                        {{ permission.name }}
                                    </Label>
                                </div>

                            </div>
                        </CardContent>
                    </Card>
                </section>
            </form>
        </div>
        <pre>{{ form }}</pre>
    </AppLayout>
</template>