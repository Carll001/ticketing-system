<script setup lang="ts">
import AddStepDialog from '@/components/task-components/task-step-components/AddStepDialog.vue';
import TaskDeleteDialog from '@/components/task-components/TaskDeleteDialog.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import taskLink from '@/routes/task';
import { BreadcrumbItem, Task } from '@/types';
import { Head, router } from '@inertiajs/vue3';


const props = defineProps<{
    task: Task
}>();

const editTask = () => {
    router.get(taskLink.edit(props.task.id).url)
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: taskLink.index().url,
    },
    {
        title: props.task.title,
        href: taskLink.show(props.task.id).url,
    },
]

</script>

<template>
    <Head :title="task.title ?? 'Undefined'"/>

    <AppLayout :breadcrumbs="breadcrumbs">
        
        <div class="flex flex-col flex-1 gap-4 p-4">
            <div class="flex items-start justify-between">
                <section>
                    <h3 class="text-3xl">{{ task.title }}</h3>
                    <p class="text-muted-foreground">{{ task.description }}</p>
                    <p class="text-muted-foreground text-sm">From: {{ task.creator?.name }}</p>
                </section>
                <section class="text-right">
                    <div class="flex gap-2 items-center">
                        <p class="text-muted-foreground text-sm">Assigned to:</p>
                        <Button class="text-xs p-2 h-8" variant="outline">
                            {{ task.assigned_to }}
                        </Button>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-sm gap-2">
                            Due: {{ task.due_date }}
                        </p>
                    </div>
                </section>
            </div>
            <div class="flex justify-end gap-2">
                <TaskDeleteDialog :id="props.task.id"/>
                <Button size="sm" @click="editTask">Edit task</Button>
                <AddStepDialog :task="task"/>
            </div>
        </div>
    </AppLayout>
</template>