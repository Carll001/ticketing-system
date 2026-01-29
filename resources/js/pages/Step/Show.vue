<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import stepLink from '@/routes/step';
import taskLink from '@/routes/task';
import { BreadcrumbItem, Step, Field } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, watch, ref } from 'vue';
import EmptyData from '@/components/EmptyData.vue';
import { FileQuestion, Plus, X, File, Image as ImageIcon } from 'lucide-vue-next';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import StepDeleteDialog from '@/components/task-step-components/StepDeleteDialog.vue';
import { Checkbox } from '@/components/ui/checkbox';
import response from '@/routes/response';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { ScrollArea } from '@/components/ui/scroll-area'
import proof from '@/routes/proof';
import step from '@/routes/step';
import comment from '@/routes/step/comment'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Separator } from '@/components/ui/separator';
import PermissionGuard from '@/components/PermissionGuard.vue';

const openProofs = ref(false);
const page = usePage();
const auth = computed(() => page.props.auth);
interface FormState {
  step_field_id: string | number;
  response: Record<string, any>;
}

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
    const existingValue = field.responses?.[0]?.response;

    if (field.type === 'Checkbox') {
      acc[field.id] = existingValue !== undefined ? existingValue : "0";
    } else {
      acc[field.id] = existingValue || '';
    }
    return acc;
  }, {} as Record<string, any>)
});

// Proof submission form with attachments
const proofForm = useForm({
  description: '',
  attachments: [] as File[]
});

const commentForm = useForm({
  content: ''
});


const fileInputRef = ref<HTMLInputElement | null>(null);
const attachmentPreviews = ref<{ file: File; preview?: string }[]>([]);

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
  form.post(response.store().url, {
    preserveScroll: true,
    onSuccess: () => {
      // Optional: clear or handle success
    },
  });
};

const handleAddAttachment = () => {
  fileInputRef.value?.click();
};

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const files = target.files;

  if (files) {
    const newFiles = Array.from(files);

    // Add files to the form
    proofForm.attachments.push(...newFiles);

    // Create previews for images
    newFiles.forEach(file => {
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
          attachmentPreviews.value.push({
            file,
            preview: e.target?.result as string
          });
        };
        reader.readAsDataURL(file);
      } else {
        attachmentPreviews.value.push({ file });
      }
    });

    // Clear input for re-selection
    target.value = '';
  }
};

const removeAttachment = (index: number) => {
  proofForm.attachments.splice(index, 1);
  attachmentPreviews.value.splice(index, 1);
};

const submitProof = () => {
  // Update the route as needed for proof submission
  proofForm.post(proof.store({ task: props.step.data.task_id, step: props.step.data.id }).url, {
    preserveScroll: true,
    onSuccess: () => {
      // Clear form after successful submission
      proofForm.reset();
      attachmentPreviews.value = [];
    },
  });
};

const submitComment = () => {
  commentForm.post(comment.store({ task: props.step.data.task_id, step: props.step.data.id }).url, {
    preserveScroll: true,
    onSuccess: () => {
      // Clear form after successful submission
      commentForm.reset();
    },
  });
};

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

// Check if all fields have responses
const allFieldsHaveResponses = computed(() => {
  const fields = props.step.data.fields || [];
  
  if (fields.length === 0) return false;
  
  return fields.every(field => {
    const response = form.response[field.id];
    
    // For checkboxes, both "true" and "false" are valid responses
    if (field.type === 'Checkbox') {
      return response === 'true' || response === 'false' || response === '0';
    }
    
    // For other fields, check if there's a non-empty value
    return response && response.toString().trim() !== '';
  });
});
</script>

