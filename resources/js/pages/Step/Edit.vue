<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';
import { toast } from 'vue-sonner'
import TaskCreateForm from '@/components/task-components/TaskCreateForm.vue'
import { BreadcrumbItem, Department, Step, Task, User } from '@/types';
import taskLink from '@/routes/task';
import StepCreateForm from '@/components/task-step-components/StepCreateForm.vue';
import stepLink from '@/routes/step';

import StepEditForm from '@/components/task-step-components/StepEditForm.vue';

const props = defineProps<{
    // departments: Department[],
    users: User[],
    step: Step
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: taskLink.index().url,
    },
    {
        title: props.step.task.title,
        href: taskLink.show(props.step.task.id).url,
    },
    {
        title: `Edit ${props.step.title}`,
        href: stepLink.create(props.step.id).url,
    },
];


</script>
<template>
    <Head title="Create Task" />
    <AppLayout :breadcrumbs="breadcrumbs">
        
        <div class="p-4 flex flex-col flex-1">
            <StepEditForm :step="props.step" :users="props.users"/>
            
        </div>
    </AppLayout>
</template>