<script setup lang="ts">
import { cn } from '@/lib/utils';
import userLink from '@/routes/user';
import { Department, User } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import InputError from '../InputError.vue';
import { Button } from '../ui/button';
import {
    Command,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '../ui/command';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '../ui/dialog';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '../ui/popover';

const openEdit = ref(false);
const openCombo = ref(false);

const props = defineProps<{
    user: User;
    departments?: Department[];
}>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    department_id: [] as string[],
});

const createFormValue = () => {
    form.name = props.user.name;
    form.email = props.user.email;
    form.password = '';
    form.password_confirmation = '';
    form.department_id = props.user.departments?.map((d) => d.id) ?? [];
};

const toggleEdit = () => {
    createFormValue();
    openEdit.value = true;
};

const updateUser = () => {
    form.patch(userLink.update(props.user.id).url, {
        onSuccess: () => {
            toast.success('User updated successfully!');
            openEdit.value = false;
        },
    });
};

const toggleDepartment = (id: string) => {
    const index = form.department_id.indexOf(id);
    index > -1
        ? form.department_id.splice(index, 1)
        : form.department_id.push(id);
};

const selectedLabel = computed(() => {
    if (form.department_id.length === 0) return 'Select departments...';
    if (form.department_id.length === 1) {
        return props.departments?.find((d) => d.id === form.department_id[0])
            ?.name;
    }
    return `${form.department_id.length} departments selected`;
});
</script>

<template>
    <Dialog v-model:open="openEdit">
        <DialogTrigger as-child>
            <Button @click="toggleEdit" variant="secondary">Edit</Button>
        </DialogTrigger>
        <DialogContent>
            <form @submit.prevent="updateUser" class="space-y-6">
                <DialogHeader>
                    <DialogTitle>Edit User</DialogTitle>
                    <DialogDescription
                        >Update the user details below.</DialogDescription
                    >
                </DialogHeader>

                <div class="space-y-6">
                    <div class="grid grid-cols-[2fr_1fr] gap-2">
                        <div class="space-y-2">
                            <Label for="user-name">Name</Label>
                            <Input
                                id="user-name"
                                v-model="form.name"
                                placeholder="John Doe"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="flex flex-col space-y-2">
                            <Label for="department">Departments</Label>
                            <Popover v-model:open="openCombo">
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        :aria-expanded="openCombo"
                                        class="justify-between font-normal"
                                    >
                                        <span class="truncate">{{
                                            selectedLabel
                                        }}</span>
                                        <ChevronsUpDown
                                            class="ml-2 h-4 w-4 shrink-0 opacity-50"
                                        />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent
                                    class="w-[200px] p-0"
                                    align="end"
                                >
                                    <Command>
                                        <CommandInput
                                            placeholder="Search department..."
                                        />
                                        <CommandList>
                                            <CommandGroup>
                                                <CommandItem
                                                    v-for="dept in departments"
                                                    :key="dept.id"
                                                    :value="dept.name"
                                                    @select="
                                                        toggleDepartment(
                                                            dept.id,
                                                        )
                                                    "
                                                >
                                                    <Check
                                                        :class="
                                                            cn(
                                                                'mr-2 h-4 w-4',
                                                                form.department_id.includes(
                                                                    dept.id,
                                                                )
                                                                    ? 'opacity-100'
                                                                    : 'opacity-0',
                                                            )
                                                        "
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
                        <Input
                            id="user-email"
                            v-model="form.email"
                            placeholder="john@example.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid grid-cols-2 items-start gap-4">
                        <div class="flex flex-col space-y-2">
                            <Label for="user-password" class="h-5">
                                Password (leave blank to keep current)
                            </Label>

                            <Input
                                id="user-password"
                                type="password"
                                v-model="form.password"
                                placeholder="••••••••"
                            />

                            <div class="min-h-[20px]">
                                <InputError :message="form.errors.password" />
                            </div>
                        </div>

                        <div class="flex flex-col space-y-2">
                            <Label for="user-confirm_password" class="h-5">
                                Confirm Password
                            </Label>

                            <Input
                                id="user-confirm_password"
                                type="password"
                                v-model="form.password_confirmation"
                                placeholder="••••••••"
                            />

                            <div class="min-h-[20px]">
                                <InputError
                                    :message="form.errors.password_confirmation"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button
                            variant="ghost"
                            type="button"
                            @click="form.reset()"
                        >
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
