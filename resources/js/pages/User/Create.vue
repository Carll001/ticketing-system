<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import user from '@/routes/user';
import { BreadcrumbItem, Department } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
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
    {
        name: 'can view user',
    },
    {
        name: 'can view dashboard',
    },
    {
        name: 'can view transaction',
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

const isPermissionChecked = (permissionName: string) => {
    return computed({
        get: () => form.permissions.includes(permissionName),
        set: (value: boolean) => {
            if (value) {
                if (!form.permissions.includes(permissionName)) {
                    form.permissions.push(permissionName);
                }
            } else {
                const index = form.permissions.indexOf(permissionName);
                if (index > -1) {
                    form.permissions.splice(index, 1);
                }
            }
        }
    });
};

const selectedLabel = computed(() => {
    if (form.department_id.length === 0) return "Select departments...";
    if (form.department_id.length === 1) {
        return props.departments?.find(d => d.id === form.department_id[0])?.name;
    }
    return `${form.department_id.length} departments selected`;
});

const allPermissionsSelected = computed({
    get: () => form.permissions.length === permissions.length,
    set: (value) => {
        if (value) {
            // Clear existing and add all
            form.permissions.splice(0);
            form.permissions.push(...permissions.map(p => p.name));
        } else {
            // Clear all
            form.permissions.splice(0);
        }
    }
});

const createUser = () => {
    form.post(user.store().url, {
        onSuccess: () => form.reset()
    });
};

const discardChanges = () => {
    router.visit(user.index().url)
}
</script>

<template>

    <Head title="Create User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <form @submit.prevent="createUser">
                <section class="flex justify-between items-center mb-4">
                    <div>
                        <h3>Creating New User</h3>
                    </div>
                    <div class="space-x-2">
                        <Button size="sm" type="button" variant="destructive" @click="discardChanges">Discard</Button>
                        <Button size="sm" type="submit" variant="default" :disabled="form.processing">
                            {{ form.processing ? 'Creating...' : 'Create User' }}
                        </Button>
                    </div>
                </section>

                <section class="flex flex-col lg:grid grid-cols-[3fr_2fr] gap-4">
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
                                        <Input placeholder="e.g Juan Dela Cruz" id="name" v-model="form.name" />
                                        <InputError :message="form.errors.name" />
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
                                        <Input placeholder="example@sample.com" id="email" type="email"
                                            v-model="form.email" />
                                        <InputError :message="form.errors.email" />
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
                                            <InputError :message="form.errors.password" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="password_confirmation">Confirm Password</Label>
                                        <Input id="password_confirmation" type="password"
                                            v-model="form.password_confirmation" placeholder="••••••••" />
                                            <InputError :message="form.errors.password_confirmation" />
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
                            <div class="space-y-4">
                                <div class="flex items-center gap-2 pb-4 border-b">
                                    <Checkbox type="checkbox" id="select-all" v-model="allPermissionsSelected"
                                        class="h-4 w-4 rounded border-gray-300" />
                                    <Label for="select-all" class="text-sm font-semibold cursor-pointer">
                                        Select All
                                    </Label>
                                </div>
                                <div class="columns-2 space-y-4">
                                    <div v-for="permission in permissions" :key="permission.name"
                                        class="flex items-center gap-2">
                                        <!-- <input type="checkbox" :id="permission.name" :value="permission.name"
                                            v-model="form.permissions" class="h-4 w-4 rounded border-gray-300" /> -->
                                        <Checkbox :id="permission.name"
                                            v-model="isPermissionChecked(permission.name).value"
                                            class="h-4 w-4 rounded border-gray-300" />

                                        <Label :for="permission.name"
                                            class="text-sm font-normal cursor-pointer capitalize">
                                            {{ permission.name }}
                                        </Label>
                                    </div>

                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </section>
            </form>
            <!-- <pre>{{ form }}</pre> -->
        </div>
    </AppLayout>
</template>