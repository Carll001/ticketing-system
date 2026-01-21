<script setup lang="ts">
import EmptyData from '@/components/EmptyData.vue';
import AddStepDialog from '@/components/task-step-components/AddStepDialog.vue';
import StepCard from '@/components/task-step-components/StepCard.vue';
import TaskDeleteDialog from '@/components/task-components/TaskDeleteDialog.vue';
import { Button } from '@/components/ui/button';
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import AppLayout from '@/layouts/AppLayout.vue';
import taskLink from '@/routes/task';
import { BreadcrumbItem, Task } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowUpRightIcon, FolderCode, FolderOpen, List, NotepadText } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import step from '@/routes/step';

const props = defineProps<{
    task: { data: Task }
}>();


const editTask = () => {
    router.visit(taskLink.edit(props.task.data.id).url)
}

const addStep = () => {
    router.visit(step.create(props.task.data.id).url, {
    });
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

    <Head :title="props.task.data.title ?? 'Undefined'" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex flex-col flex-1 gap-4 p-4">
            <div class="flex items-start justify-between">
                <section>
                    <h3 class="text-3xl">{{ props.task.data.title }}</h3>
                    <p class="text-muted-foreground">{{ props.task.data.description ?? 'No description' }}</p>
                    <p class="text-muted-foreground text-sm">Creator: {{ props.task.data.creator?.name }}</p>
                </section>
                <section class="text-right">
                    <div class="flex gap-2 items-center">
                        <p class="text-muted-foreground text-sm">Assigned to:</p>
                        <Button class="text-xs p-2 h-8" variant="outline">
                            {{ props.task.data.assigned?.name ?? 'Anyone' }}
                        </Button>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-sm gap-2">
                           Due: 
                           {{ props.task.data.due_date ? new Date(props.task.data.due_date).toDateString() : 'No due date' }}
                        </p>
                    </div>
                </section>
            </div>
            <div class="flex justify-end gap-2">
                <TaskDeleteDialog :id="props.task.data.id" />
                <Button size="sm" @click="editTask" variant="secondary">
                    Edit task
                </Button>
                <!-- <AddStepDialog :task="task" /> -->
                <Button size="sm" @click="addStep">Add Step</Button>
            </div>

            <div class="space-y-4">
                <div class="">
                    <StepCard :steps="props.task.data.steps" />
                </div>
                <EmptyData :icon="NotepadText" title="No Steps Yet"
                    message="You haven't created any steps yet. Get started by creating first step."
                    :length="props.task.data.steps?.length === 0" />
            </div>
        </div>
    </AppLayout>
</template>