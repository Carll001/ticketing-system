<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import Input from '@/components/ui/input/Input.vue';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import department from '@/routes/department';
import { Department, User, type BreadcrumbItem } from '@/types';
import { Form, Head, Link, useForm } from '@inertiajs/vue3';
import { Building, Pencil, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { router } from '@inertiajs/vue3';
import PermissionGuard from '@/components/PermissionGuard.vue';

// Create function
const props = defineProps<{
    departments: { data: Department[] };
    users: { data: User[] };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(
        department.index.url(),
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});

const form = useForm({
    name: '',
});

const createDepartment = () => {
    form.post(department.store().url, {
        onSuccess: () => {
            toast.success('Department created successfulluy!');
            form.reset();
            isOpen.value = false;
        },
    });
};

const isOpen = ref(false);

// Computed property to get employees per department
const employeesPerDept = computed(() => {
    const map = new Map<string, number>();
    props.users.data.forEach((user) => {
        // Assuming users have a departments relationship
        if (user.departments && Array.isArray(user.departments)) {
            user.departments.forEach((dept: any) => {
                map.set(dept.id, (map.get(dept.id) || 0) + 1);
            });
        }
    });
    return map;
});

// Edit functions
const editDialogOpen = ref(false);
const editForm = useForm({
    id: null,
    name: '',
});

const openEditDialog = (dept: any) => {
    editForm.id = dept.id;
    editForm.name = dept.name;
    editDialogOpen.value = true;
};

const updateDepartment = () => {
    editForm.put(`/department/${editForm.id}`, {
        onSuccess: () => {
            toast.success('Department updated successfully!');
            editDialogOpen.value = false;
            editForm.reset();
        },
    });
};

// delete functions

const deleteDialogOpen = ref(false);
const deleteId = ref<string | null>(null);

const confirmDelete = (id: string) => {
    deleteId.value = id;
    deleteDialogOpen.value = true;
};

const deleteDepartment = () => {
    if (!deleteId.value) return;

    form.delete(`/department/${deleteId.value}`, {
        onSuccess: () => {
            toast.success('Department deleted successfully!');
            deleteDialogOpen.value = false;
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Department Management',
        href: department.index.url(),
    },
];
</script>

<template>

    <Head title="Department" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-2 p-4">
            <!-- Create Dialog -->
            <div class="flex items-center justify-between gap-4">
                <!-- Left: Search -->
                <div class="relative w-120">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" class="pl-10" placeholder="Search..." />
                </div>

                <PermissionGuard permission="can create department">
                    <!-- Right: Create Department -->
                    <Dialog v-model:open="isOpen">
                        <DialogTrigger as-child>
                            <Button size="sm" class="w-full lg:w-auto">
                                Create Department
                            </Button>
                        </DialogTrigger>

                        <DialogContent class="sm:max-w-md">
                            <DialogHeader>
                                <DialogTitle>Create New Department</DialogTitle>
                                <DialogDescription>
                                    Add a new department to your organization
                                </DialogDescription>
                            </DialogHeader>

                            <Form @submit.prevent="createDepartment" class="space-y-5">
                                <div class="py-2">
                                    <Label for="dept-name" class="pb-2">
                                        Department Name
                                        <span class="text-red-500">*</span>
                                    </Label>
                                    <Input id="dept-name" v-model="form.name"
                                        placeholder="e.g., Marketing Department" />
                                    <InputError :message="form.errors.name" />
                                </div>

                                <DialogFooter class="gap-2">
                                    <DialogClose as-child>
                                        <Button variant="outline" class="flex-1 lg:flex-none" @click="form.reset()">
                                            Cancel
                                        </Button>
                                    </DialogClose>
                                    <Button type="submit" class="flex-1 lg:flex-none">
                                        Create Department
                                    </Button>
                                </DialogFooter>
                            </Form>
                        </DialogContent>
                    </Dialog>
                </PermissionGuard>
            </div>

            <!-- Edit Dialog -->
            <div>
                <Dialog v-model:open="editDialogOpen">
                    <DialogContent class="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Edit Department</DialogTitle>
                        </DialogHeader>

                        <form @submit.prevent="updateDepartment" class="space-y-5">
                            <Input v-model="editForm.name" placeholder="Department Name" />
                            <DialogFooter class="gap-2 border-t pt-4">
                                <DialogClose as-child>
                                    <Button variant="outline">Cancel</Button>
                                </DialogClose>
                                <Button type="submit">Update</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- delete dialog -->
            <Dialog v-model:open="deleteDialogOpen">
                <DialogContent class="sm:max-w-sm">
                    <DialogHeader>
                        <DialogTitle>Delete Department</DialogTitle>
                        <DialogDescription>
                            Are you sure you want to delete this department?
                            This action cannot be undone.
                        </DialogDescription>
                    </DialogHeader>

                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline">Cancel</Button>
                        </DialogClose>
                        <Button variant="destructive" @click="deleteDepartment">
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border shadow-sm">
                <div class="border-b p-3">
                    <h2 class="text-md">Department List</h2>
                </div>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Department</TableHead>
                            <TableHead>Employees</TableHead>
                            <TableHead class="text-end">Actions</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <!-- Show this row if no departments exist -->
                        <TableRow v-if="props.departments.data.length === 0">
                            <TableCell colspan="4" class="text-center text-gray-500">
                                No departments yet.
                            </TableCell>
                        </TableRow>

                        <!-- Otherwise, render all departments -->
                        <TableRow v-else v-for="dept in props.departments.data" :key="dept.id">
                            <TableCell>
                                <div class="flex items-center gap-3">
                                    <p class="font-medium">
                                        {{ dept.name }}
                                    </p>
                                </div>
                            </TableCell>

                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{
                                        employeesPerDept.get(dept.id) || 0
                                    }}</span>
                                    <span class="text-sm">employees</span>
                                </div>
                            </TableCell>

                            <TableCell>
                                <div class="flex justify-end gap-2">
                                    <Link :href="department.show(dept.id).url">
                                        <Button size="sm" variant="outline" class="gap-1">
                                            View
                                        </Button>
                                    </Link>
                                    <PermissionGuard permission="can edit department">
                                        <Button @click="openEditDialog(dept)" size="sm" variant="outline" class="gap-1">
                                            <Pencil class="h-3 w-3" />
                                            Edit
                                        </Button>
                                    </PermissionGuard>
                                    <PermissionGuard permission="can delete department">
                                        <Button @click="confirmDelete(dept.id)" size="sm" variant="destructive"
                                            class="gap-1">
                                            <Trash2 class="h-3 w-3" />
                                            Delete
                                        </Button>
                                    </PermissionGuard>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <!-- Pagination -->
                <div class="border-t px-6 py-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm">Showing 5 of 5 departments</p>
                        <div class="flex gap-2">
                            <Button variant="outline" size="sm" disabled>Previous</Button>
                            <Button variant="outline" size="sm" disabled>Next</Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