<template>

  <Head :title="props.step.data.title ?? 'Undefined'" />
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
            <!-- <PermissionGuard permission="can delete step"> -->
            <StepDeleteDialog :step="props.step.data" />
            <!-- </PermissionGuard> -->
            <!-- <PermissionGuard permission="can edit step"> -->
              <Button size="sm" @click="editStep(props.step.data.task_id, props.step.data.id)">Edit</Button>
              <Button v-if="allFieldsHaveResponses">Mark as Completed</Button>
            <!-- </PermissionGuard> -->
            
            <Button @click="submitResponse" v-if="!allFieldsHaveResponses">Submit Response </Button>
          </div>
        </section>
      </div>

      <div class="grid grid-cols-[3fr_2fr] gap-4">
        <section class="h-fit">
          <Card>
            <CardHeader>
              <CardTitle>Fields</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
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
                          <Input v-if="type === 'Input'" v-model="form.response[field.id]" :readonly="auth.user.id === props.step.data.task.creator_id"
                            :placeholder="`Enter ${field.label.toLowerCase()}...`" class="h-8 text-xs bg-zinc-900/50" />

                          <Textarea v-else-if="type === 'Description'" v-model="form.response[field.id]" :readonly="auth.user.id === props.step.data.task.creator_id"
                            :placeholder="`Provide details for ${field.label.toLowerCase()}...`"
                            class="min-h-[60px] text-xs bg-zinc-900/50 resize-none" />

                          <div v-else-if="type === 'Checkbox'" class="flex items-center">
                            <Checkbox :id="field.id" :model-value="form.response[field.id] === 'true'" :disabled="auth.user.id === props.step.data.task.creator_id"
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
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-xs text-zinc-600 text-center mx-auto">
                <EmptyData :icon="FileQuestion" title="no fields yet" message="No fields configured for this step."
                  :length="props.step.data.fields?.length === 0" />
              </div>
            </CardContent>
          </Card>
          
        </section>

        <section class="space-y-4" >
          <Card class="gap-2" v-if="props.step.data.status === 'completed'">
            <CardHeader>
              <CardTitle>
                <div class="flex justify-between items-center">
                  <p>Submit proof</p>
                  <Button variant="secondary" @click="openProofs = true" size="sm">View submitted proofs</Button>
                </div>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <Textarea v-model="proofForm.description" placeholder="Enter proof description..."
                  class="bg-zinc-900/50 resize-none" />

                <!-- Hidden file input -->
                <input ref="fileInputRef" type="file" multiple accept="image/*,application/pdf,.doc,.docx,.txt"
                  class="hidden" @change="handleFileSelect" />

                <!-- Attachment previews -->
                <div v-if="attachmentPreviews.length > 0" class="space-y-2">
                  <Label class="text-xs text-zinc-400">Attachments ({{ attachmentPreviews.length }})</Label>
                  <ScrollArea class="h-48 w-full">
                    <div class="space-y-2">
                      <div v-for="(item, index) in attachmentPreviews" :key="index"
                        class="flex items-center gap-3 p-2 bg-zinc-900 rounded border border-zinc-800">
                        <!-- Image preview -->
                        <div v-if="item.preview" class="w-12 h-12 rounded overflow-hidden flex-shrink-0">
                          <img :src="item.preview" :alt="item.file.name" class="w-full h-full object-cover" />
                        </div>
                        <!-- File icon for non-images -->
                        <div v-else
                          class="w-12 h-12 rounded bg-zinc-800 flex items-center justify-center flex-shrink-0">
                          <File class="w-6 h-6 text-zinc-400" />
                        </div>

                        <!-- File info -->
                        <div class="flex-1 min-w-0">
                          <p class="text-xs text-zinc-300 truncate">{{ item.file.name }}</p>
                          <p class="text-[10px] text-zinc-500">{{ formatFileSize(item.file.size) }}</p>
                        </div>

                        <!-- Remove button -->
                        <Button size="sm" variant="ghost" class="h-8 w-8 p-0 hover:bg-red-950 hover:text-red-400"
                          @click="removeAttachment(index)">
                          <X class="w-4 h-4" />
                        </Button>
                      </div>
                    </div>
                  </ScrollArea>
                </div>

                <Button size="sm" variant="outline" @click="handleAddAttachment">
                  <Plus class="w-4 h-4" /> Add attachment
                </Button>
              </div>
            </CardContent>
            <CardFooter class="justify-end">
              <Button size="sm" @click="submitProof" :disabled="proofForm.processing">
                {{ proofForm.processing ? 'Submitting...' : 'Submit proof' }}
              </Button>
            </CardFooter>
          </Card>

          <Card>
            <form @submit.prevent="comment.store({ task: props.step.data.task_id, step: props.step.data.id }).url">
              <CardHeader>
                <CardTitle>Comments</CardTitle>
              </CardHeader>
              <CardContent>
                <div>
                  <ScrollArea class="min-h-20">

                    <div class="h-48 w-full">
                      <div class="space-y-2 pr-4">
                        <!-- Display actual comments if they exist -->
                        <template v-if="props.step.data.comments && props.step.data.comments.length > 0">
                          <div v-for="stepComment in [...props.step.data.comments].reverse()" :key="stepComment.id"
                            class="p-4 bg-zinc-900 rounded border border-zinc-800">
                            <p class="text-xs text-zinc-300">{{ stepComment.content }}</p>
                            <p class="text-[10px] text-zinc-500 mt-2">
                              by <span class="font-medium text-zinc-400">{{ stepComment.user?.name ?? 'Someone'
                                }}</span> on
                              {{ new Date(stepComment.created_at).toLocaleString() }}
                            </p>
                          </div>
                        </template>

                        <!-- Empty state -->
                        <div v-else class="text-center py-8">
                          <p class="text-xs text-zinc-500">No comments yet. Be the first to comment!</p>
                        </div>
                      </div>
                    </div>
                  </ScrollArea>
                </div>
              </CardContent>
              <CardFooter class="pt-4 w-full">
                <div class="space-y-2 flex flex-col w-full">
                  <Textarea placeholder="Add a comment..." v-model="commentForm.content"
                    class="bg-zinc-900/50 resize-none" />
                  <Button size="sm" class="ml-auto" @click="submitComment">Post Comment</Button>
                </div>
              </CardFooter>
            </form>
          </Card>
        </section>
      </div>
    </div>

    <!-- Proofs Dialog -->
    <Dialog v-model:open="openProofs">
      <DialogContent class="max-w-3xl max-h-[80vh]">
        <DialogHeader>
          <DialogTitle>Submitted Proofs ({{ props.step.data.proofs?.length || 0 }})</DialogTitle>
        </DialogHeader>

        <ScrollArea class="h-[60vh] pr-4">
          <div class="space-y-4">
            <!-- Display proofs if they exist -->
            <template v-if="props.step.data.proofs && props.step.data.proofs.length > 0">
              <div v-for="(proofItem, index) in props.step.data.proofs" :key="proofItem.id"
                class="p-4 bg-zinc-900/50 rounded-lg border border-zinc-800">
                <!-- Proof Header -->
                <div class="flex items-start justify-between mb-3">
                  <div class="flex-1">
                    <p class="text-xs font-medium text-zinc-400">
                      Proof #{{ index + 1 }}
                    </p>
                    <p class="text-[10px] text-zinc-500 mt-1">
                      Submitted by <span class="font-medium text-zinc-400">{{ proofItem.user?.name ?? 'Unknown'
                        }}</span>
                    </p>
                    <p class="text-[10px] text-zinc-500">
                      {{ new Date(proofItem.created_at).toLocaleString() }}
                    </p>
                  </div>
                </div>

                <!-- Proof Description -->
                <div v-if="proofItem.description" class="mb-3">
                  <Label class="text-xs text-zinc-400 mb-1 block">Description</Label>
                  <p class="text-sm text-zinc-300 bg-zinc-800/50 p-3 rounded">
                    {{ proofItem.description }}
                  </p>
                </div>

                <!-- Attachments -->
                <div v-if="proofItem.attachments && proofItem.attachments.length > 0" class="mt-3">
                  <Label class="text-xs text-zinc-400 mb-2 block">
                    Attachments ({{ proofItem.attachments.length }})
                  </Label>
                  <div class="grid grid-cols-2 gap-2">
                    <a v-for="attachment in proofItem.attachments" :key="attachment.id"
                      :href="`/storage/${attachment.path}`" target="_blank"
                      class="flex items-center gap-2 p-2 bg-zinc-800/50 rounded border border-zinc-700 hover:border-zinc-600 hover:bg-zinc-800 transition-colors">
                      <!-- Image preview for images -->
                      <div v-if="attachment.mime?.startsWith('image/')"
                        class="w-10 h-10 rounded overflow-hidden flex-shrink-0">
                        <img :src="`/storage/${attachment.path}`" :alt="attachment.original_name"
                          class="w-full h-full object-cover" />
                      </div>
                      <!-- File icon for non-images -->
                      <div v-else class="w-10 h-10 rounded bg-zinc-700 flex items-center justify-center flex-shrink-0">
                        <File class="w-5 h-5 text-zinc-400" />
                      </div>

                      <!-- File info -->
                      <div class="flex-1 min-w-0">
                        <p class="text-xs text-zinc-300 truncate">{{ attachment.original_name }}</p>
                        <p class="text-[10px] text-zinc-500">
                          {{ formatFileSize(attachment.size || 0) }}
                        </p>
                      </div>

                      <!-- Download icon -->
                      <ExternalLink class="w-4 h-4 text-zinc-500 flex-shrink-0" />
                    </a>
                  </div>
                </div>

                <Separator v-if="index < props.step.data.proofs.length - 1" class="mt-4" />
              </div>
            </template>

            <!-- Empty state -->
            <div v-else class="text-center py-12">
              <FileQuestion class="w-12 h-12 text-zinc-600 mx-auto mb-3" />
              <p class="text-sm text-zinc-500">No proofs submitted yet</p>
              <p class="text-xs text-zinc-600 mt-1">Submit your first proof above</p>
            </div>
          </div>
        </ScrollArea>
      </DialogContent>
    </Dialog>
  </AppLayout>


</template>