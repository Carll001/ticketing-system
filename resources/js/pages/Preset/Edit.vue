<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import presetRoute from '@/routes/preset';
import { Preset } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Trash2Icon } from 'lucide-vue-next';
import { computed } from 'vue';


const props = defineProps<{
    preset: Preset
}>();

const form = useForm({
    name: props.preset.name,
    description: props.preset.description,
    fields: (props.preset.fields ?? []).map(field => ({
        ...field,
        id: field.id ?? crypto.randomUUID()
    })) as {
        id: string;
        type: 'Checkbox' | 'Input' | 'Description';
        label: string
    }[],
});

const groupedFields = computed(() => {
    return form.fields.reduce((acc, field) => {
        const type = field.type;
        if (!acc[type]) acc[type] = [];
        acc[type].push(field);
        return acc;
    }, {} as Record<string, typeof form.fields>);
});

const addField = (type: 'Checkbox' | 'Input' | 'Description') => {
    form.fields.push({
        id: crypto.randomUUID(),
        type: type,
        label: '',
    });
};

const removeField = (index: number) => {
    form.fields.splice(index, 1);
};

const updatePreset = () => {
    form.patch(presetRoute.update(props.preset.id).url);
};

const discardEdit = () => {
    form.reset();
};
</script>

<template>

    <Head title="Edit Preset" />
    <AppLayout>
        <div class="p-6">
            <form @submit.prevent="updatePreset" class="space-y-6">
                <section class="flex justify-between items-start">
                    <Heading :title="`Editing: ${props.preset.name}`" />
                    <div class="flex items-center gap-2">
                        <Button size="sm" type="button" variant="destructive" @click="discardEdit">Discard</Button>
                        <Button size="sm" type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Updating...' : 'Update Preset' }}
                        </Button>
                    </div>
                </section>

                <section class="grid grid-cols-[2fr_1fr] gap-6">
                    <div class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Preset Details</CardTitle>
                                <CardDescription>Update the primary information for this template</CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="name">Preset Name</Label>
                                    <Input id="name" v-model="form.name" placeholder="e.g. Standard Inspection" />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="description">Description</Label>
                                    <Textarea id="description" v-model="form.description"
                                        placeholder="What is this preset for?" />
                                    <InputError :message="form.errors.description" />
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader class="flex flex-row items-center justify-between space-y-0">
                                <div>
                                    <CardTitle>User Input Fields</CardTitle>
                                    <CardDescription>Define what the user must provide during execution
                                    </CardDescription>
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
                            </CardHeader>
                            <CardContent class="space-y-8">
                                <div v-if="form.fields.length === 0"
                                    class="border-2 border-dashed border-zinc-800 rounded-lg p-10 text-center">
                                    <p class="text-sm text-zinc-500">No input fields added yet.</p>
                                </div>

                                <div v-for="(fields, type) in groupedFields" :key="type" class="space-y-4">
                                    <Label
                                        class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800 pb-1 block">
                                        {{ type }}{{ type === 'Checkbox' ? 'es' : 's' }}
                                    </Label>

                                    <div class="space-y-4">
                                        <section v-for="field in fields" :key="field.id"
                                            class="relative p-4 rounded-xl border border-zinc-800 bg-zinc-900/30 space-y-3">

                                            <div class="flex items-end justify-between">
                                                <div class="flex-1 mr-4">
                                                    <Label
                                                        class="text-[10px] text-zinc-500 uppercase font-bold mb-1 block">Field
                                                        Label / Question</Label>
                                                    <Input v-model="form.fields[form.fields.indexOf(field)].label"
                                                        :placeholder="`e.g. ${type === 'Checkbox' ? 'Check if confirmed' : 'Enter detail name'}`" />
                                                </div>
                                                <Button variant="ghost" size="icon"
                                                    class="h-8 w-8 text-zinc-500 hover:text-red-500 shrink-0 mb-1"
                                                    @click="removeField(form.fields.indexOf(field))">
                                                    <Trash2Icon class="w-4 h-4" />
                                                </Button>
                                            </div>

                                            <div
                                                class="mt-4 pt-4 border-t border-zinc-800/50 opacity-40 grayscale pointer-events-none">
                                                <p class="text-[9px] uppercase font-bold text-zinc-600 mb-2">User
                                                    Response Preview</p>

                                                <div
                                                    :class="['flex gap-3', type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start']">
                                                    <div :class="[type === 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                                                        <div v-if="type === 'Checkbox'"
                                                            class="w-4 h-4 rounded border border-zinc-700 bg-zinc-900/50">
                                                        </div>
                                                        <Input v-if="type === 'Input'" disabled
                                                            class="h-8 text-xs bg-zinc-900/50" />
                                                        <Textarea v-if="type === 'Description'" disabled
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
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <div class="space-y-6">
                        <Card class="h-fit">
                            <CardHeader>
                                <CardTitle>Statistics</CardTitle>
                                <CardDescription>Usage and metadata</CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="pt-2">
                                    <Label class="text-xs text-zinc-500 uppercase">Field Count</Label>
                                    <p class="text-sm font-medium mt-1 text-zinc-300">{{ form.fields.length }} defined
                                        fields</p>
                                </div>
                                <div class="pt-2">
                                    <Label class="text-xs text-zinc-500 uppercase">Last Updated</Label>
                                    <p class="text-sm font-medium mt-1 text-zinc-300">Just now</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </section>
                <!-- <div>
                    <pre>{{ form }}</pre>
                </div> -->
            </form>
        </div>
    </AppLayout>
</template>