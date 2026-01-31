<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import stepLink from '@/routes/step';
import taskLink from '@/routes/task';
import { BreadcrumbItem, Step } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import StepDeleteDialog from '@/components/task-step-components/StepDeleteDialog.vue';
import response from '@/routes/response';
import stepRoute from '@/routes/step';
import PermissionGuard from '@/components/PermissionGuard.vue';
import Proof from '@/components/task-step-components/Proof.vue';
import Comment from '@/components/task-step-components/Comment.vue';
import Fields from '@/components/task-step-components/Field.vue';
import { toast } from 'vue-sonner';
import EmptyData from '@/components/EmptyData.vue';
import { FileQuestion } from 'lucide-vue-next';

const page = usePage();
const auth = computed(() => page.props.auth);

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

// Initialize form with existing responses
const form = useForm<{
  step_field_id: string | number;
  response: Record<string, any>;
}>({
  step_field_id: props.step.data.id,
  response: (props.step.data.fields || []).reduce((acc, field) => {
    const existingValue = field.responses?.[0]?.response;

    if (field.type === 'Checkbox') {
      // Normalize checkbox values to boolean strings
      if (typeof existingValue === 'boolean') {
        acc[field.id] = existingValue ? 'true' : 'false';
      } else if (typeof existingValue === 'string') {
        acc[field.id] = (existingValue === 'true' || existingValue === '1') ? 'true' : 'false';
      } else {
        acc[field.id] = 'false';
      }
    } else {
      acc[field.id] = existingValue || '';
    }
    return acc;
  }, {} as Record<string, any>)
});

// Computed properties for UI state

// step status is complete
const isStepCompleted = computed(() => props.step.data.status === 'completed');

// step status is not completed and user is not the creator
const canSubmitResponse = computed(() => !isStepCompleted.value && auth.value.user.id !== props.step.data.task.creator_id);

// can mark complete if step is not completed
const canMarkComplete = computed(() => !isStepCompleted.value);

// can revert 
const canRevert = computed(() => isStepCompleted.value && auth.value.user.id !== props.step.data.task.creator_id);

// if step has fields
const hasFields = computed(() => props.step.data.fields && props.step.data.fields.length > 0);

const editStep = (task_id: string, step_id: string) => {
  router.visit(stepLink.edit({ task: task_id, step: step_id }).url, {
    preserveScroll: true,
  });
};

const submitResponse = () => {
  form.post(response.store().url, {
    preserveScroll: true,
    onSuccess: () => {
      // Optional: Show success toast/notification
      console.log('Response submitted successfully');
      toast.success('Response submitted successfully');
    },
    onError: (errors) => {
      // Optional: Show error toast/notification
      console.error('Submission errors:', errors);
    },
  });
};

const markAsCompleted = () => {
  router.patch(
    stepRoute.updateStatus({ task: props.step.data.task_id, step: props.step.data.id }).url,
    { status: 'completed' },
    {
      preserveScroll: true,
      onSuccess: () => {
        console.log('Step marked as completed');
      },
      onError: (errors) => {
        console.error('Failed to mark as completed:', errors);
      }
    }
  );
};

const isCreator = computed(() => {
  return auth.value.user.id === props.step.data.task.creator_id;
});
</script>

<template>

  <Head :title="props.step.data.title ?? 'Undefined'" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col flex-1 gap-4 p-4">
      <!-- Header Section -->
      <section class="flex items-start justify-between">

        <div>
          <h3 class="text-3xl font-bold">{{ props.step.data.title }}</h3>
          <p class="text-muted-foreground">{{ props.step.data.description ?? 'No description' }}</p>
        </div>

        <div class="flex gap-2 flex-col items-end mb-6">
          <!-- Assignment Info -->
          <div class="flex gap-2 items-center">
            <p class="text-muted-foreground text-sm">Assigned to:</p>
            <Button class="text-xs p-2 h-8" variant="outline">
              {{ props.step.data.assigned?.name ?? 'Anyone' }}
            </Button>
          </div>

          <!-- Action Buttons -->
          <div class="space-x-2">
            <PermissionGuard permission="can delete step">
              <StepDeleteDialog :step="props.step.data" />
            </PermissionGuard>

            <PermissionGuard permission="can edit step">
              <Button size="sm" @click="editStep(props.step.data.task_id, props.step.data.id)">
                Edit
              </Button>
            </PermissionGuard>

            <Button v-if="canMarkComplete && hasFields" size="sm" @click="markAsCompleted">
              Mark as Completed
            </Button>

            <Button v-if="canRevert" size="sm">Revert</Button>

            <Button v-if="canSubmitResponse && hasFields" @click="submitResponse" :disabled="form.processing">
              {{ form.processing ? 'Submitting...' : 'Submit Response' }}
            </Button>
          </div>
        </div>
      </section>

      <section>
        <!-- Main Content Grid -->
        <div class="lg:grid grid-cols-[3fr_2fr] flex flex-col gap-4" v-if="hasFields">
          <!-- Fields Section -->
          <section class="h-fit">
            <Fields :step="props.step.data" :form="form" />
          </section>

          <!-- Proof & Comments Section -->
          <section class="space-y-4">
            <Proof :step="props.step.data" />
            <Comment :step="props.step.data" />
          </section>
        </div>

        <div v-else>
          <!-- Empty State -->
          <section class="text-xs text-zinc-600 text-center mx-auto">
            <EmptyData :icon="FileQuestion" title="No fields yet" message="No fields configured for this step."
              :length="!hasFields" />
          </section>
        </div>
      </section>
    </div>

  </AppLayout>
</template>