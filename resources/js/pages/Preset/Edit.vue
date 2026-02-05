<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Trash2Icon } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import presetRoute from '@/routes/preset';
import { BreadcrumbItem, Preset } from '@/types';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { Separator } from '@/components/ui/separator';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import preset from '@/routes/preset';

const props = defineProps<{
    preset: Preset
}>();

const form = useForm({
    name: props.preset.name,
    description: props.preset.description,
    order: props.preset.order,
    
    has_cost: props.preset.has_cost,
    step_cost: null as number | null,

    fields: (props.preset.fields ?? []).map(field => ({
        ...field,
        id: field.id ?? crypto.randomUUID()
    })) as {
        id: string;
        type: 'Checkbox' | 'Input' | 'Description';
        label: string
    }[],
});

const updatePreset = () => {
    form.patch(presetRoute.update(props.preset.id).url, {
        onSuccess: () => {
            // Optional: Add success notification
        }
    });
};

const discardEdit = () => {
    form.reset();
};

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

const groupedFields = computed(() => {
    return form.fields.reduce((acc, field) => {
        const type = field.type;
        if (!acc[type]) acc[type] = [];
        acc[type].push(field);
        return acc;
    }, {} as Record<string, typeof form.fields>);
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Preset',
        href: preset.index().url,
    },
    {
        title: 'Edit',
        href: '',
    }
]
</script>

<template>

    <Head title="Edit Preset" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col flex-1 gap-4 p-4">
            <form @submit.prevent="updatePreset" class="space-y-4">
                <section class="flex justify-between items-center">
                    <div>
                        <h3>Edit Preset</h3>
                    </div>
                    <div class="space-x-2">
                        <Button size="sm" type="button" variant="destructive" @click="discardEdit">Discard</Button>
                        <Button size="sm" type="submit" variant="default" :disabled="form.processing">
                            {{ form.processing ? 'Updating...' : 'Update' }}
                        </Button>
                    </div>
                </section>
                <section class="grid grid-cols-[2fr_1fr] gap-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>Present Details</CardTitle>
                            <CardDescription>Present description</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <section class="space-y-4">
                                <div class="space-y-4">
                                    <Label for="name">Present Name</Label>
                                    <Input id="name" v-model="form.name" />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="space-y-4">
                                    <Label for="description">Present Description</Label>
                                    <Input id="description" v-model="form.description" />
                                    <InputError :message="form.errors.description" />
                                </div>
                                <div class="space-y-4">
                                    <section class="flex gap-2 items-center justify-between">
                                        <div>
                                            <h3 class="text-lg">User Input Fields</h3>
                                            <p class="text-xs text-muted-foreground">user input fields</p>
                                        </div>
                                        <div>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <Button size="sm" variant="outline" class="gap-2">
                                                        <Plus class="w-4 h-4" /> Add Field
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <DropdownMenuItem @click="addField('Description')">Textarea
                                                        (Description)
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem @click="addField('Checkbox')">Checkbox
                                                        (Confirmation)
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem @click="addField('Input')">Small Input
                                                        (Text/Number)
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </div>
                                    </section>
                                    <Separator class="my-2" />
                                    <section v-if="form.fields.length === 0"
                                        class="border-2 border-dashed border-zinc-800 rounded-lg p-6 text-center">
                                        <p class="text-sm text-zinc-500">No input fields added. The User will just mark
                                            this
                                            step as complete.</p>
                                    </section>

                                    <section v-for="(fields, type) in groupedFields" :key="type" class="space-y-4">
                                        <Label
                                            class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800/50 pb-1 block">
                                            {{ type }}{{ type === 'Checkbox' ? 'es' : 's' }}
                                        </Label>

                                        <div class="space-y-4">
                                            <section v-for="field in fields" :key="field.id"
                                                class="relative p-4 rounded-xl border border-zinc-800 bg-zinc-900/30 space-y-3 group">

                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1 mr-4">
                                                        <Label
                                                            class="text-[10px] text-zinc-500 uppercase font-bold mb-1 block">Field
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
                                                    class="mt-4 pt-4 border-t border-zinc-800/50 opacity-40 grayscale pointer-events-none">
                                                    <p class="text-[9px] uppercase font-bold text-zinc-600 mb-2">User
                                                        Response
                                                        Preview</p>

                                                    <div
                                                        :class="['flex gap-3', type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start']">

                                                        <div
                                                            :class="[type === 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                                                            <Checkbox v-if="type === 'Checkbox'" class="rounded-sm" />

                                                            <Input v-if="type === 'Input'" disabled
                                                                :placeholder="`User will enter ${field.label || 'data'}...`"
                                                                class="h-8 text-xs bg-zinc-900/50" />

                                                            <Textarea v-if="type === 'Description'" disabled
                                                                :placeholder="`User will provide ${field.label || 'details'}...`"
                                                                class="min-h-[60px] text-xs bg-zinc-900/50 resize-none" />
                                                        </div>

                                                        <span
                                                            :class="['text-sm', type === 'Checkbox' ? 'order-2' : 'order-1 font-medium text-zinc-300']">
                                                            {{ field.label || 'Field Label' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <InputError
                                                    :message="form.errors[`fields.${form.fields.indexOf(field)}.label` as keyof typeof form.errors]" />
                                            </section>
                                        </div>
                                    </section>
                                    <div v-if="form.has_cost" class="space-y-4 my-4">
                                        <Label class="text-[10px] text-zinc-500 uppercase font-bold mb-1 block">Cost
                                            Field Preview</Label>
                                        <Input disabled placeholder="User will enter cost amount..."
                                            class="h-8 text-xs  " />

                                    </div>
                                </div>
                            </section>
                        </CardContent>
                    </Card>
                    <Card class="h-fit">
                        <CardHeader>
                            <CardTitle>Additional details</CardTitle>
                            <CardDescription>Additional details</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <section class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="order">Order</Label>
                                    <Select id="order" v-model="form.order">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select a order" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="sequencial">
                                                    Sequencial
                                                </SelectItem>
                                                <SelectItem value="random">
                                                    Random
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <Separator class="my-4" />

                                <div class="space-y-2">
                                    <div class="flex gap-2">
                                        <Checkbox id="has-cost" v-model="form.has_cost" />
                                        <Label for="has-cost">Has cost</Label>
                                    </div>
                                    <p class="text-sm text-zinc-500">Check this if the step has a cost; you can enter an
                                        amount below.</p>
                                </div>

                            </section>
                        </CardContent>
                    </Card>
                </section>
                <!-- <div>
                    <pre>{{ form.has_cost }}</pre>
                </div> -->
            </form>
        </div>
    </AppLayout>
</template>