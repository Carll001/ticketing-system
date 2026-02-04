<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import stepLink from '@/routes/step';
import taskLink from '@/routes/task';
import { BreadcrumbItem, Step, User } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import StepDeleteDialog from '@/components/task-step-components/StepDeleteDialog.vue';
import response from '@/routes/response';
import stepRoute from '@/routes/step';
import PermissionGuard from '@/components/PermissionGuard.vue';
import Proof from '@/components/task-step-components/Proof.vue';
import Comment from '@/components/task-step-components/Comment.vue';
import Fields from '@/components/task-step-components/Field.vue';
import { toast } from 'vue-sonner';
import EmptyData from '@/components/EmptyData.vue';
import { Ellipsis, FileQuestion } from 'lucide-vue-next';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { getInitials } from '@/composables/useInitials';
import { ScrollArea } from '@/components/ui/scroll-area';
const page = usePage();
const auth = computed(() => page.props.auth);
const showDeleteDialog = ref(false);
const showReassign = ref(false);
const showRejectedReason = ref(false);
const props = defineProps<{
  step: { data: Step }
  users: User[]
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
const deleteStep = () => {
  router.delete(stepLink.delete({ task: props.step.data.task_id, step: props.step.data.id }).url, {
    preserveScroll: true,
  })
}
// Initialize form with existing responses
const form = useForm<{
  step_field_id: string | number;
  response: Record<string, any>;
  cost?: number | null;
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
  }, {} as Record<string, any>),
  cost: props.step.data.cost || null,
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

// Permission checks for step actions
const canEditStep = computed(() => {
  const user = auth.value.user;

  // Superadmin can do everything
  if (user.role === 'superadmin') return true;

  // Creator can edit their steps if they have permission
  return isCreator.value && user.can?.includes('can edit task');
});

const canDeleteStep = computed(() => {
  const user = auth.value.user;

  // Superadmin can do everything
  if (user.role === 'superadmin') return true;

  // Creator can delete their steps if they have permission and step is not completed
  return isCreator.value &&
    user.can?.includes('can delete task') &&
    ['pending', 'assigned', 'accepted'].includes(props.step.data.status);
});

const canSeeStepActions = computed(() => {
  return canEditStep.value || canDeleteStep.value;
});

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

// Reassign form - allow null for "anyone"
const reassignForm = useForm({
  user_id: null as string | null,
  status: 'assigned',
});

const assignUser = () => {

}

const reassignStep = () => {
  reassignForm.patch(
    stepRoute.reassign({ task: props.step.data.task_id, step: props.step.data.id }).url,
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Step reassigned successfully');
        showReassign.value = false;
        reassignForm.reset();
      },
      onError: (errors) => {
        console.error('Failed to reassign:', errors);
        toast.error('Failed to reassign step');
      }
    }
  );
};

const isCreator = computed(() => {
  return auth.value.user.id === props.step.data.task.creator_id;
});

