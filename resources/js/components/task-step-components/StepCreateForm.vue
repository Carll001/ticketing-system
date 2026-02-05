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
import { Textarea } from '@/components/ui/textarea';
import taskLink from '@/routes/task';
import { Preset, Task, User } from '@/types';
import { Form, router, useForm } from '@inertiajs/vue3';
import { CheckIcon, ChevronsUpDownIcon, Plus, Trash2Icon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import InputError from '../InputError.vue';
import Separator from '../ui/separator/Separator.vue';

import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import stepLink from '@/routes/step';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '../ui/command';
import { Checkbox } from '../ui/checkbox';
import HeadingSmall from '../HeadingSmall.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '../ui/select';
import { toast } from 'vue-sonner';
const openCombobox = ref(false);

const props = defineProps<{
    users: User[];
    task: Task;
    presets: Preset[];
}>();

const form = useForm({
    task_id: props.task.id,
    preset_id: null as string | null,
    title: '',
    description: '',
    type: 'custom',
    assigned_to: null as string | null,
    status: '',
    has_cost: null as boolean | null,
    again: null as boolean | null,

    fields: [] as {
        id: string;
        type: 'Checkbox' | 'Input' | 'Description';
        label: string
    }[],
});

const addField = (type: 'Checkbox' | 'Input' | 'Description') => {
    form.fields.push({
        id: crypto.randomUUID(),
        type: type,
        label: '',
    });
};
// Function to remove a field
const removeField = (index: number) => {
    form.fields.splice(index, 1);
};
const selectedPresetId = ref<string | null>(null);
const assignedUser = computed(() => {
    if (!form.assigned_to) return 'Anyone';
    const user = props.users.find(
        (user) => String(user.id) === form.assigned_to,
    );

    return user?.name ?? 'Select user...';
});

const storeStep = () => {
    form.post(stepLink.store({ task: props.task.id }).url, {
        onSuccess: () => {
            form.reset();
            toast.success('Task Step Created successfully!');
        },
    });
};

const discardCreate = () => {
    router.visit(taskLink.show(props.task.id).url, {
        preserveState: false,
    });
};

const groupedFields = computed(() => {
    return form.fields.reduce((acc, field) => {
        const type = field.type;
        if (!acc[type]) acc[type] = [];
        acc[type].push(field);
        return acc;
    }, {} as Record<string, typeof form.fields>);
});
const applyPreset = (presetId: string | null) => {
    // 1. Handle clearing the selection
    if (!presetId) {
        form.preset_id = null;
        selectedPresetId.value = null;
        return;
    }

    const preset = props.presets.find(p => p.id === presetId);
    if (!preset) return;

    // 2. Assign the ID to the form so the backend sees it
    form.preset_id = preset.id;
    selectedPresetId.value = presetId;

    // 3. Populate title and description if they are currently empty
    if (!form.title) form.title = preset.name;
    if (!form.description) form.description = preset.description;
    if (!form.has_cost) form.has_cost = preset.has_cost;

    // 4. Map the preset fields
    form.fields = preset.fields.map(field => ({
        id: crypto.randomUUID(),
        type: field.type as 'Checkbox' | 'Input' | 'Description',
        label: field.label
    }));
};
</script>
<template>
    <div class="">
        <form @submit.prevent="storeStep" class="space-y-2">
            <section class="flex justify-between items-start">
                <Heading title="Add Task Step" />
                <div class="flex items-center gap-2">
                    <Button size="sm" type="button" variant="destructive" @click="discardCreate">Discard</Button>
                    <Button size="sm" type="submit" :disabled="form.processing">{{ form.processing ? 'Creating...' :
                        'Create' }}</Button>
                </div>
            </section>

            <section class="grid grid-cols-[2fr_1fr] gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Step Details</CardTitle>
                        <CardDescription>Description of step</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <section class="flex items-start gap-4">
                                <div class="w-full">
                                    <Label for="step-title">Step title
                                        <span class="text-lg text-red-500">*</span></Label>
                                    <Input id="step-title" v-model="form.title" placeholder="step title" />
                                    <InputError :message="form.errors.title" />
                                </div>
                            </section>
                            <section class="space-y-4">
                                <Label for="step-title">Step description</Label>
                                <Textarea id="step-title" v-model="form.description" placeholder="step title" />
                                <InputError :message="form.errors.description" />
                            </section>
                        </div>
                        <div class="space-y-6">
                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <Label class="text-base font-semibold">User Input Fields</Label>
                                    <p class="text-xs text-zinc-500">Define what information the User must provide.</p>
                                </div>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button size="sm" variant="outline" class="gap-2">
                                            <Plus class="w-4 h-4" /> Add Field
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem @click="addField('Description')">Textarea (Description)
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="addField('Checkbox')">Checkbox (Confirmation)
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="addField('Input')">Small Input (Text/Number)
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>

                            <div v-if="form.fields.length === 0"
                                class="border-2 border-dashed border-zinc-800 rounded-lg p-6 text-center">
                                <p class="text-sm text-zinc-500">No input fields added. The User will just mark this
                                    step as complete.</p>
                            </div>

                            <div v-for="(fields, type) in groupedFields" :key="type" class="space-y-4">
                                <Label
                                    class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800/50 pb-1 block">
                                    {{ type }}{{ type === 'Checkbox' ? 'es' : 's' }}
                                </Label>

                                <div class="space-y-4">
                                    <section v-for="field in fields" :key="field.id"
                                        class="relative p-4 rounded-xl border border-zinc-300  space-y-3 group">

                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 mr-4">
                                                <Label
                                                    class="text-xs dark:text-white text-black uppercase font-bold mb-1 block">Field
                                                    Label / Question</Label>
                                                <Input v-model="form.fields[form.fields.indexOf(field)].label"
                                                    :placeholder="`e.g. ${type === 'Checkbox' ? 'Check if confirmed' : 'Enter detail name'}`" />
                                            </div>
                                            <Button variant="ghost" size="icon"
                                                class="h-8 w-8 text-zinc-500 hover:text-red-500 shrink-0"
                                                @click="removeField(form.fields.indexOf(field))">
                                                <Trash2Icon class="w-4 h-4" />
                                            </Button>
                                        </div>

                                        <div
                                            class="mt-4 pt-4 border-t border-zinc-800/50 opacity-80 grayscale pointer-events-none">
                                            <p class="text-xs uppercase font-bold text-black dark:text-white mb-2">User Response
                                                Preview</p>

                                            <div
                                                :class="['flex gap-3', type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start']">

                                                <div :class="[type === 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                                                    <Checkbox v-if="type === 'Checkbox'" class="rounded-sm" />

                                                    <Input v-if="type === 'Input'" disabled
                                                        :placeholder="`User will enter ${field.label || 'data'}...`"
                                                        class="h-8 text-xs " />

                                                    <Textarea v-if="type === 'Description'" disabled
                                                        :placeholder="`User will provide ${field.label || 'details'}...`"
                                                        class="min-h-[60px] text-xs  resize-none" />
                                                </div>

                                                <span
                                                    :class="['text-sm', type === 'Checkbox' ? 'order-2' : 'order-1 font-medium text-black']">
                                                    {{ field.label || 'Field Label' }}
                                                </span>
                                            </div>
                                        </div>

                                        <InputError
                                            :message="form.errors[`fields.${form.fields.indexOf(field)}.label` as keyof typeof form.errors]" />
                                    </section>
                                </div>

                            </div>
                            <div v-if="form.has_cost" class="space-y-4 my-4">
                                <Label class="text-[10px] text-zinc-500 uppercase font-bold mb-1 block">Cost Field
                                    Preview</Label>
                                <Input disabled placeholder="User will enter cost amount..."
                                    class="h-8 text-xs" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
                <Card class="h-fit">
                    <CardHeader>
                        <CardTitle>Additional details</CardTitle>
                        <CardDescription>additional details</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Label for="task-type">Task type</Label>
                            <Select id="task-type" v-model="form.type">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="preset">Preset</SelectItem>
                                    <SelectItem value="custom">Custom</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div v-if="form.type === 'preset'"
                            class="space-y-4 mt-4 animate-in fade-in slide-in-from-top-1">
                            <Label for="preset-selection">Choose Preset</Label>
                            <Select @update:modelValue="(val) => applyPreset(val as string)" class="w-full"
                                :disabled="presets.length === 0">
                                <SelectTrigger class="w-full">
                                    <SelectValue class="w-full" placeholder="Select a template..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="preset in presets" :key="preset.id" :value="preset.id"
                                        :disabled="presets.length === 0">
                                        {{ preset.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p class="text-[10px] text-zinc-500 italic">Selecting a preset will populate the input
                                fields automatically.</p>
                        </div>

                        <Separator class="my-4" />
                        <section class="space-y-4">
                            <Label for="step-type">Assign to </Label>
                            <Popover v-model:open="openCombobox">
                                <PopoverTrigger as-child>
                                    <Button variant="outline" role="combobox" :class="cn(
                                        'w-full justify-between',
                                        form.assigned_to
                                            ? 'text-black dark:text-white'
                                            : 'text-zinc-400',
                                    )
                                        ">
                                        {{ assignedUser }}
                                        <ChevronsUpDownIcon class="opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-full p-0">
                                    <Command>
                                        <CommandInput class="h-9 w-xs" placeholder="Search user..." />
                                        <CommandList>
                                            <CommandEmpty>No user found.</CommandEmpty>
                                            <CommandGroup>
                                                <CommandItem value="Anyone" @select="
                                                    () => {
                                                        form.assigned_to = null;
                                                        openCombobox = false;
                                                    }
                                                ">
                                                    Anyone
                                                    <CheckIcon v-show="form.assigned_to === null
                                                        " class="ml-auto" />
                                                </CommandItem>
                                                <CommandItem v-for="user in props.users" :key="user.id"
                                                    :value="String(user.id)" @select="
                                                        () => {
                                                            form.assigned_to =
                                                                form.assigned_to ===
                                                                    String(user.id)
                                                                    ? null
                                                                    : String(
                                                                        user.id,
                                                                    );
                                                            openCombobox = false;
                                                        }
                                                    ">
                                                    {{ user.name }}

                                                    <CheckIcon v-show="form.assigned_to ===
                                                        String(user.id)
                                                        " class="ml-auto" />
                                                </CommandItem>
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                            <InputError :message="form.errors.assigned_to" />
                        </section>


                        <Separator class="my-4" />
                        <section class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <Checkbox id="has-cost" v-model="form.has_cost" />
                                    <Label for="has-cost">has cost</Label>
                                </div>
                                <p class="text-sm text-zinc-500">
                                    Check this if the step has a cost; you can enter an amount below.
                                </p>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <Checkbox id="add-new" v-model="form.again" />
                                    <Label for="add-new">Add another</Label>
                                </div>
                                <p class="text-sm text-zinc-500">
                                    Keep checked to stay on this form and quickly add another step.
                                </p>
                            </div>
                        </section>
                    </CardContent>
                </Card>
            </section>
        </form>
        <!-- <pre>{{ props }}</pre> -->
        <!-- <pre>{{ form }}</pre> -->
    </div>
</template>
