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
import { Step, User } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
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

const page = usePage();
const auth = computed(() => page.props.auth);

const takeStep = (taskId: string, stepId: string) => {
    router.patch(
        stepLink.updateStatus({ task: taskId, step: stepId }).url,
        { status: 'accepted' },
        {
            preserveScroll: true,
            onSuccess: () => {
                // Optional: show success message or toast
            }
        }
    );
};

const props = defineProps<{
    steps?: Step[];
    creator?: User;
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
                            <section class="space-x-2">
                                <Button size="sm" @click="showStep(step.task_id, step.id)">View Step</Button>
                                <Button size="sm" v-if="creator?.id !== auth.user.id && step.assigned_to === null"
                                    @click="takeStep(step.task_id, step.id)">Take</Button>
                                <Button size="sm"
                                    v-if="creator?.id !== auth.user.id && step.status === 'assigned' && step.assigned_to === auth.user.id"
                                    @click="takeStep(step.task_id, step.id)">Accept</Button>
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
                                    <div v-for="field in fields" :key="field.id" class="space-y-4">
                                        <div class="flex gap-3"
                                            :class="type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start'">
                                            <div :class="[type === 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                                                <Input v-if="type === 'Input'"
                                                    :model-value="field.responses?.[0]?.response ?? ''" readonly
                                                    disabled :placeholder="`Enter ${field.label.toLowerCase()}...`"
                                                    class="h-8 text-xs bg-zinc-900/50" />

                                                <Textarea v-else-if="type === 'Description'"
                                                    :model-value="field.responses?.[0]?.response ?? ''" readonly
                                                    disabled
                                                    :placeholder="`Provide details for ${field.label.toLowerCase()}...`"
                                                    class="min-h-[60px] text-xs bg-zinc-900/50 resize-none" />

                                                <div v-else-if="type === 'Checkbox'" class="flex items-center">
                                                    <Checkbox :id="field.id"
                                                        :model-value="field.responses?.[0]?.response === 'true'"
                                                        disabled />
                                                </div>

                                            </div>

                                            <Label :class="[
                                                'text-xs font-medium text-zinc-300',
                                                type === 'Checkbox' ? 'order-2' : 'order-1'
                                            ]">
                                                {{ field.label }}
                                            </Label>
                                        </div>
                                        <p v-if="field.responses && field.responses[0]"
                                            class=" text-[10px] text-zinc-500 italic ">
                                            Answered by <span class=" text-zinc-400 font-medium">{{
                                                field.responses?.[0].user?.name ??
                                                'Someone' }}</span>
                                            on {{ new Date(field.responses?.[0].created_at || '').toLocaleDateString()
                                            }}
                                            at {{ new Date(field.responses?.[0].created_at || '').toLocaleTimeString([],
                                                {
                                                    hour:
                                                        '2-digit', minute: '2-digit'
                                                }) }}
                                        </p>
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
            <!-- <pre>{{ props }}</pre> -->
        </Card>
    </Collapsible>
</template>
