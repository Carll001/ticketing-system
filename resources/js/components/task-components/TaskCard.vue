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
import { Calendar } from 'lucide-vue-next';
import TaskDeleteDialog from './TaskDeleteDialog.vue';


const props = defineProps<{
    tasks: Task[],
}>();

const visitTask = (id: string) => {
    router.get(taskLink.show(id).url)
}

const editTask = (id: string) => {
    router.get(taskLink.edit(id).url)
}

</script>
<template>
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
                    <Button class="h-6 p-2 text-xs" variant="outline">{{
                        task.assigned_to ? task.assigned_to : 'Anyone'
                    }}</Button>

                    <div class="flex items-end gap-1">
                        <Calendar :size="17" v-show="task.due_date !== 'No due date'"/>
                        <p class="text-xs">{{task.due_date}}</p>
                       
                    </div>
                    <p class="text-xs">Steps: 12/100</p>
                </div>
            </CardDescription>
        </CardHeader>
    </Card>

    <p class="text-center text-muted-foreground" v-show="tasks.length === 0">No task at the moment</p>
</template>
