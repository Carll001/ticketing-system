<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { Button } from '../ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '../ui/card';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { ScrollArea } from '../ui/scroll-area';
import { Textarea } from '../ui/textarea';
import { computed, ref } from 'vue';
import { Step } from '@/types';
import proof from '@/routes/proof';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '../ui/dialog';
import { Separator } from '../ui/separator';
import { ExternalLink, File, FileQuestion, Plus, X } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// FOR CURRENTLY AUTHENTICATED USER
const page = usePage();
const auth = computed(() => page.props.auth);

// FOR OPEN PROOF DIALOG
const openProofs = ref(false);

const fileInputRef = ref<HTMLInputElement | null>(null);
const attachmentPreviews = ref<{ file: File; preview?: string }[]>([]);

const props = defineProps<{
    step: Step
}>();

// Proof submission form with attachments
const proofForm = useForm({
    description: '',
    attachments: [] as File[]
});


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

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const submitProof = () => {
    // Update the route as needed for proof submission
    proofForm.post(proof.store({ task: props.step.task_id, step: props.step.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            // Clear form after successful submission
            proofForm.reset();
            attachmentPreviews.value = [];
            toast.success('Proof submitted successfully');
        },
    });
};

</script>
<template>
    <Card class="gap-2">
        <CardHeader>
            <CardTitle>
                <div class="flex justify-between items-center">
                    <p>{{ auth.user.id === props.step.task.creator_id || props.step.status === 'completed' ? 'Submitted Proofs: ' : 'Submit proof' }}
                    </p>
                    <Button variant="secondary" @click="openProofs = true" size="sm">View submitted proofs</Button>
                </div>
            </CardTitle>
        </CardHeader>
        <div v-if="props.step.status !== 'completed' || auth.user.id !== props.step.task.creator_id && props.step.status !== 'completed'">
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
                        <ScrollArea class="min-h-auto max-h-48 w-full">
                            <div class="space-y-2 max-h-40 p-2">
                                <div v-for="(item, index) in attachmentPreviews" :key="index"
                                    class="flex items-center gap-3 p-2 bg-zinc-900 rounded border border-zinc-800">
                                    <!-- Image preview -->
                                    <div v-if="item.preview" class="w-12 h-12 rounded overflow-hidden flex-shrink-0">
                                        <img :src="item.preview" :alt="item.file.name"
                                            class="w-full h-full object-cover" />
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
                                    <Button size="sm" variant="ghost"
                                        class="h-8 w-8 p-0 hover:bg-red-950 hover:text-red-400"
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
        </div>
    </Card>

    <Dialog v-model:open="openProofs">
        <DialogContent class="max-w-3xl max-h-[80vh]">
            <DialogHeader>
                <DialogTitle>Submitted Proofs ({{ props.step.proofs?.length || 0 }})</DialogTitle>
            </DialogHeader>

            <ScrollArea class="h-[60vh] pr-4">
                <div class="space-y-4">
                    <!-- Display proofs if they exist -->
                    <template v-if="props.step.proofs && props.step.proofs.length > 0">
                        <div v-for="(proofItem, index) in props.step.proofs" :key="proofItem.id"
                            class="p-4 bg-zinc-900/50 rounded-lg border border-zinc-800">
                            <!-- Proof Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-zinc-400">
                                        Proof #{{ index + 1 }}
                                    </p>
                                    <p class="text-[10px] text-zinc-500 mt-1">
                                        Submitted by <span class="font-medium text-zinc-400">{{ proofItem.user?.name ??
                                            'Unknown'
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
                                        <div v-else
                                            class="w-10 h-10 rounded bg-zinc-700 flex items-center justify-center flex-shrink-0">
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

                            <Separator v-if="index < props.step.proofs.length - 1" class="mt-4" />
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
</template>