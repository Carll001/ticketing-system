<script setup lang="ts">
import {
    Card,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Task } from '@/types';
import taskLink from '@/routes/task';
import { Button } from '../ui/button';
import { router } from '@inertiajs/vue3';
import { Calendar, Ellipsis, NotebookText } from 'lucide-vue-next';
import TaskDeleteDialog from './TaskDeleteDialog.vue';
import EmptyData from '../EmptyData.vue';
import task from '@/routes/task';
import { computed } from 'vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '../ui/dropdown-menu';
import PermissionGuard from '../PermissionGuard.vue';
import step from '@/routes/step';

const props = defineProps<{
    tasks: Task[],
}>();

// navigating to task details
const visitTask = (id: string) => {
    router.visit(taskLink.show(id).url)
}

// for editing task
// const editTask = (id: string) => {
//     router.visit(taskLink.edit(id).url)
// }

// for visiting department
const visitDepartment = (id: string) => {
    router.visit(`/department/${id}`)
}

// calculating total steps
const totalStepsCount = (task: Task) => {
    if (!task.steps?.data || !Array.isArray(task.steps?.data)) return 0;
    return task.steps.data.length;
};

// calculating completed steps
const completedStepsCount = (task: Task) => {
    if (!task.steps?.data || !Array.isArray(task.steps?.data)) return 0;
    return task.steps.data.filter(step => step.status === 'completed').length;
};

// check if task has steps
const hasSteps = (task: Task) => {
    return task.steps?.data && task.steps.data.length > 0;
};

// calculating steps text: completed task / total steps
const getStepsText = (task: Task) => {
    if (!hasSteps(task)) {
        return 'No Steps Created';
    }
    return `Steps completed: ${completedStepsCount(task)} / ${totalStepsCount(task)}`;
};

</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <Card v-for="task in props.tasks">
            <CardHeader>
                <!-- <div class="flex justify-between items-center"> -->
                <CardTitle @click="visitTask(task.id)" class="cursor-pointer text-lg truncate max-w-[18rem]">
                    {{ task.title }},
                </CardTitle>

                <!-- <div class="space-x-2">
                        <PermissionGuard permission="can delete task">
                            <TaskDeleteDialog :id="task.id" size="sm" />
                        </PermissionGuard>
                        <PermissionGuard permission="can edit task">
                            <Button size="sm" variant="secondary" @click="editTask(task.id)">Edit</Button>
                        </PermissionGuard>
                    </div> -->
                <!-- </div> -->

                <CardDescription>{{ task.description }}</CardDescription>

                <CardDescription>
                    <div class="flex gap-4 items-center">
                        <Button class="h-6 p-2 text-xs" variant="outline"
                            @click="task.assigned && visitDepartment(task.assigned.data.id)">
                            {{ task.assigned?.data?.name ?? 'Anyone' }}
                        </Button>

                        <div class="flex items-end gap-1">
                            <Calendar :size="17" v-show="task.due_date" />
                            <p class="text-xs">
                                {{ task.due_date ? new Date(task.due_date).toDateString() : 'No due date' }}
                            </p>
                        </div>

                        <p class="text-xs">

                            {{ getStepsText(task) }}
                            <!-- {{ completedStepsCount(task) }} / {{ task.steps?.data.length || 0 }} -->
                            <!-- <pre>{{ task }}</pre> -->
                        </p>

                    </div>
                </CardDescription>
            </CardHeader>
        </Card>
    </div>


    <EmptyData :icon="NotebookText" title="no task yet" message="no task yet. be the first to create a task"
        :length="tasks.length === 0" />

</template>
