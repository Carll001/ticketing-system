<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import TaskCard from '@/components/task-components/TaskCard.vue';
import { Button } from '@/components/ui/button';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import taskLink from '@/routes/task';
import { type BreadcrumbItem, Task } from '@/types';
import { Head, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: taskLink.index().url,
    },
];


const props = defineProps<{
    tasks: {data: Task[]},
}>()

const createTask = () => {
    router.visit(taskLink.create(), {
        preserveState: false
    })
}

</script>
<template>
    <Head title="Task" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 flex flex-col flex-1 gap-4">
            <div class="flex items-start justify-between">
                <Input class="max-w-lg"/>
                <Button size="sm" @click="createTask">Create task</Button>
            </div>
            <div class="flex flex-col gap-4">
                <TaskCard :tasks="props.tasks.data"/>
            </div>
        </div>
    </AppLayout>
</template>