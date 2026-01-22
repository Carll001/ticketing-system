<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Trash2Icon, Save } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import preset from '@/routes/preset';
import { BreadcrumbItem } from '@/types';
import { Checkbox } from '@/components/ui/checkbox';

const form = useForm({
    name: '',
    description: '',
    has_cost: false,
});

const createField = () => {
    form.post(preset.store().url, {
        onSuccess: () => form.reset()
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Preset',
        href: '',
    },
    {
        title: 'Create',
        href: '',
    }
]
</script>

<template>

    <Head title="Presets" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col flex-1 gap-4 p-4">
            <form @submit.prevent="createField">
                <section class="flex justify-between items-center">
                    <div>
                        <h3>Add Custom Preset</h3>
                    </div>
                    <div class="space-x-2">
                        <Button size="sm" variant="destructive">Discard</Button>
                        <Button size="sm" variant="default">Create</Button>
                    </div>
                </section>
                <section class="grid grid-cols-[2fr_1fr] gap-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>Present Details</CardTitle>
                            <CardDescription>Present description</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <Label for="name">Present Name</Label>
                                <Input id="name" v-model="form.name" />
                            </div>
                            <div class="space-y-4">
                                <Label for="description">Present Description</Label>
                                <Input id="description" v-model="form.description" />
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Additional details</CardTitle>
                            <CardDescription>Additional details</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2">
                                <div class="flex gap-2">
                                    <Checkbox id="has-cost" v-model="form.has_cost" />
                                    <Label for="has-cost">Has cost</Label>
                                </div>  
                                <p class="text-sm text-zinc-500">Check this if the step has a cost; you can enter an amount below.</p>
                            </div>
                        </CardContent>
                    </Card>
                </section>
                <div>
                    <pre>{{ form }}</pre>
                </div>
            </form>
        </div>
    </AppLayout>
</template>