// Get available users for reassignment
const availableUsers = computed(() => {
  // You'll need to pass this from the controller as props.users
  return props.users || [];
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

        <div class="flex gap-2 flex-col items-end">
          <DropdownMenu v-if="canSeeStepActions">
            <DropdownMenuTrigger as-child>
              <Button variant="ghost" size="icon-sm">
                <Ellipsis />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <PermissionGuard permission="can edit task">
                <DropdownMenuItem @click="editStep(props.step.data.task_id, props.step.data.id)">Edit Task
                </DropdownMenuItem>
              </PermissionGuard>
              <PermissionGuard permission="can delete task">
                <DropdownMenuItem class="text-destructive focus:text-destructive" @click="showDeleteDialog = true">
                  Delete
                </DropdownMenuItem>
              </PermissionGuard>
            </DropdownMenuContent>
          </DropdownMenu>
          <!-- Assignment Info -->
          <div class="flex gap-2 items-center">
            <p class="text-muted-foreground text-sm">Assigned to:</p>
            <Button class="text-xs p-2 h-8" variant="outline">
              {{ props.step.data.assigned?.name ?? 'Anyone' }}
            </Button>
          </div>

          <!-- Action Buttons -->
          <div class="space-x-2">
            <Button v-if="props.step.data.status === 'rejected' && props.step.data.task.creator_id === auth.user.id"
              @click="showReassign = true" size="sm">Reassign</Button>
            <Button v-if="canMarkComplete && hasFields && props.step.data.status !== 'rejected'" size="sm" @click="markAsCompleted">
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
        <div class="lg:grid grid-cols-[3fr_2fr] flex flex-col gap-4"
          v-if="hasFields && props.step.data.status !== 'rejected'">
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
            <EmptyData v-if="props.step.data.status !== 'rejected'" :icon="FileQuestion" title="No fields yet"
              message="No fields configured for this step." :length="!hasFields" />

            <div v-else>
              <EmptyData :icon="FileQuestion" title="Rejected" message="Please reassigned."
                :length="props.step.data.status === 'rejected'" />
              <Button @click="showRejectedReason = true">Show Rejected Reason</Button>
            </div>
          </section>
        </div>
      </section>
      <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Are you absolutely sure?</DialogTitle>
            <DialogDescription>
              This action cannot be undone. This will permanently delete your account
              and remove your data from our servers.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <DialogClose as-child>
              <Button variant="ghost">Close</Button>
            </DialogClose>
            <Button @click="deleteStep">Delete</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <Dialog v-model:open="showReassign">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Reassign Step</DialogTitle>
            <DialogDescription>
              Select a user to reassign this rejected step to, or assign to "Anyone".
            </DialogDescription>
          </DialogHeader>

          <div class="space-y-4 py-4">
            <div class="space-y-2">
              <Label for="user-select">Assign to User</Label>
              <Select v-model="reassignForm.user_id">
                <SelectTrigger id="user-select" class="w-full">
                  <SelectValue placeholder="Select a user or anyone" class="w-full" />
                </SelectTrigger>
                <SelectContent>
                  <!-- Anyone Option -->
                  <SelectItem :value="null">
                    Anyone (Available to all users)
                  </SelectItem>

                  <!-- Specific Users -->
                  <SelectItem v-for="user in availableUsers" :key="user.id" :value="user.id">
                    {{ user.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <DialogFooter>
            <DialogClose as-child>
              <Button variant="ghost">Cancel</Button>
            </DialogClose>
            <Button @click="reassignStep" :disabled="reassignForm.processing">
              {{ reassignForm.processing ? 'Reassigning...' : 'Reassign' }}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <Dialog v-model:open="showRejectedReason">
        <DialogContent class="max-w-2xl">
          <DialogHeader>
            <DialogTitle>Rejection History</DialogTitle>
            <DialogDescription>
              {{ props.step.data.rejections?.length || 0 }} rejection{{ props.step.data.rejections?.length !== 1 ? 's' :
                '' }} for this step
            </DialogDescription>
          </DialogHeader>

          <ScrollArea class="h-[400px]">
            <div class="space-y-3 pr-4">
              <div v-for="(rejection, index) in props.step.data.rejections" :key="rejection.id" :class="[
                'p-4 rounded-lg border transition-colors hover:bg-accent/50',
                index === 0
                  ? 'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-900/30'
                  : 'bg-muted/30'
              ]">
                <!-- Header -->
                <div class="flex items-start justify-between mb-3">
                  <div class="flex items-center gap-3">
                    <!-- Avatar -->
                    <div :class="[
                      'w-9 h-9 rounded-full flex items-center justify-center text-xs font-semibold',
                      index === 0 ? 'bg-red-500 text-white' : 'bg-muted text-muted-foreground'
                    ]">
                      {{ (rejection.rejected_by?.name || 'U').charAt(0).toUpperCase() }}
                    </div>

                    <div>
                      <div class="flex items-center gap-2">
                        <p class="font-medium text-sm">
                          {{ rejection.rejected_by?.name || 'Unknown User' }}
                        </p>
                        <span v-if="index === 0" class="px-2 py-0.5 bg-red-500 text-white text-[10px] rounded-full">
                          Latest
                        </span>
                      </div>
                      <p class="text-xs text-muted-foreground mt-0.5">
                        {{ new Date(rejection.created_at).toLocaleString() }}
                      </p>
                    </div>
                  </div>

                  <!-- <div class="text-[10px] text-muted-foreground">
                    {{ getTimeAgo(rejection.created_at) }}
                  </div> -->
                </div>

                <!-- Reason -->
                <div class="text-sm text-foreground/90 leading-relaxed pl-12 border-l-2 border-red-400/30">
                  {{ rejection.reason }}
                </div>
              </div>
            </div>
          </ScrollArea>

          <DialogFooter class="border-t pt-4">
            <DialogClose as-child>
              <Button variant="ghost">Close</Button>
            </DialogClose>
            <!-- <Button v-if="canReassign" @click="showReassignDialog = true; showRejectedReason = false">
              Reassign Step
            </Button> -->
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>

  </AppLayout>
</template>