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
import { router, useForm, usePage } from '@inertiajs/vue3';
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
import { useCurrency } from '@/composables/useCurrency';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
const { formatCurrency, formatNumber } = useCurrency();

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
const acceptStep = (taskId: string, stepId: string) => {
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
const rejectStep = (taskId: string, stepId: string) => {
    router.patch(
        stepLink.updateStatus({ task: taskId, step: stepId }).url,
        { status: 'rejected' },
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
    taskOrder?: 'sequential' | 'random';
}>();


const rejectForm = useForm({
    status: 'rejected',
    reason: '',
});

const submitRejectForm = (taskId: string, stepId: string) => {
    // console.log('dddd');
    rejectForm.patch(
        stepLink.updateStatus({ task: taskId, step: stepId }).url,
        {
            preserveScroll: true,
            onSuccess: () => {
                rejectForm.reset();
                // Optional: show success toast
            },
            onError: () => {
                // Optional: show error toast
            }
        }
    );
};

const isStepLocked = (step: Step) => {
    // console.log('🔒 isStepLocked called for:', step.title);
    // console.log('  - taskOrder:', props.taskOrder);
    // console.log('  - step.position:', step.position);

    // If task is random order, no steps are locked
    if (props.taskOrder === 'random') {
        // console.log('  - Result: FALSE (random order)');
        return false;
    }

    // If not sequential, don't lock (safety fallback)
    if (props.taskOrder !== 'sequential') {
        // console.log('  - Result: FALSE (not sequential)');
        return false;
    }

    // Check if all previous steps (by position, not index) are accepted or completed
    const previousSteps = props.steps?.filter(s => s.position < step.position);
    // console.log('  - Previous steps:', previousSteps?.map(s => ({
    //     title: s.title,
    //     position: s.position,
    //     status: s.status
    // })));

    const result = previousSteps?.some(s => !['accepted', 'completed'].includes(s.status)) ?? false;
    // console.log('  - Has incomplete previous steps?', result);
    // console.log('  - Result:', result);

    return result;
};

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

const isCreator = computed(() => {
    return auth.value.user.id === props.creator?.id;
});

// Helper functions for button visibility
const canViewStep = (step: Step) => {
    if (isCreator.value) return true;

    if (step.assigned?.id === auth.value.user.id && ['accepted', 'in-progress', 'completed'].includes(step.status)) {
        return true;
    }

    if (isStepLocked(step)) return false;

    if (auth.value.user.role !== 'admin' && !['pending', 'assigned'].includes(step.status) && step.assigned.id === auth.value.user.id) {
        return true;
    }
    

    return false;
};

// Add this debug log
// console.log('StepCard props:', {
//     taskOrder: props.taskOrder,
//     stepsCount: props.steps?.length,
//     steps: props.steps?.map(s => ({ id: s.id, position: s.position, status: s.status }))
// });
const canTakeStep = (step: Step) => {
    // console.log('=== canTakeStep for:', step.title, '===');
    // console.log('taskOrder:', props.taskOrder);
    // console.log('isCreator:', isCreator.value);
    // console.log('assigned_to:', step.assigned_to);
    // console.log('isStepLocked result:', isStepLocked(step));

    // Can't take if you're the creator
    if (isCreator.value) {
        // console.log('❌ Cannot take: You are creator');
        return false;
    }

    // Can't take if already assigned to someone
    if (step.assigned_to !== null) {
        // console.log('❌ Cannot take: Already assigned');
        return false;
    }

    // Can't take if locked (this already handles sequential order check)
    if (isStepLocked(step)) {
        // console.log('❌ Cannot take: Step is locked');
        return false;
    }

    // console.log('✅ CAN TAKE');
    return true;
};

const canRejectStep = (step: Step) => {
    return !isStepLocked(step) &&
        !isCreator.value &&
        step.status === 'assigned' &&
        step.assigned_to === auth.value.user.id;
};

const canAcceptStep = (step: Step) => {
    return !isStepLocked(step) &&
        !isCreator.value &&
        step.status === 'assigned' &&
        step.assigned_to === auth.value.user.id;
};

// Emit event to parent component to change active tab
const emit = defineEmits<{
    filterByStatus: [status: string]
}>();

// Handle status button click
const handleStatusClick = (status: string) => {
    emit('filterByStatus', status);
}

</script>
<template>
    <Collapsible v-for="step in props.steps" :key="step.id" :open="openStepId === step.id">

        <Card :class="{ 'opacity-50': isStepLocked(step) }">

            <div class="flex flex-row px-2">
                <Button variant="ghost" size="icon-sm" class="" @click="toggleStep(step.id)">
                    <ChevronDown v-if="openStepId === step.id" />
                    <ChevronRight v-else />
                </Button>
                <div class="flex-1">
                    <CardHeader class="w-full m-0 px-2">
                        <div class="flex items-start justify-between ">
                            <section class="space-y-2">
                                <CardTitle class="break-all">{{ step.title }}</CardTitle>
                                <CardDescription>
                                    <div class="flex items-center gap-2 break-all">
                                        <Button class="h-6 p-2 text-xs" variant="outline">
                                            {{ step.assigned?.name ?? 'Anyone' }}
                                        </Button>
                                        <Button class="h-6 p-2 text-xs capitalize" variant="outline"
                                            @click="handleStatusClick(step.status)">
                                            {{ step.status }}
                                        </Button>
                                    </div>
                                    <p class="text-xs" v-if="step.has_cost">
                                        <span class="font-bold">Cost: </span>{{ formatCurrency(step.cost) }}
                                    </p>
                                </CardDescription>
                            </section>
                            <section class="space-x-2">
                                <Button v-if="canViewStep(step)" size="sm" @click="showStep(step.task_id, step.id)" variant="outline">
                                    View Step
                                </Button>

                                <p v-if="isStepLocked(step)" class="text-muted-foreground text-sm">
                                    Locked
                                </p>

                                <Button v-if="canTakeStep(step)" size="sm" @click="takeStep(step.task_id, step.id)">
                                    Take
                                </Button>

                                <Dialog v-if="canRejectStep(step)">
                                    <DialogTrigger as-child>
                                        <Button size="sm" variant="destructive">
                                            Reject
                                        </Button>
                                    </DialogTrigger>

                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>Reject Step</DialogTitle>
                                            <DialogDescription>
                                                Please provide a reason for rejecting this step. This will be sent to
                                                the task creator.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <div class="space-y-4 py-4">
                                            <div class="space-y-2">
                                                <Label for="reject-message">Reason for rejection</Label>
                                                <Textarea id="reject-message" v-model="rejectForm.reason"
                                                    placeholder="Explain why you're rejecting this step..."
                                                    class="min-h-[100px]" />
                                            </div>
                                        </div>

                                        <DialogFooter>
                                            <DialogClose as-child>
                                                <Button variant="ghost">Cancel</Button>
                                            </DialogClose>
                                            <Button variant="destructive"
                                                @click="submitRejectForm(step.task_id, step.id)"
                                                :disabled="!rejectForm.reason.trim()">
                                                Reject Step
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                                <!-- <Button v-if="canRejectStep(step)" size="sm" @click="rejectStep(step.task_id, step.id)">
                                    Reject
                                </Button> -->

                                <Button v-if="canAcceptStep(step)" size="sm" @click="acceptStep(step.task_id, step.id)">
                                    Accept
                                </Button>
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
                                                    :disabled="auth.user.id === props.creator?.id"
                                                    :placeholder="`Enter ${field.label.toLowerCase()}...`"
                                                    class="h-8 text-xs " />

                                                <Textarea v-else-if="type === 'Description'"
                                                    :model-value="field.responses?.[0]?.response ?? ''" readonly
                                                    disabled
                                                    :placeholder="`Provide details for ${field.label.toLowerCase()}...`"
                                                    class="min-h-[60px] text-xs  resize-none" />

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
                            <div v-if="step.has_cost" class="space-y-4 my-4">
                                <Label class="text-[10px] text-zinc-500 uppercase font-bold mb-1 block">Cost Field
                                    Preview</Label>
                                <Input disabled placeholder="User will enter cost amount..."
                                    :model-value="formatCurrency(step.cost)" class="h-8 text-xs  " />
                            </div>
                            <div v-if="!step.fields?.length" class="text-xs text-zinc-600 italic text-center">
                                No fields configured for this step.
                            </div>
                        </div>

                    </CollapsibleContent>
                </div>
            </div>

        </Card>
        <!-- <pre>{{ rejectForm }}</pre> -->
    </Collapsible>
</template>