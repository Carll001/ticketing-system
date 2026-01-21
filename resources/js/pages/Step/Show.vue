<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input'; // Added missing imports
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import stepLink from '@/routes/step';
import taskLink from '@/routes/task';
import { BreadcrumbItem, Step, Field } from '@/types'; // Ensure Field type is available
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, reactive, watch } from 'vue';
import EmptyData from '@/components/EmptyData.vue';
import { FileQuestion, Plus } from 'lucide-vue-next';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import StepDeleteDialog from '@/components/task-step-components/StepDeleteDialog.vue';
import { Checkbox } from '@/components/ui/checkbox';
import response from '@/routes/response';
interface FormState {
    step_field_id: string | number;
    response: Record<string, any>; // Flexible index signature
}

// 2. Define your Props
const props = defineProps<{
    step: { data: Step }
}>();


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: taskLink.index().url,
    },
    {
        title: props.step.data.task.title,
        href: taskLink.show(props.step.data.task_id).url,
    },
    {
        title: props.step.data.title,
        href: stepLink.show({ task: props.step.data.task_id, step: props.step.data.id }).url,
    },
];

// Simplified grouping: only group fields for the current step
const groupedFields = computed(() => {
    const fields = props.step.data.fields || [];

    return fields.reduce((acc, field) => {
        const type = field.type;
        if (!acc[type]) acc[type] = [];
        acc[type].push(field);
        return acc;
    }, {} as Record<string, Field[]>);
});

const form = useForm<{
    step_field_id: string | number;
    response: Record<string, any>;
}>({
    step_field_id: props.step.data.id,
    response: (props.step.data.fields || []).reduce((acc, field) => {
        // Grab the first response for the current user
        const existingValue = field.responses?.[0]?.response;

        if (field.type === 'Checkbox') {
            acc[field.id] = existingValue !== undefined ? existingValue : "0";
        } else {
            acc[field.id] = existingValue || '';
        }
        return acc;
    }, {} as Record<string, any>)
});

// IMPORTANT: This watch updates the form inputs when Laravel sends back fresh data
watch(() => props.step.data.fields, (newFields) => {
    newFields?.forEach(field => {
        const freshValue = field.responses?.[0]?.response;
        if (freshValue !== undefined) {
            form.response[field.id] = freshValue;
        }
    });
}, { deep: true });

const editStep = (task_id: string, step_id: string) => {
    router.visit(stepLink.edit({ task: task_id, step: step_id }).url, {
        preserveScroll: true,
    });
};

const submitResponse = () => {
    // form.post is now available because we used useForm
    form.post(response.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: clear or handle success
        },
    });
};
</script>
<template>

    <Head :title="step.data.title ?? 'Undefined'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col flex-1 gap-4 p-4">
            <div class="flex items-start justify-between">
                <section>
                    <h3 class="text-3xl font-bold">{{ props.step.data.title }}</h3>
                    <p class="text-muted-foreground">{{ props.step.data.description ?? 'No description' }}</p>
                </section>

                <section class="flex gap-2 flex-col items-end mb-6">
                    <div class="flex gap-2 items-center">
                        <p class="text-muted-foreground text-sm">Assigned to:</p>
                        <Button class="text-xs p-2 h-8" variant="outline">
                            {{ props.step.data.assigned?.name ?? 'Anyone' }}
                        </Button>
                    </div>
                    <div class="space-x-2">
                        <StepDeleteDialog :step="props.step.data" />
                        <Button size="sm" @click="editStep(props.step.data.task_id, props.step.data.id)">Edit</Button>
                        <Button @click="submitResponse">Submit Response</Button>
                    </div>
                </section>

            </div>
            <div class="space-y-8">
                <div v-for="(fields, type) in groupedFields" :key="type" class="space-y-4">
                    <Label
                        class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800 pb-1 block">
                        {{ type }}{{ type === 'Checkbox' ? 'es' : 's' }}
                    </Label>

                    <div class="space-y-4 pl-2">
                        <div v-for="field in fields" :key="field.id" class="space-y-4">
                            <div class="flex gap-3"
                                :class="type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start'">
                                <div :class="[type === 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                                    <Input v-if="type === 'Input'" v-model="form.response[field.id]"
                                        :placeholder="`Enter ${field.label.toLowerCase()}...`"
                                        class="h-8 text-xs bg-zinc-900/50" />

                                    <Textarea v-else-if="type === 'Description'" v-model="form.response[field.id]"
                                        :placeholder="`Provide details for ${field.label.toLowerCase()}...`"
                                        class="min-h-[60px] text-xs bg-zinc-900/50 resize-none" />

                                    <div v-else-if="type === 'Checkbox'" class="flex items-center">
                                        <Checkbox :id="field.id" :model-value="form.response[field.id] === 'true'"
                                            @update:model-value="(val) => form.response[field.id] = val ? 'true' : 'false'" />
                                    </div>

                                </div>

                                <Label :class="[
                                    'text-xs font-medium text-zinc-300',
                                    type === 'Checkbox' ? 'order-2' : 'order-1'
                                ]">
                                    {{ field.label }}
                                </Label>
                            </div>
                            <p v-if="field.responses?.[0] !== null" class="text-[10px] text-zinc-500 italic ">
                                Answered by <span class="text-zinc-400 font-medium">{{ field.responses?.[0].user?.name ??
                                    'Someone' }}</span>
                                on {{ new Date(field.responses?.[0].created_at || '').toLocaleDateString() }}
                                at {{ new Date(field.responses?.[0].created_at || '').toLocaleTimeString([], {
                                    hour:
                                '2-digit', minute: '2-digit' }) }}
                            </p>
                        </div>
                    </div>

                </div>
                <!-- <div class="mt-10 p-4 bg-zinc-900 rounded border border-zinc-800">
                    <p class="text-[10px] text-zinc-500 uppercase mb-2">Form Live Data Debug:</p>
                    <pre class="text-xs text-green-400">{{ form.response }}</pre>
                </div> -->

                <div class="text-xs text-zinc-600 text-center mx-auto">
                    <EmptyData :icon="FileQuestion" title="no fields yet" message="No fields configured for this step."
                        :length="props.step.data.fields?.length === 0" />
                </div>

            </div>
        </div>
    </AppLayout>
</template>