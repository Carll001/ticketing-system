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

const props = defineProps<{
    tasks: Task[],
}>();

const visitTask = (id: string) => {
    router.visit(taskLink.show(id).url)
}

const editTask = (id: string) => {
    router.visit(taskLink.edit(id).url)
}


const visitDepartment = (id: string) => {
    router.visit(`/department/${id}`)
}


const completedStepsCount = (task: Task) => {
    if (!task.steps) return 0;
    return task.steps.filter(step => step.status === 'completed').length;
};
</script>

<template>
   <div class="grid grid-cols-2 gap-3">
        <Card v-for="task in props.tasks"  >
        <CardHeader>
            <div class="flex justify-between items-center">
                <CardTitle @click="visitTask(task.id)" class="cursor-pointer text-lg">
                    {{ task.title }}
                </CardTitle>

                <div class="space-x-2">
                    <TaskDeleteDialog :id="task.id" size="sm"/>
                    <Button size="sm" variant="secondary" @click="editTask(task.id)">Edit</Button>
                </div>
            </div>

            <CardDescription>{{ task.description }}</CardDescription>

            <CardDescription>
                <div class="flex gap-4 items-center">
                    <Button
                        class="h-6 p-2 text-xs"
                        variant="outline"
                        :disabled="!task.assigned"
                        @click="task.assigned && visitDepartment(task.assigned.id)"
                    >
                        {{ task.assigned?.name ?? 'Anyone' }}
                    </Button>

                    <div class="flex items-end gap-1">
                        <Calendar :size="17" v-show="task.due_date"/>
                        <p class="text-xs">
                            {{ task.due_date ? new Date(task.due_date).toDateString() : 'No due date' }}
                        </p>
                    </div>

                    <p class="text-xs">
                        Steps completed: {{ completedStepsCount(task) }} / {{ task.steps?.length ?? 0 }}
                    </p>
                </div>
            </CardDescription>
        </CardHeader>
    </Card>
    </div>
    

    <EmptyData
        :icon="NotebookText"
        title="no task yet"
        message="no task yet. be the first to create a task"
        :length="tasks.length === 0"
    />
</template>
