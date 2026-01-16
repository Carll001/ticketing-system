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
import { Department } from '@/types';
import { computed, Ref, ref, watch } from 'vue';
import type { DateValue } from '@internationalized/date'
import { getLocalTimeZone, today } from '@internationalized/date'
import { ChevronDownIcon } from 'lucide-vue-next'
import { Calendar } from '@/components/ui/calendar'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'




const props = defineProps<{
    departments: Department[],
}>()

const hasDepartments = computed(() => (props.departments?.length ?? 0) > 0);

const form = useForm({
    title: '',
    description: '',
    assigned_to: null as string | null,
    is_billable: false,
    expenses_total: 0,
    type: '',
    due_date: '',
});

const date: Ref<DateValue | undefined> = ref(undefined)

watch(date, (value) => {
    form.due_date = value ?  value.toString() : ''
})

const storeTask = () => {
    form.post(taskLink.store().url);
};

const discardCreate = () => {
    router.visit(taskLink.index().url, {
        preserveState: false,
    })
} 

</script>
<template>
    <div class="space-y-4">
        <Form @submit.prevent="storeTask">
            <section class="flex justify-between">
                <div class="flex gap-2">
                    <Heading title="Add Task" />
                </div>
                <div class="flex items-center gap-2">
                    <Button size="sm" type="button" variant="destructive" @click="discardCreate">Discard</Button>
                    <Button size="sm" type="submit">Create</Button>
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
                                        :model-value="date"
                                        layout="month-and-year"
                                        @update:model-value="(value) => {
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
                            <Label for="tast-type"
                                >Task type
                                <span class="text-lg text-red-500"
                                    >*</span
                                ></Label
                            >
                            <Select id="task-type" v-model="form.type">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Task types</SelectLabel>
                                        <SelectItem value="preset">
                                            Preset
                                        </SelectItem>
                                        <SelectItem value="custom">
                                            Custom
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.type" />
                        </div>

                        <Separator class="my-4" />

                        <div class="space-y-4">
                            <Label for="assigned_to">Assign to</Label>
                            <Select id="assigned_to" v-model="form.assigned_to" >
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Assign to" />
                                </SelectTrigger>
                                <SelectContent>
                                    
                                    <SelectGroup >
                                        <SelectItem :value="null">
                                            Anyone
                                        </SelectItem>
                                        <SelectItem v-for="department in props.departments" :key="department.id" :value="department.id">
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
