<script setup lang="ts">
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

import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import { Head, useForm } from '@inertiajs/vue3';
import { Building, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';


// Create function
const props = defineProps<{
    departments: any[];
}>();

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
const deleteId = ref<number | null>(null);

const confirmDelete = (id: number) => {
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
</script>

<template>
    <Head title="Department" />

    <AppLayout>
        <div class="min-h-screen">
            <div class="p-6 lg:p-8">
                <div class="mb-8 grid gap-6 lg:grid-cols-3">
                    <!-- Create Dialog -->
                    <div class="flex items-end">
                        <Dialog v-model:open="isOpen">
                            <DialogTrigger as-child>
                                <Button class="w-full lg:w-auto">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Create Department
                                </Button>
                            </DialogTrigger>

                            <DialogContent class="sm:max-w-md">
                                <DialogHeader>
                                    <DialogTitle
                                        >Create New Department</DialogTitle
                                    >
                                    <DialogDescription>
                                        Add a new department to your
                                        organization
                                    </DialogDescription>
                                </DialogHeader>

                                <form
                                    @submit.prevent="createDepartment"
                                    class="space-y-5"
                                >
                                    <div class="py-2">
                                        <label class="text-sm font-medium">
                                            Department Name
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <Input
                                            v-model="form.name"
                                            placeholder="e.g., Marketing Department"
                                        />
                                    </div>
                                    <DialogFooter class="gap-2 border-t pt-4">
                                        <DialogClose as-child>
                                            <Button
                                                variant="outline"
                                                class="flex-1 lg:flex-none"
                                                @click="form.reset()"
                                                >Cancel</Button
                                            >
                                        </DialogClose>
                                        <Button
                                            type="submit"
                                            class="flex-1 lg:flex-none"
                                            >Create Department</Button
                                        >
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>

                <!-- Edit Dialog -->
                <div>
                    <Dialog v-model:open="editDialogOpen">
                        <DialogContent class="sm:max-w-md">
                            <DialogHeader>
                                <DialogTitle>Edit Department</DialogTitle>
                            </DialogHeader>

                            <form
                                @submit.prevent="updateDepartment"
                                class="space-y-5"
                            >
                                <Input
                                    v-model="editForm.name"
                                    placeholder="Department Name"
                                />
                                <DialogFooter class="gap-2 border-t pt-4">
                                    <DialogClose as-child>
                                        <Button variant="outline"
                                            >Cancel</Button
                                        >
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
                            <Button
                                variant="destructive"
                                @click="deleteDepartment"
                            >
                                Delete
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <!-- Search & Filters -->
                <div class="mb-6 rounded-xl border p-4 shadow-sm">
                    <div
                        class="flex flex-col gap-4 lg:flex-row lg:items-center"
                    >
                        <div class="flex items-center gap-3">
                            <!-- Search -->
                            <div class="relative w-64">
                                <Search
                                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                                />
                                <Input class="pl-10" placeholder="Search..." />
                            </div>

                            <!-- Status Select -->
                            <Select>
                                <SelectTrigger class="w-36">
                                    <SelectValue placeholder="All Status" />
                                </SelectTrigger>

                                <SelectContent>
                                    <SelectItem value="active"
                                        >Active</SelectItem
                                    >
                                    <SelectItem value="inactive"
                                        >Inactive</SelectItem
                                    >
                                </SelectContent>
                            </Select>

                            <!-- Filter Button -->
                            <Button variant="outline"> Filter </Button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-hidden rounded-xl border shadow-sm">
                    <div class="border-b p-6">
                        <h2 class="text-lg">Department List</h2>
                    </div>

                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Department</TableHead>
                                <TableHead>Employees</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-end">Actions</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <!-- Show this row if no departments exist -->
                            <TableRow v-if="props.departments.length === 0">
                                <TableCell
                                    colspan="4"
                                    class="text-center text-gray-500"
                                >
                                    No departments yet.
                                </TableCell>
                            </TableRow>

                            <!-- Otherwise, render all departments -->
                            <TableRow
                                v-else
                                v-for="dept in props.departments"
                                :key="dept.id"
                            >
                                <TableCell>
                                    <div class="flex items-center gap-3">
                                        <Building
                                            class="h-4 w-4 text-primary"
                                        />
                                        <p class="font-medium">
                                            {{ dept.name }}
                                        </p>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">1</span>
                                        <span class="text-sm">employees</span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800"
                                    >
                                        Active
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            @click="openEditDialog(dept)"
                                            size="sm"
                                            variant="outline"
                                            class="gap-1"
                                        >
                                            <Pencil class="h-3 w-3" />
                                            Edit
                                        </Button>
                                        <Button
                                            @click="confirmDelete(dept.id)"
                                            size="sm"
                                            variant="destructive"
                                            class="gap-1"
                                        >
                                            <Trash2 class="h-3 w-3" />
                                            Delete
                                        </Button>
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
                                <Button variant="outline" size="sm" disabled
                                    >Previous</Button
                                >
                                <Button variant="outline" size="sm" disabled
                                    >Next</Button
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
