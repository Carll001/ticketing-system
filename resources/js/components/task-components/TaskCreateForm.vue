<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
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
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
// Idinagdag na imports para sa Combobox
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Textarea } from '@/components/ui/textarea';
import taskLink from '@/routes/task';
import { Department } from '@/types';
import { Form, router, useForm } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import { getLocalTimeZone } from '@internationalized/date';
import { ChevronDownIcon, Check, ChevronsUpDown } from 'lucide-vue-next';
import { computed, Ref, ref, watch } from 'vue';
import InputError from '../InputError.vue';
import Separator from '../ui/separator/Separator.vue';
import { cn } from '@/lib/utils'; // Utility para sa conditional classes
import { toast } from 'vue-sonner';

const props = defineProps<{
    departments: Department[];
}>();

const hasDepartments = computed(() => (props.departments?.length ?? 0) > 0);

const form = useForm({
    title: '',
    description: '',
    assigned_to: null as string | number | null,
    order: '',
    is_billable: false,
    expenses_total: 0,
    type: '',
    due_date: '',
});

// Logic para sa Combobox state at labels
const open = ref(false);
const selectedDepartmentLabel = computed(() => {
    if (form.assigned_to === null) return 'Anyone';
    const dept = props.departments.find((d) => d.id === form.assigned_to);
    return dept ? dept.name : 'Select department...';
});

const date: Ref<DateValue | undefined> = ref(undefined);

watch(date, (value) => {
    form.due_date = value ? value.toString() : '';
});

const storeTask = () => {
    form.post(taskLink.store().url, {
        onSuccess: () => {
            toast.success('sakses');
        }
    });
};

const discardCreate = () => {
    router.visit(taskLink.index().url, {
        preserveState: false,
    });

    
};
</script>

<template>
    <div class="space-y-4">
        <form @submit.prevent="storeTask" >
            <section class="flex justify-between">
                <div class="flex gap-2">
                    <Heading title="Add Task" />
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        size="sm"
                        type="button"
                        variant="destructive"
                        @click="discardCreate"
                        >Discard</Button
                    >
                    <Button size="sm" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create' }}
                    </Button>
                </div>
            </section>

            <section class="grid grid-cols-[3fr_2fr] gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Task Details</CardTitle>
                        <CardDescription>Description of tas k</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <section class="flex items-center gap-4">
                                <div class="w-full ">
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
                                        Due date <span class="text-muted-foreground text-xs"> (Optional) </span>
                                    </Label>
                                    <Popover v-slot="{ close }">
                                        <PopoverTrigger as-child>
                                            <Button
                                                id="date"
                                                variant="outline"
                                                class="w-48 justify-between font-normal"
                                            >
                                                {{
                                                    date
                                                        ? date
                                                              .toDate(
                                                                  getLocalTimeZone(),
                                                              )
                                                              .toLocaleDateString()
                                                        : 'Select date'
                                                }}
                                                <ChevronDownIcon />
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent
                                            class="w-auto overflow-hidden p-0"
                                            align="start"
                                        >
                                            <Calendar
                                                :model-value="date"
                                                layout="month-and-year"
                                                @update:model-value="
                                                    (value) => {
                                                        if (value) {
                                                            date = value;
                                                            close();
                                                        }
                                                    }
                                                "
                                            />
                                        </PopoverContent>
                                    </Popover>
                                </div>
                            </section>
                            <section class="space-y-4">
                                <Label for="task-title">Task description <span class="text-muted-foreground text-xs"> (Optional) </span></Label>
                                <Textarea
                                    id="task-title"
                                    v-model="form.description"
                                    placeholder="task title"
                                />
                                <InputError
                                    :message="form.errors.description"
                                />
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
                                >Step order
                                <span class="text-lg text-red-500"
                                    >*</span
                                ></Label
                            >
                            <Select id="step-order" v-model="form.order">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a order" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Task order</SelectLabel>
                                        <SelectItem value="sequential">
                                            Sequential
                                        </SelectItem>
                                        <SelectItem value="random">
                                            Random
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.order" />
                        </div>

                        <Separator class="my-4" />

                        <div class="space-y-4">
                            <Label for="assigned_to">Assign department</Label>
                            
                            <Popover v-model:open="open">
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        :aria-expanded="open"
                                        class="w-full justify-between"
                                    >
                                        {{ selectedDepartmentLabel }}
                                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-full p-0">
                                    <Command>
                                        <CommandInput placeholder="Search department..." />
                                        <CommandEmpty>No department found.</CommandEmpty>
                                        <CommandList>
                                            <CommandGroup>
                                                <CommandItem
                                                    value="anyone"
                                                    @select="() => {
                                                        form.assigned_to = null;
                                                        open = false;
                                                    }"
                                                >
                                                    <Check
                                                        :class="cn(
                                                            'mr-2 h-4 w-4',
                                                            form.assigned_to === null ? 'opacity-100' : 'opacity-0'
                                                        )"
                                                    />
                                                    Anyone
                                                </CommandItem>

                                                <CommandItem
                                                    v-for="department in props.departments"
                                                    :key="department.id"
                                                    :value="department.name"
                                                    @select="() => {
                                                        form.assigned_to = department.id;
                                                        open = false;
                                                    }"
                                                >
                                                    <Check
                                                        :class="cn(
                                                            'mr-2 h-4 w-4',
                                                            form.assigned_to === department.id ? 'opacity-100' : 'opacity-0'
                                                        )"
                                                    />
                                                    {{ department.name }}
                                                </CommandItem>
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                            
                            <InputError :message="form.errors.assigned_to" />
                        </div>
                    </CardContent>
                </Card>
            </section>
        </form>

        <!-- <pre>{{ form }}</pre> -->
    </div>
</template>