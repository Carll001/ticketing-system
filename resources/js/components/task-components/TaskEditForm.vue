<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import taskLink from '@/routes/task';
import { Form, router, useForm } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import InputError from '../InputError.vue';
import Separator from '../ui/separator/Separator.vue';
import TaskCustomForm from './TaskCustomForm.vue';
import { store } from '@/routes/login';
import { Department, Task } from '@/types';
import { computed, Ref, ref, watch } from 'vue';
import task from '@/routes/task';
import type { DateValue } from '@internationalized/date'
import { getLocalTimeZone, parseDate } from '@internationalized/date'
import { ChevronDownIcon } from 'lucide-vue-next'
import { Calendar } from '@/components/ui/calendar'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '../ui/tooltip';

const props = defineProps<{
    departments: Department[],
    task: {data: Task},
}>()

const form = useForm({
    title: props.task.data.title,
    description: props.task.data.description,
    assigned_to: props.task.data.assigned_to === 'Anyone' ? null : props.task.data.assigned_to,
    type: props.task.data.type,
    due_date: props.task.data.due_date || '',
});

const date = ref(
    props.task.data.due_date 
        ? parseDate(props.task.data.due_date.split('T')[0])  // only take the date part
        : undefined
);

watch(date, (value) => {
    form.due_date = value ? value.toString() : '';
});

const updateTask = () => {
    form.put(taskLink.update(props.task.data.id).url);
};

const discardEdit = () => {
    router.visit(taskLink.show(props.task.data.id).url, {
        preserveState: false,
    })
} 

</script>
<template>
    <div class="space-y-4">
        <Form @submit.prevent="updateTask">
            <section class="flex justify-between">
                <div class="flex gap-2">
                    <Heading title="Edit Task" />
                </div>
                <div class="flex items-center gap-2">
                    <Button size="sm" type="button" @click="discardEdit" variant="destructive">Discard</Button>
                    <Button size="sm" type="submit" >Update</Button>
                </div>
            </section>

            <section class="grid grid-cols-[3fr_2fr] gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Task Details</CardTitle>
                        <CardDescription>Description of task</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <section class="flex items-center gap-4">
                                <div class="w-full space-y-4">
                                    <Label for="task-title"
                                        >Task title
                                        <span class="text-lg text-red-500"
                                            >*</span
                                        ></Label
                                    >
                                    <Input
                                        id="task-title"
                                        v-model="form.title"
                                        placeholder="task title"
                                    />
                                    <InputError :message="form.errors.title" />
                                </div>
                                <div class="flex flex-col gap-3">
                                    <Label for="date" class="px-1">
                                    Due date
                                    </Label>
                                    <Popover v-slot="{ close }">
                                    <PopoverTrigger as-child>
                                        <Button
                                        id="date"
                                        variant="outline"
                                        class="w-48 justify-between font-normal"
                                        >
                                        {{ date ? date.toDate(getLocalTimeZone()).toLocaleDateString() : "Select date" }}
                                        <ChevronDownIcon />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                                        <Calendar
                                        :model-value="date as any"
                                        layout="month-and-year"
                                        @update:model-value="(value: any) => {
                                            if (value) {
                                            date = value
                                            close()
                                            }
                                        }"
                                        />
                                    </PopoverContent>
                                    </Popover>
                                </div>
                            </section>
                            <section class="space-y-4">
                                <Label for="task-title">Task description</Label>
                                <Textarea
                                    id="task-title"
                                    v-model="form.description"
                                    placeholder="task title"
                                />
                                <InputError :message="form.errors.description" />
                            </section>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle>Additional details</CardTitle>
                        <CardDescription>additional details</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Label for="task-type">
                                Task type
                                <span class="text-lg text-red-500">*</span>
                            </Label>

                            <Select id="task-type" v-model="form.type" disabled>
                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Select a Type" />
                                            </SelectTrigger>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>You can't change the type once it has been created.</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Task types</SelectLabel>
                                        <SelectItem value="preset">Preset</SelectItem>
                                        <SelectItem value="custom">Custom</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>

                            <InputError :message="form.errors.type" />
                        </div>


                        <Separator class="my-4" />

                        <div class="space-y-4">
                            <Label for="assigned_to">Assign to</Label>
                            <Select id="assigned_to" v-model="form.assigned_to">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Anyone" />
                                </SelectTrigger>

                                <SelectContent>
                                    <SelectGroup>
                                        <!-- Anyone -->
                                        <SelectItem :value="null">
                                            Anyone
                                        </SelectItem>

                                        <!-- Departments -->
                                        <SelectItem
                                            v-for="department in props.departments"
                                            :key="department.id"
                                            :value="department.id"
                                        >
                                            {{ department.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.assigned_to" />
                        </div>
                    </CardContent>
                </Card>
            </section>
        </Form>
    </div>
</template>
