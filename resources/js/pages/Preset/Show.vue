<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import preset from '@/routes/preset';
import { BreadcrumbItem, Field, Preset } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    preset: Preset
}>();

// Simplified grouping: only group fields for the current step
const groupedFields = computed(() => {
    const fields = props.preset.fields || [];

    return fields.reduce((acc, field) => {
        const type = field.type;
        if (!acc[type]) acc[type] = [];
        acc[type].push(field);
        return acc;
    }, {} as Record<string, Field[]>);
});

const editField = () => {
    router.visit(preset.edit(props.preset.id).url)
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Preset',
        href: preset.index().url,
    },
    {
        title: 'View',
        href: preset.index().url,
    }
]
</script>

<template>

    <Head title="View Preset" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col flex-1 gap-4 p-4">
            <section class="flex justify-between items-center">
                <div>
                    <h3>View Preset</h3>
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
                                <Input id="name" v-model="props.preset.name" readonly class="bg-zinc-900/50" />
                            </div>
                            <div class="space-y-4">
                                <Label for="description">Present Description</Label>
                                <Input id="description" v-model="props.preset.description" readonly class="bg-zinc-900/50" />
                            </div>
                            <div class="space-y-4">
                                <section class="flex gap-2 items-center justify-between">
                                    <div>
                                        <h3 class="text-lg">User Input Fields</h3>
                                        <p class="text-xs text-muted-foreground">user input fields</p>
                                    </div>
                                </section>
                                <Separator class="my-2" />
                                <section v-if="Object.keys(groupedFields).length === 0"
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
                                            class="relative p-4 rounded-xl border border-zinc-800 bg-zinc-900/30 space-y-3">

                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 mr-4">
                                                    <Label
                                                        class="text-[10px] text-zinc-500 uppercase font-bold mb-1 block">Field
                                                        Label / Question</Label>
                                                    <Input :value="field.label" readonly class="bg-zinc-900/50" />
                                                </div>
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
                                                        <Checkbox v-if="type === 'Checkbox'" class="rounded-sm" disabled />

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
                                        </section>
                                    </div>
                                </section>
                                <div v-if="props.preset.has_cost" class="space-y-4 my-4">
                                        <Label class="text-[10px] text-zinc-500 uppercase font-bold mb-1 block">Cost
                                            Field Preview</Label>
                                        <Input disabled placeholder="User will enter cost amount..."
                                            class="h-8 text-xs bg-zinc-900/50 " />

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
                                <Select id="order" disabled>
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
                                    <Checkbox id="has-cost" disabled v-model="props.preset.has_cost" />
                                    <Label for="has-cost">Has cost</Label>
                                </div>
                                <p class="text-sm text-zinc-500">Check this if the step has a cost; you can enter an
                                    amount below.</p>
                            </div>

                        </section>
                    </CardContent>
                </Card>
            </section>
        </div>

        <!-- <pre>{{ props.preset }}</pre> -->
    </AppLayout>
</template>