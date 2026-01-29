<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import stepLink from '@/routes/step';
import taskLink from '@/routes/task';
import proofLink from '@/routes/proof';
import { BreadcrumbItem, Step, Field } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import EmptyData from '@/components/EmptyData.vue';
import StepDeleteDialog from '@/components/task-step-components/StepDeleteDialog.vue';
import { FileQuestion, Plus } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const viewProofs = ref(false);
const viewComments = ref(false);

interface FormState {
  step_field_id: string | number;
  response: Record<string, any>;
}

const props = defineProps<{ step: { data: Step } }>();

// -----------------------------
// STEP COMMENTS (with useForm)
// -----------------------------
interface StepComment {
  id: number;
  content: string;
  created_at: string;
  user: {
    name: string;
  };
}

const stepComments = ref<StepComment[]>(props.step.data.comments ?? []);

const commentForm = useForm({
  content: '',
});

const addStepComment = () => {
  if (!commentForm.content.trim()) return;

  commentForm.post(
    `/task/${props.step.data.task_id}/step/${props.step.data.id}/comment`,
    {
      preserveScroll: true,
      onSuccess: () => {
        stepComments.value = props.step.data.comments ?? [];
        commentForm.reset();
        toast.success('Step comment added successfully!');
      },
    }
  );
};

// -----------------------------
// PROOFS STATE
// -----------------------------
const proofs = ref<any[]>(props.step.data.proofs ?? []);

watch(
  () => props.step.data.proofs,
  (val) => {
    if (val) proofs.value = val;
  },
  { immediate: true }
);

// -----------------------------
// BREADCRUMBS
// -----------------------------
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Task', href: taskLink.index().url },
  { title: props.step.data.task.title, href: taskLink.show(props.step.data.task_id).url },
  { title: props.step.data.title, href: stepLink.show({ task: props.step.data.task_id, step: props.step.data.id }).url },
];

// -----------------------------
// STEP FIELDS
// -----------------------------
const groupedFields = computed(() => {
  const fields = props.step.data.fields || [];
  return fields.reduce((acc, field) => {
    const type = field.type;
    if (!acc[type]) acc[type] = [];
    acc[type].push(field);
    return acc;
  }, {} as Record<string, Field[]>);
});

const form = useForm<FormState>({
  step_field_id: props.step.data.id,
  response: (props.step.data.fields || []).reduce((acc, field) => {
    const existingValue = field.responses?.[0]?.response;
    if (field.type === 'Checkbox') acc[field.id] = existingValue ?? 'false';
    else acc[field.id] = existingValue || '';
    return acc;
  }, {} as Record<string, any>)
});

watch(() => props.step.data.fields, (newFields) => {
  newFields?.forEach(field => {
    const freshValue = field.responses?.[0]?.response;
    if (freshValue !== undefined) form.response[field.id] = freshValue;
  });
}, { deep: true });

const editStep = (task_id: string, step_id: string) => {
  router.visit(stepLink.edit({ task: task_id, step: step_id }).url, { preserveScroll: true });
};

// -----------------------------
// PROOF FORM (with useForm)
// -----------------------------
const proofForm = useForm({
  description: '',
  attachments: [] as File[],
});

const fileInputRef = ref<HTMLInputElement | null>(null);

const triggerFileInput = () => fileInputRef.value?.click();

const handleFiles = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    proofForm.attachments.push(...Array.from(target.files));
    target.value = '';
  }
};

const removeAttachment = (index: number) => {
  proofForm.attachments.splice(index, 1);
};

const attachmentPreviews = computed(() =>
  proofForm.attachments.map(file => ({
    file,
    url: file.type.startsWith('image/') ? URL.createObjectURL(file) : null
  }))
);

const editProof = (proof: any) => {
  router.visit(`/task/${props.step.data.task_id}/step/${props.step.data.id}/proof/${proof.id}/edit`);
};

const deleteProof = (proofId: string) => {
  const taskId = props.step.data.task_id;
  const stepId = props.step.data.id;

  if (!taskId || !stepId) {
    console.error('Missing task or step ID!');
    return;
  }

  router.delete(`/task/${taskId}/step/${stepId}/proof/${proofId}`, {
    preserveScroll: true,
    onSuccess: () => {
      proofs.value = proofs.value.filter(p => p.id !== proofId);
      toast.success('Proof deleted successfully');
    },
  });
};

