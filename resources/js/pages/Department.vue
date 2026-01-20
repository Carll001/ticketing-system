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

import { Label } from '@/components/ui/label';
import { Department } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

// const props = defineProps<{
//     departments: { data: Department[] };
// }>();

const props = defineProps<{
    departments: { data: Department[]; links: any[]; meta: any };
    filters: { search?: string };
}>();

// Search functionality
const searchQuery = ref(props.filters.search || '');

const performSearch = debounce(() => {
    router.get(
        '/department',
        { search: searchQuery.value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}, 300);

watch(searchQuery, () => {
    performSearch();
});

// Pagination
// const goToPage = (url: string | null) => {
//     if (!url) return;

//     router.get(
//         url,
//         {},
//         {
//             preserveState: true,
//             preserveScroll: true,
//         },
//     );
// };

// Create function
const form = useForm({
    name: '',
});

const createDialog = ref(false);

const createDepartment = () => {
    form.post(department.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Department created successfully!');
            createDialog.value = false;
        },
    });
};

// Reset form when dialog is closed
watch(createDialog, (isOpen) => {
    if (!isOpen) {
        form.reset();
        form.clearErrors();
    }
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
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Department updated successfully!');
            editDialogOpen.value = false;
        },
    });
};

// Reset edit form when dialog is closed
watch(editDialogOpen, (isOpen) => {
    if (!isOpen) {
        editForm.reset();
        editForm.clearErrors();
    }
});

// Delete functions
const deleteDialogOpen = ref(false);
const deleteId = ref<string | null>(null);

const confirmDelete = (id: string) => {
    deleteId.value = id;
    deleteDialogOpen.value = true;
};

const deleteForm = useForm({});

const deleteDepartment = () => {
    if (!deleteId.value) return;

    deleteForm.delete(`/department/${deleteId.value}`, {
        onSuccess: () => {
            toast.success('Department deleted successfully!');
            deleteDialogOpen.value = false;
        },
        onError: () => {
            toast.error('Failed to delete department');
        },
    });
};
</script>

<template>
    <Head title="Department" />

    <AppLayout>
        <div class="flex flex-1 flex-col gap-4 p-4">
            <!-- Create Dialog -->
            <div class="flex justify-end">
                <div>
                    <Dialog v-model:open="createDialog">
                        <DialogTrigger as-child>
                            <Button class="w-full lg:w-auto">
                                <Plus class="mr-2 h-4 w-4" />
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

                            <form
                                @submit.prevent="createDepartment"
                                class="space-y-5"
                            >
                                <div class="py-2">
                                    <Label
                                        class="text-sm font-medium"
                                        for="dept-name"
                                    >
                                        Department Name
                                        <span class="text-red-500">*</span>
                                    </Label>
                                    <div>
                                        <Input
                                            id="dept-name"
                                            v-model="form.name"
                                            placeholder="e.g., Marketing Department"
                                        />

                                        <p
                                            v-if="form.errors.name"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors.name }}
                                        </p>
                                    </div>
                                </div>
                                <DialogFooter class="gap-2">
                                    <DialogClose as-child>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            class="flex-1 lg:flex-none"
                                        >
                                            Cancel
                                        </Button>
                                    </DialogClose>
                                    <Button
                                        type="submit"
                                        class="flex-1 lg:flex-none"
                                        :disabled="form.processing"
                                    >
                                        Create
                                    </Button>
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
                            <div>
                                <Label
                                    class="text-sm font-medium"
                                    for="edit-dept-name"
                                >
                                    Department Name
                                    <span class="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="edit-dept-name"
                                    v-model="editForm.name"
                                    placeholder="e.g., Marketing Department"
                                />

                                <p
                                    v-if="editForm.errors.name"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ editForm.errors.name }}
                                </p>
                            </div>

                            <DialogFooter class="gap-2 pt-2">
                                <DialogClose as-child>
                                    <Button type="button" variant="outline"
                                        >Cancel</Button
                                    >
                                </DialogClose>
                                <Button
                                    type="submit"
                                    :disabled="editForm.processing"
                                >
                                    Update
                                </Button>
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
                            :disabled="deleteForm.processing"
                        >
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <!-- Rest of your template... -->
            <!-- Search & Filters -->
            <div class="rounded-xl border p-4 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                    <div class="flex items-center gap-3">
                        <div class="relative w-64">
                            <Search
                                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                class="pl-10"
                                v-model="searchQuery"
                                placeholder="Search..."
                            />
                        </div>
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
                            <TableHead class="text-end">Actions</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-if="props.departments.data.length === 0">
                            <TableCell
                                colspan="4"
                                class="text-center text-gray-500"
                            >
                                No departments yet.
                            </TableCell>
                        </TableRow>

                        <TableRow
                            v-else
                            v-for="dept in props.departments.data"
                            :key="dept.id"
                        >
                            <TableCell>
                                <div class="flex items-center gap-3">
                                    <p class="font-medium">{{ dept.name }}</p>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">1</span>
                                    <span class="text-sm">employees</span>
                                </div>
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
    </AppLayout>
</template>
