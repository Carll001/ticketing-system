<script setup lang="ts">
import EmptyData from '@/components/EmptyData.vue';
import StepCard from '@/components/task-step-components/StepCard.vue';
import TaskDeleteDialog from '@/components/task-components/TaskDeleteDialog.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import taskLink from '@/routes/task';
import { BreadcrumbItem, Task, Step } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Ellipsis, NotepadText } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import step from '@/routes/step';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import PermissionGuard from '@/components/PermissionGuard.vue';
import { useCurrency } from '@/composables/useCurrency';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { toast } from 'vue-sonner';
const showDeleteDialog = ref(false);

const { formatCurrency, formatNumber } = useCurrency();

const props = defineProps<{
    task: { data: Task }
}>();

const activeTab = ref('all')

// Helper to safely get steps array
const getStepsArray = (): Step[] => {
    const steps = props.task.data.steps;

    if (!steps) return [];

    // If steps has a 'data' property (Laravel Resource structure)
    if ('data' in steps && Array.isArray(steps.data)) {
        return steps.data;
    }

    // If steps is already an array
    if (Array.isArray(steps)) {
        return steps;
    }

    return [];
};

const filteredSteps = computed(() => {
    const stepsArray = getStepsArray();

    if (activeTab.value === 'all') {
        return stepsArray;
    }

    return stepsArray.filter((step: Step) => step.status === activeTab.value);
});

// Handle status filter from StepCard
const handleFilterByStatus = (status: string) => {
    activeTab.value = status;
};

const deleteTask = () => {
    router.delete(taskLink.delete(props.task.data.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Task deleted successfully!');
            showDeleteDialog.value = false;
            router.visit(taskLink.index().url);
        }
    });
};
const editTask = () => {
    router.visit(taskLink.edit(props.task.data.id).url)
}

const addStep = () => {
    router.visit(step.create(props.task.data.id).url);
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: taskLink.index().url,
    },
    {
        title: props.task.data.title,
        href: taskLink.show(props.task.data.id).url,
    },
]

const statusTabs = [
    {
        value: 'all',
        label: 'All',
        emptyTitle: 'No Steps Yet',
        emptyMessage: 'You haven\'t created any steps yet. Get started by creating first step.'
    },
    {
        value: 'pending',
        label: 'Pending',
        emptyTitle: 'No Pending Steps',
        emptyMessage: 'There are no pending steps.'
    },
    {
        value: 'assigned',
        label: 'Assigned',
        emptyTitle: 'No Assigned Steps',
        emptyMessage: 'There are no assigned steps.'
    },
    {
        value: 'accepted',
        label: 'Accepted',
        emptyTitle: 'No Accepted Steps',
        emptyMessage: 'There are no accepted steps.'
    },
    {
        value: 'completed',
        label: 'Completed',
        emptyTitle: 'No Completed Steps',
        emptyMessage: 'There are no completed steps.'
    },
    {
        value: 'rejected',
        label: 'Rejected',
        emptyTitle: 'No Rejected Steps',
        emptyMessage: 'There are no rejected steps.'
    },
];

// check if task has steps

</script>

<template>

    <Head :title="props.task.data.title ?? 'Undefined'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col flex-1 gap-4 p-4">
            <div class="flex items-start justify-between truncate">
                <section class="min-w-0 flex-1">
                    <h3 class="text-3xl truncate">{{ props.task.data.title }}</h3>
                    <p class="text-muted-foreground truncate">{{ props.task.data.description ?? 'No description' }}</p>
                    <p class="text-muted-foreground text-sm">Creator: {{ props.task.data.creator?.name }}</p>
                    <p class="text-muted-foreground text-sm">total cost: {{ formatCurrency(task.data.total_steps_cost)
                    }}</p>
                </section>
                <section class="text-right">
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon-sm">
                                <Ellipsis />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <PermissionGuard permission="can edit task">
                                <DropdownMenuItem @click="editTask">Edit Task</DropdownMenuItem>
                            </PermissionGuard>
                            <PermissionGuard permission="can delete task">
                                <DropdownMenuItem class="text-destructive focus:text-destructive"
                                    @click="showDeleteDialog = true">
                                    Delete
                                </DropdownMenuItem>
                            </PermissionGuard>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <div class="flex gap-2 items-center">
                        <p class="text-muted-foreground text-sm">Assigned to:</p>
                        <Button class="text-xs p-2 h-8" variant="outline">
                            {{ props.task.data.assigned?.name ?? 'Anyone' }}
                        </Button>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-sm gap-2">
                            Due:
                            {{ props.task.data.due_date ? new Date(props.task.data.due_date).toDateString() : 'Noduedate' }}
                        </p>
                    </div>
                </section>
            </div>

            <Tabs v-model="activeTab" default-value="all">
                <div class="flex justify-between gap-4">
                    <TabsList>
                        <TabsTrigger v-for="tab in statusTabs" :key="tab.value" :value="tab.value">
                            {{ tab.label }}
                        </TabsTrigger>
                    </TabsList>
                    <div class="space-x-2">
                        <PermissionGuard permission="can create task">
                            <Button size="sm" @click="addStep">Add Step</Button>
                        </PermissionGuard>
                    </div>
                </div>

                <TabsContent v-for="tab in statusTabs" :key="tab.value" :value="tab.value">
                    <div class="space-y-4">
                        <StepCard :steps="filteredSteps" :creator="props.task.data.creator"
                            @filter-by-status="handleFilterByStatus" :task-order="props.task.data.order"/>
                    </div>
                    <EmptyData :icon="NotepadText" :title="tab.emptyTitle" :message="tab.emptyMessage"
                        :length="filteredSteps.length === 0" />
                </TabsContent>
            </Tabs>

            <!-- Delete Dialog -->
            <Dialog v-model:open="showDeleteDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Are you absolutely sure?</DialogTitle>
                        <DialogDescription>
                            This action cannot be undone. This will permanently delete this task
                            and remove all associated data from our servers.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="ghost">Cancel</Button>
                        </DialogClose>
                        <Button variant="destructive" @click="deleteTask">Delete</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>