// -----------------------------
// SUBMIT FUNCTION
// -----------------------------
const submitAll = () => {
  const formData = new FormData();

  formData.append('step_field_id', String(props.step.data.id));
  Object.keys(form.response).forEach(key => {
    formData.append(`response[${key}]`, form.response[key]);
  });

  formData.append('description', proofForm.description);
  proofForm.attachments.forEach((file, i) => {
    formData.append(`attachments[${i}]`, file);
  });

  router.post(
    proofLink.store({ task: props.step.data.task_id, step: props.step.data.id }).url,
    formData,
    {
      preserveScroll: true,
      forceFormData: true,
      onSuccess: (page) => {
        // Reset proof form
        proofForm.reset();

        // Update proofs from backend
        const updatedStep = page.props.step as Step;
        proofs.value = updatedStep.proofs ?? [];
        toast.success('Proof submitted successfully!');
      },
    }
  );
};

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { MoreVertical } from 'lucide-vue-next'

</script>


<template>

  <Head :title="step.data.title ?? 'Undefined'" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col flex-1 gap-4 p-4">

      <!-- Step Header -->
      <div class="flex items-start justify-between">
        <section>
          <h3 class="text-3xl font-bold">{{ props.step.data.title }}</h3>
          <p class="text-muted-foreground">{{ props.step.data.description ?? 'No description' }}</p>
        </section>
        <section class="flex gap-2 flex-col items-end mb-6">
          <div class="flex gap-2 items-center">
            <p class="text-muted-foreground text-sm">Assigned to:</p>
            <Button class="text-xs p-2 h-8" variant="outline">{{ props.step.data.assigned?.name ?? 'Anyone' }}</Button>
          </div>
          <div class="space-x-2">
            <Button @click="submitAll" :disabled="form.processing">
              {{ form.processing ? 'Submitting...' : 'Submit Response' }}
            </Button>
            <div>
              <DropdownMenu>
  <DropdownMenuTrigger as-child>
    <Button variant="ghost" size="icon">
      <MoreVertical class="w-4 h-4" />
    </Button>
  </DropdownMenuTrigger>

  <DropdownMenuContent align="end" class="w-44">
    <DropdownMenuItem
      @click="editStep(props.step.data.task_id, props.step.data.id)"
    >
      Edit
    </DropdownMenuItem>
    <DropdownMenuItem class="text-destructive focus:text-destructive">
      <StepDeleteDialog :step="props.step.data" />
    </DropdownMenuItem>
  </DropdownMenuContent>
