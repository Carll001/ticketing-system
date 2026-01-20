<script setup lang="ts">
import { Form, useForm } from '@inertiajs/vue3';
import { Button } from '../ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '../ui/popover';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '../ui/command';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import InputError from '../InputError.vue';
import { computed, ref } from 'vue';
import userLink from '@/routes/user';
import { Department } from '@/types';
import { Toaster } from '../ui/sonner';
import { toast } from 'vue-sonner';

const openCreate = ref(false);
const openCombo = ref(false);

const props = defineProps<{
    departments?: Department[]
}>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    // THIS IS MULTIPLE SECLECTION. IM USING UUID SO I USED STRING NOT ID 
    department_id: [] as string[], 
})

const createUser = () => {
    form.post(userLink.store().url, {
        onSuccess: () => {
            toast.success('User created sucessfully!')
            form.reset();
            openCreate.value = false;
        }
    })
}

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
</script>
<template>
    <Dialog v-model:open="openCreate">
        <DialogTrigger as-child>
            <Button>Create User</Button>
        </DialogTrigger>
        <DialogContent>
            <form @submit.prevent="createUser" class="space-y-6">
                <DialogHeader>
                    <DialogTitle>Create User</DialogTitle>
                    <DialogDescription>Enter the details below to create a new user account.</DialogDescription>
                </DialogHeader>

                <div class="space-y-6">
                    <div class="grid grid-cols-[2fr_1fr] gap-2">
                        <div class="space-y-2">
                            <Label for="user-name">Name</Label>
                            <Input id="user-name" v-model="form.name" placeholder="John Doe" />
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
                                                <CommandItem
                                                    v-for="dept in departments"
                                                    :key="dept.id"
                                                    :value="dept.name"
                                                    @select="toggleDepartment(dept.id)"
                                                >
                                                    <Check
                                                        :class="cn(
                                                            'mr-2 h-4 w-4',
                                                            form.department_id.includes(dept.id) ? 'opacity-100' : 'opacity-0'
                                                        )"
                                                    />
                                                    {{ dept.name }}
                                                </CommandItem>
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                            <InputError :message="form.errors.department_id" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="user-email">Email</Label>
                        <Input id="user-email" v-model="form.email" placeholder="john@example.com" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="user-password">Password</Label>
                            <Input id="user-password" type="password" v-model="form.password" placeholder="••••••••" />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="space-y-2">
                            <Label for="user-confirm_password">Confirm Password</Label>
                            <Input id="user-confirm_password" type="password" v-model="form.password_confirmation"
                                placeholder="••••••••" />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="ghost" type="button" @click="form.reset()">
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>