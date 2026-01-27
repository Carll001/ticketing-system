<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';
import { toast } from 'vue-sonner'
import TaskCreateForm from '@/components/task-components/TaskCreateForm.vue'
import { BreadcrumbItem, Department, Preset, Task, User } from '@/types';
import taskLink from '@/routes/task';
import StepCreateForm from '@/components/task-step-components/StepCreateForm.vue';
import stepLink from '@/routes/step';

const props = defineProps<{
    // departments: Department[],
    users: User[],
    task: Task
    presets: Preset[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: taskLink.index().url,
    },
    {
        title: props.task.title,
        href: taskLink.show(props.task.id).url,
    },
    {
        title: 'Add Task Step',
        href: stepLink.create(props.task.id).url,
    },
];

</script>
<template>
    <Head title="Create Task" />
    <AppLayout :breadcrumbs="breadcrumbs">
        
        <div class="p-4 flex flex-col flex-1">
            <StepCreateForm :users="props.users" :task="props.task" :presets="presets"/>
            
        </div>
    </AppLayout>
</template>