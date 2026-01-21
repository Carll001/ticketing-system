<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import stepLink from '@/routes/step';
import { Step } from '@/types';
import { router } from '@inertiajs/vue3';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '../ui/collapsible';
import { computed, ref } from 'vue';
import { ChevronDown, ChevronRight, Pen } from 'lucide-vue-next';
import { Separator } from '../ui/separator';
import { Label } from '../ui/label';
import { Input } from '../ui/input';
import { Textarea } from '../ui/textarea';
import StepDeleteDialog from './StepDeleteDialog.vue';
import { Checkbox } from '../ui/checkbox';

const props = defineProps<{
    steps?: Step[];
}>();

const openStepId = ref<string | null>(null);

// FOR COLLAPSIBLE // IF STEP IS OPEN = CLOSE ELSE OPEN THE CLICKED STEP
const toggleStep = (id: string) => {
    openStepId.value = openStepId.value === id ? null : id;
}

const editStep = (task_id: string, step_id: string) => {
    router.visit(stepLink.edit({ task: task_id, step: step_id }).url, {
        preserveScroll: true,
    });
};

const showStep = (task_id: string, step_id: string) => {
    router.visit(stepLink.show({ task: task_id, step: step_id }).url)
}
const groupedFields = computed(() => {
    if (!props.steps) return [];

    // This creates an object where keys are the 'type' 
    // and values are arrays of fields of that type
    return props.steps.map(step => {
        const groups = step.fields?.reduce((acc, field) => {
            const type = field.type;
            if (!acc[type]) acc[type] = [];
            acc[type].push(field);
            return acc;
        }, {} as Record<string, typeof step.fields>);

        return { stepId: step.id, groups };
    });
});

// Helper to get groups for a specific step
const getGroupsForStep = (stepId: string) => {
    return groupedFields.value.find(g => g.stepId === stepId)?.groups || {};
};
</script>
<template>
    <Collapsible v-for="step in props.steps" :key="step.id" :open="openStepId === step.id">
        <Card>
            <div class="flex flex-row px-2">
                <Button variant="ghost" size="icon-sm" class="" @click="toggleStep(step.id)">
                    <ChevronDown v-if="openStepId === step.id" />
                    <ChevronRight v-else />
                </Button>
                <div class="flex-1">
                    <CardHeader class="w-full m-0 px-2">
                        <div class="flex items-start justify-between ">
                            <section class="space-y-2">
                                <CardTitle>{{ step.title }}</CardTitle>
                                <CardDescription>
                                    <div class="flex items-center gap-2">
                                        <Button class="h-6 p-2 text-xs" variant="outline">
                                            {{ step.assigned?.name ?? 'Anyone' }}
                                        </Button>
                                        <Button class="h-6 p-2 text-xs capitalize" variant="outline">
                                            {{ step.status }}
                                        </Button>
                                    </div>
                                </CardDescription>
                            </section>
                            <section class="">
                                <Button size="sm" @click="showStep(step.task_id, step.id)">View Step</Button>
                            </section>
                        </div>

                    </CardHeader>

                    <Separator v-if="openStepId === step.id" class="my-4" />

                    <CollapsibleContent>
                        <div class="space-y-8 px-10 ">
                            <div v-for="(fields, type) in getGroupsForStep(step.id)" :key="type" class="space-y-4">

                                <Label
                                    class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800 pb-1 block">

                                    {{ type }}{{ type === 'Checkbox' ? 'es' : 's' }}
                                </Label>

                                <div class="space-y-4 pl-2">
                                    <div v-for="field in fields" :key="field.id" class="flex gap-3"
                                        :class="type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start'">

                                        <div :class="[type === 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                                            <template v-if="type === 'Input'">
                                                <input disabled :value="field.responses?.[0]?.response ?? ''"
                                                    class="flex h-8 w-full rounded-md border border-zinc-800 bg-zinc-900/50 px-3 py-1 text-xs shadow-sm transition-colors text-zinc-100 opacity-100 cursor-not-allowed" />
                                            </template>

                                            <template v-else-if="type === 'Description'">
                                                <textarea disabled :value="field.responses?.[0]?.response ?? ''"
                                                    class="flex min-h-[60px] w-full rounded-md border border-zinc-800 bg-zinc-900/50 px-3 py-2 text-xs shadow-sm text-zinc-100 opacity-100 cursor-not-allowed resize-none"></textarea>
                                            </template>

                                            <div v-else-if="type === 'Checkbox'"
                                                class="flex items-center justify-center">
                                                <Checkbox :id="field.id" disabled
                                                    :model-value="field.responses?.[0]?.response === 'true'"
                                                    class="opacity-100 cursor-not-allowed" />
                                            </div>
                                        </div>

                                        <Label :class="[
                                            'text-xs font-medium text-zinc-300',
                                            type === 'Checkbox' ? 'order-2' : 'order-1'
                                        ]">
                                            {{ field.label }}
                                        </Label>
                                        <!-- <div class="mt-10 p-4 bg-zinc-900 rounded border border-zinc-800">
                                            <p class="text-[10px] text-zinc-500 uppercase mb-2">Form Live Data Debug:
                                            </p>
                                            <pre class="text-xs text-green-400">{{ field.responses?.[0] }}</pre>
                                        </div> -->
                                    </div>

                                </div>

                            </div>

                            <div v-if="!step.fields?.length" class="text-xs text-zinc-600 italic text-center">
                                No fields configured for this step.
                            </div>
                        </div>

                    </CollapsibleContent>
                </div>

            </div>

        </Card>
    </Collapsible>
</template>