</DropdownMenu>
            </div>
          </div>
        </section>
      </div>

      <!-- Step Fields -->
      <div class="space-y-8">
        <div v-for="(fields, type) in groupedFields" :key="type" class="space-y-4">
          <Label
            class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800 pb-1 block">
            {{ type }}{{ type === 'Checkbox' ? 'es' : 's' }}
          </Label>
          <div class="space-y-4 pl-2">
            <div v-for="field in fields" :key="field.id" class="space-y-4">
              <div class="flex gap-3" :class="type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start'">
                <div :class="[type === 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                  <Input v-if="type === 'Input'" v-model="form.response[field.id]"
                    :placeholder="`Enter ${field.label.toLowerCase()}...`" class="h-8 text-xs bg-zinc-900/50" />
                  <Textarea v-else-if="type === 'Description'" v-model="form.response[field.id]"
                    :placeholder="`Provide details for ${field.label.toLowerCase()}...`"
                    class="min-h-[60px] text-xs bg-zinc-900/50 resize-none" />
                  <div v-else-if="type === 'Checkbox'" class="flex items-center">
                    <Checkbox :id="field.id" :model-value="form.response[field.id] === 'true'"
                      @update:model-value="(val) => form.response[field.id] = val ? 'true' : 'false'" />
                  </div>
                </div>
                <Label :class="['text-xs font-medium text-zinc-300', type === 'Checkbox' ? 'order-2' : 'order-1']">
                  {{ field.label }}
                </Label>
              </div>
            </div>
          </div>
        </div>

        <div class="text-xs text-zinc-600 text-center mx-auto">
          <EmptyData :icon="FileQuestion" title="no fields yet" message="No fields configured for this step."
            :length="props.step.data.fields?.length === 0" />
        </div>


        <Card>
          <CardHeader>
            <CardTitle>Proofs</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-2">
              <Label class="text-xs font-bold uppercase">Add New Proof</Label>
              <Textarea v-model="proofForm.description" placeholder="Enter proof description..."
                class="min-h-[50px] text-xs bg-zinc-900/50 resize-none" />


              <div v-if="attachmentPreviews.length" class="flex flex-wrap gap-2 mt-2">
                <div v-for="(item, idx) in attachmentPreviews" :key="idx"
                  class="relative w-20 h-20 border border-zinc-700 rounded overflow-hidden bg-zinc-900 group">
                  <img v-if="item.url" :src="item.url" class="w-full h-full object-cover" />
                  <p v-else class="text-[10px] text-center p-1 break-words">{{ item.file.name }}</p>
                  <button @click="removeAttachment(idx)"
                    class="absolute top-1 right-1 w-5 h-5 bg-black/50 text-white rounded-full flex items-center justify-center text-xs"
                    type="button">×</button>
                </div>
              </div>

              <div class="flex gap-2 mt-2">
                <Button size="sm" variant="ghost" @click="triggerFileInput">
                  <Plus class="w-3 h-3 mr-1 inline" /> Add Attachments
                </Button>
                <input type="file" multiple ref="fileInputRef" class="hidden" @change="handleFiles" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="p-3 gap-0">
          <CardHeader class="p-3">
            <CardTitle>Proof / Comment</CardTitle>
            <CardDescription>Proof / Comment</CardDescription>
          </CardHeader>
          <CardFooter class="p-3 space-x-2">
            <Button variant="outline" @click="viewProofs = true" size="sm">Proofs</Button>
            <Button variant="outline" @click="viewComments = true" size="sm">Comments</Button>
          </CardFooter>
        </Card>
      </div>
    </div>

    <!-- Submitted Proofs Dialog -->
    <Dialog v-model:open="viewProofs">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Submitted Proofs</DialogTitle>
          <DialogDescription>View all submitted proofs</DialogDescription>
        </DialogHeader>

        <div v-if="proofs.length" class="mt-6 space-y-4">
          <div v-for="proof in proofs" :key="proof.id"
            class="bg-zinc-950 border border-zinc-800 rounded-lg p-4 space-y-3">
            <p class="text-xs text-zinc-300">
              {{ proof.description || 'No description provided.' }}
            </p>

            <div class="flex flex-wrap gap-2">
              <div v-for="(file, i) in proof.attachments || []" :key="i"
                class="w-24 h-24 rounded overflow-hidden border border-zinc-800">
                <img :src="file.url" class="w-full h-full object-cover" />
              </div>
            </div>

            <div class="flex justify-end gap-2">
              <Button size="sm" variant="outline" @click="editProof(proof)">Edit</Button>
              <Button size="sm" variant="destructive" @click="deleteProof(proof.id)">Delete</Button>
            </div>
          </div>
        </div>

        <p v-else class="text-xs text-zinc-600 text-center py-4">No proofs submitted yet.</p>
      </DialogContent>
    </Dialog>

    <!-- Comments Dialog -->
    <Dialog v-model:open="viewComments">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Comments</DialogTitle>
          <DialogDescription>Step comments and discussions</DialogDescription>
        </DialogHeader>

        <!-- Comment list -->
        <div v-if="stepComments.length" class="space-y-3 max-h-96 overflow-y-auto">
          <div v-for="comment in stepComments" :key="comment.id"
            class="bg-zinc-900 border border-zinc-800 rounded p-3 space-y-1">
            <div class="flex justify-between text-xs text-zinc-500">
              <span class="font-medium truncate max-w-[120px]">{{ comment.user.name }}</span>
              <span>{{ new Date(comment.created_at).toLocaleString() }}</span>
            </div>
            <p class="text-sm text-zinc-200">
              {{ comment.content }}
            </p>
          </div>
        </div>

        <p v-else class="text-xs text-zinc-600 text-center py-4">No comments yet.</p>

        <!-- Add comment -->
        <div class="space-y-2 pt-2 border-t border-zinc-800">
          <Textarea v-model="commentForm.content" placeholder="Write a comment..."
            class="min-h-[60px] text-xs bg-zinc-900/50 resize-none" />

          <div class="flex justify-end">
            <Button size="sm" @click="addStepComment" :disabled="commentForm.processing">
              {{ commentForm.processing ? 'Posting...' : 'Post Comment' }}
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>

    <pre>{{ form }}</pre>
  </AppLayout>
</template>