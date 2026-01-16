<script setup lang="ts">
import AddStepDialog from '@/components/task-components/task-step-components/AddStepDialog.vue';
import TaskDeleteDialog from '@/components/task-components/TaskDeleteDialog.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import taskLink from '@/routes/task';
import { BreadcrumbItem, Task } from '@/types';
import { Head, router } from '@inertiajs/vue3';


const props = defineProps<{
    task: {data: Task}
}>();

const editTask = () => {
    router.get(taskLink.edit(props.task.data.id).url)
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

</script>

<template>
    <Head :title="props.task.data.title ?? 'Undefined'"/>

    <AppLayout :breadcrumbs="breadcrumbs">
        
        <div class="flex flex-col flex-1 gap-4 p-4">
            <div class="flex items-start justify-between">
                <section>
                    <h3 class="text-3xl">{{ props.task.data.title }}</h3>
                    <p class="text-muted-foreground">{{ props.task.data.description }}</p>
                    <p class="text-muted-foreground text-sm">From: {{ props.task.data.creator?.name }}</p>
                </section>
                <section class="text-right">
                    <div class="flex gap-2 items-center">
                        <p class="text-muted-foreground text-sm">Assigned to:</p>
                        <Button class="text-xs p-2 h-8" variant="outline">
                            {{ props.task.data.assigned_to }}
                        </Button>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-sm gap-2">
                            Due: {{ props.task.data.due_date }}
                        </p>
                    </div>
                </section>
            </div>
            <div class="flex justify-end gap-2">
                <TaskDeleteDialog :id="props.task.data.id"/>
                <Button size="sm" @click="editTask">Edit task</Button>
                <AddStepDialog :task="task"/>
            </div>
        </div>
    </AppLayout>
</template>